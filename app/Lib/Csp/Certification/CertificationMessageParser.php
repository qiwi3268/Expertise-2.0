<?php

declare(strict_types=1);

namespace App\Lib\Csp\Certification;

use Exception;
use App\Exceptions\Lib\Csp\CspParsingException;
use App\Exceptions\Lib\Csp\CspInvalidArgumentException;

use App\Lib\Csp\MessageParser;
use App\Lib\Csp\Certification\ValueObjects\Certificate;
use App\Lib\Date\DateHelper;
use App\Lib\Date\DateFormatter;
use DateTimeZone;
use DateTimeImmutable;


final class CertificationMessageParser extends MessageParser
{


    /**
     * Конструктор класса
     *
     * @param string $message
     * @throws CspParsingException
     */
    public function __construct(private string $message)
    {
        $errorCode = $this->getErrorCode($message);

        if ($errorCode !== self::OK_ERROR_CODE) {
            throw new CspParsingException("Код ошибки: '{$errorCode}' не соответствует успешному выполнению команды получения информации о сертификатах");
        }
    }


    /**
     * Разбирает сообщение утилиты certmgr на объекты сертификатов
     *
     * @return Certificate[]
     * @throws CspParsingException
     * @throws CspInvalidArgumentException
     */
    public function getCertificates(): array
    {
        // Отделение сертификатов от названия утилиты и ErrorCode
        $message = preg_split('/={77}/', $this->message);

        if (count($message) != 3) {
            throw new CspParsingException('Неизвестный формат входного сообщения');
        }

        $message = trim($message[1]);

        // Сертификаты разделены строкой вида '1-------'
        $certificates = preg_split('/\d+-{3,}/', $message, -1, PREG_SPLIT_NO_EMPTY);

        if ($certificates[0] == $message) {
            throw new CspParsingException('Во входном сообщении не найдена информация о сертификатах');
        }

        $result = $this->explode($certificates);
        $result = $this->transformParts($result);
        $result = $this->conversionData($result);
        return $result;
    }


    /**
     * Разбивает строки с сертификатами на составные части
     *
     * @param array $certificates
     * @return array
     * @throws CspParsingException
     */
    private function explode(array $certificates): array
    {
        $result = [];

        foreach ($certificates as $certificate) {

            [$err, $debug] = info_implode(
                str_get_missing($certificate, ['Issuer', 'Subject', 'Serial', 'Not valid before', 'Not valid after'])
            );

            if ($err) {
                throw new CspParsingException("В данных сертификата отсутстуют обязательные поля: '{$debug}'");
            }

            $parts = [];

            foreach (explode(PHP_EOL, $certificate) as $part) {

                // Содержит более двух пробелов. До и после символа ':'
                // Если строка без левой части, то там больше двух пробелов
                if (pm('/\s{2,}/', $part)) {

                    $parts[] = trim($part);
                }
            }
            $result[] = $parts;
        }
        return $result;
    }


    /**
     * Трансформирует составные части сертификатов в ассоциативные массивы
     *
     * @param array $certificates
     * @return array
     * @throws CspParsingException
     */
    private function transformParts(array $certificates): array
    {
        $result = [];

        foreach ($certificates as $certificate) {

            $parts = [];

            foreach ($certificate as $part) {

                $splitted = preg_split('/\s+:\s+/', $part);
                $count = count($splitted);

                if ($count == 2) {

                    [$lastKey, $value] = $splitted;

                    // Замена всех пробелов, чтобы хранить первую часть в виде ключа массива
                    $lastKey = str_replace(' ', '_', $lastKey);

                    if (array_key_exists($lastKey, $parts)) {
                        $parts[$lastKey][] = $value; // По одному ключу могут храниться несколько значений
                    } else {
                        $parts[$lastKey] = [$value];
                    }
                } elseif ($count == 1) {

                    if (!isset($lastKey)) {
                        throw new CspParsingException('Ошибка при трансформировании составной части в массив. Отсутствует ключ');
                    }
                    $parts[$lastKey][] = $part;
                } else {

                    throw new CspParsingException("Ошибка при трансформировании составной части в массив. Некорректное количество элементов в разделенной строке: {$count}");
                }
            }

            // Преобразование массива из одного элемента в строку
            foreach ($parts as $key => $part) {

                if (count($part) == 1) {

                    $parts[$key] = $part[0];
                }
            }
            $result[] = $parts;
        }
        return $result;
    }


    /**
     * Преобразовывает данные сертификатов к выходному формату
     *
     * @param array $certificates
     * @return array
     * @throws CspInvalidArgumentException
     * @throws CspParsingException
     */
    private function conversionData(array $certificates): array
    {
        foreach ($certificates as &$certificate) {

            // Приведение ключей сертификата к нижнему регистру
            foreach ($certificate as $key => $part) {

                $certificate[mb_strtolower($key)] = $part;

                unset($certificate[$key]);
            }

            // Данные всегда являются строками, а не массивами
            $certificate['issuer']           = $this->explodeCertificateFields($certificate['issuer']);
            $certificate['subject']          = $this->explodeCertificateFields($certificate['subject']);
            $certificate['not_valid_before'] = $this->convertDate($certificate['not_valid_before']);
            $certificate['not_valid_after']  = $this->convertDate($certificate['not_valid_after']);

            $certificate = new Certificate($certificate);
        }
        unset($certificate);

        return $certificates;
    }


    /**
     * Разбивает поля сертификата в ассоциативный массив
     *
     * @param string $certificate
     * @return array
     * @throws CspParsingException
     */
    private function explodeCertificateFields(string $certificate): array
    {
        // Имя поля сертификата состоит не только из заглавных букв. Пример - UnstructuredName
        // Искуственно добавляем для первого поля ', '
        $arr = preg_split('/,\s([a-z]+)=/i', ", {$certificate}", -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $count = count($arr); // Минимальное число 1 - при отсутствии вхождения шаблона

        if (($count % 2) != 0) {
            throw new CspParsingException("Количество полей сертификата: {$count} не является четным числом");
        }

        $result = [];

        for ($i = 0; $i < ($count - 1); $i += 2) {
            $result[$arr[$i]] = $arr[$i + 1];
        }
        return $result;
    }


    /**
     * Конвертирует строку с датой и временем к типу данных БД DATETIME в зоне UTC
     *
     * @param string $date
     * @return string
     * @throws CspParsingException
     */
    private function convertDate(string $date): string
    {
        if (!pm('/(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2}:\d{2})\s+([A-Z]+)/', $date, $m)) {
            throw new CspParsingException("Ошибка при разборе строки даты: '{$date}'");
        }
        [$d, $t, $tz] = $m;

        try {
            $dtz = new DateTimeZone($tz);
        } catch(Exception $e) {
            throw new CspParsingException("Ошибка при создании объекта DateTimeZone из строки: '{$tz}'", 0, $e);
        }

        $obj = DateTimeImmutable::createFromFormat('d/m/Y H:i:s', "{$d} {$t}", $dtz);

        return DateHelper::getUtcDate($obj)->format(DateFormatter::DATETIME_FORMAT);
    }
}
