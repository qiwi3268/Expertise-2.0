<?php


namespace App\Lib\Csp\Validation;

use LogicException;
use App\Exceptions\Lib\Csp\CspParsingException;
use App\Exceptions\Lib\Csp\CspTechnicalException;
use App\Exceptions\Lib\Csp\CspHandledException;
use App\Exceptions\Lib\Csp\CspValidationLogicException;
use App\Exceptions\Lib\Csp\CspInvalidArgumentException;

use Symfony\Component\Process\Process;
use App\Lib\Stream\StreamPartsMaker;

use App\Lib\Csp\Validation\Commands\SignatureValidationCommander;
use App\Lib\ValueObjects\Fio;
use App\Lib\Csp\Validation\ValueObjects\Private\PrivateValidationResult;
use App\Lib\Csp\Validation\ValueObjects\Private\PrivateSigner;
use App\Lib\Csp\Validation\ValueObjects\Public\ValidationResult;
use App\Lib\Csp\Validation\ValueObjects\Public\Signer;
use App\Lib\Csp\Certification\CertificationMessageParser;
use App\Lib\Csp\Certification\ValueObjects\Certificate;
use App\Lib\Csp\Certification\Commands\CertificateInfoCommander;


/**
 * Класс проверки ЭЦП
 *
 */
final class SignatureValidator
{

    /**
     * Индексный массив из двух элементов:
     *
     * - 1 - код ошибки при проверке с цепочкой сертификатов
     * - 2 - код ошибки при проверке без цепочки сертификатов
     *
     */
    private array $errorCodes = [];
    
    private ValidationMessageParser $sign_parser;


    /**
     * Конструктор класса
     *
     * @param SignatureValidationCommander $sign_commander
     */
    public function __construct(private SignatureValidationCommander $sign_commander)
    {
        $this->sign_parser = new ValidationMessageParser;
    }


    /**
     * Возвращает результат проверки ЭЦП
     *
     * @return ValidationResult
     * @throws CspParsingException
     * @throws CspValidationLogicException
     * @throws CspTechnicalException
     * @throws CspHandledException
     * @throws CspInvalidArgumentException
     */
    public function validate(): ValidationResult
    {
        // c (chain) - с проверкой цепочки сертификатов
        // n (no chain) - без проверки цепочки сертификатов

        // Инициализация процессов
        $cert_commander = new CertificateInfoCommander($this->sign_commander->getCryptographicFilePath());
        $cert_process = new Process($cert_commander->getCommand());
        $c_process = new Process($this->sign_commander->getChainCommand());
        $n_process = new Process($this->sign_commander->getNoChainCommand());

        // Обработчики сформированных частей сообщения
        $c_partsMaker = new StreamPartsMaker([new MessagePartsHandler, 'handle']);
        $n_partsMaker = new StreamPartsMaker([new MessagePartsHandler, 'handle']);

        // Запуск проверок
        $cert_process->start();
        $c_process->start([$c_partsMaker, 'processChunk']);
        $n_process->start([$n_partsMaker, 'processChunk']);

        // Ожидание завершения процессов проверки
        $c_process->wait();
        $c_validationResult = $this->getValidationResult($c_partsMaker->getParts());

        $n_process->wait();
        $n_validationResult = $this->getValidationResult($n_partsMaker->getParts());

        $cert_process->wait();
        $cert_parser = new CertificationMessageParser($cert_process->getOutput());
        $certificates = $cert_parser->getCertificates();


        // Разбор всех сообщений завершился успешно
        //
        $result = new ValidationResult;

        foreach ($c_validationResult->getSigners() as $c_signer) {

            $certificate = $this->getSignerCertificate($c_signer->fio, $certificates);

            // Результат с проверкой цепочки сертификатов валидный
            // или
            // Ошибка, которую не исправить проверкой без цепочки сертификатов
            if (
                $c_signer->result
                || str_contains_any($c_signer->message, ['Error: Invalid Signature.', 'Error: Invalid algorithm specified.'])
            ) {
                $signer = new Signer(
                    $c_signer->fio,
                    $c_signer->result,
                    $this->getSignatureUserMessage($c_signer->message),
                    $certificate,
                    $c_signer->result,
                    $this->getCertificateUserMessage($c_signer->message)
                );
            } else {

                // Получаем результат проверки для текущего подписанта без проверки цепочки сертификатов
                $n_signer = $n_validationResult->getSignerByIndex($c_signer->index);

                $signer = new Signer(
                    $c_signer->fio,
                    $n_signer->result,
                    $this->getSignatureUserMessage($n_signer->message),
                    $certificate,
                    // Результат проверки сертификата остается от проверки с цепочкой сертификата
                    $c_signer->result,
                    $this->getCertificateUserMessage($c_signer->message)
                );
            }
            $result->addSigner($signer);
            unset($signer);
        }
        return $result;
    }


    /**
     * Возвращает сформированный объект результата проверки
     *
     * @param array $parts
     * @return PrivateValidationResult
     * @throws CspValidationLogicException
     * @throws CspTechnicalException
     * @throws CspHandledException
     * @throws CspParsingException
     */
    private function getValidationResult(array $parts): PrivateValidationResult
    {
        // Переиндексация входного массива
        $parts = array_values($parts);
        $errorCodes = [];

        $validationResult = new PrivateValidationResult;

        for ($l = 0; $l < count($parts); $l++) {

            $part = $parts[$l];

            if (str_contains($part, 'Signer:')) {

                $signer = new PrivateSigner;

                $signer->fio = $this->sign_parser->getFio($part);

                $next_1_part = $parts[$l + 1];
                $next_2_part = $parts[$l + 2];

                if ($next_1_part == "Signature's verified." && $next_2_part == 'Error: Signature.') {
                    // Текущая подпись валидна, но общий результат с ошибкой из-за предыдущего подписанта
                    $signer->result = true;
                    // Перескакиваем через Signature's verified. и Error: Signature.
                    $l += 2;
                } elseif ($next_1_part == "Signature's verified.") {

                    $signer->result = true;
                    // Перескакиваем через Signature's verified.
                    $l += 1;
                } elseif ($next_2_part == 'Error: Signature.') {

                    $signer->result = false;
                    // Перескакиваем через сообщение об ошибке и Error: Signature.
                    $l += 2;
                } elseif (str_contains($next_2_part, 'Signer:')) {

                    $signer->result = false;
                    // Перескакиваем через сообщение об ошибке и переходим к следующему подписанту
                    $l += 1;
                } else {

                    throw new CspValidationLogicException('Неизвестный формат частей сообщения, следующий за Signer');
                }
                $signer->message = $next_1_part;
                $validationResult->addSigner($signer);
            } elseif (str_contains($part, 'ErrorCode:')) {

                $errorCodes[] = $this->sign_parser->getErrorCode($part);
            } elseif (str_contains_any($part, [
                'Error: Invalid cryptographic message type.',
                'Error: The parameter is incorrect.',
                'Error: The streamed cryptographic message is not ready to return data.',
                "Error: Can't open file",
                'Unknown error.'
            ])) {

                continue; // Ошибки пропускаем, т.к. дальше (в следующих итерациях) отловится в ErrorCode
            } elseif (str_contains_any($part, [
                'Error: Certificate chain is not checked for this certificate (chain status 0x10000)',
                'si_init (int_ubi_mutex_open "registry_lock") failed:: Permission denied'
            ])) {
                // Блок технических ошибок, которые требуют вмешательства разработчика
                throw new CspTechnicalException("Возникла техническая ошибка: '{$part}'");
            } else {

                throw new CspValidationLogicException("Неизвестная часть сообщения: '{$part}'");
            }
        }

        $count = count($errorCodes);
        if ($count != 1) {
            throw new CspValidationLogicException("Получено некорректное количество блоков ErrorCode: {$count}");
        }
        $this->errorCodes[] = $errorCodes[0];

        if ($validationResult->isSignersEmpty()) {
            throw new CspHandledException('В сообщении отсутствуют подписанты');
        }
        return $validationResult;
    }


    /**
     * Возвращает объект сертификата по фио его владельца
     *
     * @param Fio $fio
     * @param Certificate[] $certificates
     * @return Certificate
     * @throws CspValidationLogicException
     */
    private function getSignerCertificate(Fio $fio, array $certificates): Certificate
    {
        $result = [];

        $fio = $fio->getLongFio();

        foreach ($certificates as $certificate) {

            $subject = $certificate->getSubject();

            // Сертификат человека, а не удостоверяющего центра
            if (
                isset($subject['SN'])
                && (isset($subject['GN']) || isset($subject['G']))
            ) {

                $f  = $subject['SN'];
                $io = $subject['GN'] ?? $subject['G'];

                if ($fio == "{$f} {$io}") {
                    $result[] = $certificate;
                }
            }
        }
        $count = count($result);

        if ($count != 1) {
            throw new CspValidationLogicException("По фио подписанта: '{$fio}' найдено: {$count} сертификата(ов)");
        }
        return $result[0];
    }


    /**
     * Возвращает пользовательское сообщение на основе результата проверки подписи
     *
     * @param string $message
     * @return string
     * @throws CspValidationLogicException
     */
    private function getSignatureUserMessage(string $message): string
    {
        switch ($message) {
            case "Signature's verified." :
                return 'Подпись действительна';

            case 'Error: Invalid algorithm specified.' :
                return 'Подпись имеет недействительный алгоритм';

            case 'Error: Invalid Signature.' :
                return 'Подпись не соответствует файлу';

            default :
                throw new CspValidationLogicException("Получен неизвестный результат проверки подписи: '{$message}'");
        }
    }


    /**
     * Возвращает пользовательское сообщение на основе результата проверки подписи (сертификата)
     *
     * @param string $message
     * @return string
     * @throws CspValidationLogicException
     */
    private function getCertificateUserMessage(string $message): string
    {
        switch ($message) {
            case "Signature's verified." :
                return 'Сертификат действителен';

            case 'This certificate or one of the certificates in the certificate chain is not time valid.' :
                return 'Срок действия одного из сертификатов цепочки истек или еще не наступил';

            case 'Trust for this certificate or one of the certificates in the certificate chain has been revoked.' :
                return 'Один из сертификатов цепочки аннулирован';

            case 'Error: Invalid algorithm specified.' :
            case 'Error: Invalid Signature.' :
                return 'Сертификат не проверялся';

            default :
                throw new CspValidationLogicException("Получен неизвестный результат проверки сертификата: '{$message}'");
        }
    }


    /**
     * Возвращает последний код ошибки
     *
     * @return string
     * @throws LogicException
     */
    public function getLastErrorCode(): string
    {
        return $this->errorCodes[1]
            ?? $this->errorCodes[0]
            ?? throw new LogicException('Последний код ошибки отсутствует');
    }
}
