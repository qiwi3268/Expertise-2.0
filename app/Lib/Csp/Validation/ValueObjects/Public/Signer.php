<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\ValueObjects\Public;

use App\Exceptions\Lib\Csp\CspInvalidArgumentException;

use App\Lib\Csp\Certification\ValueObjects\Certificate;
use App\Lib\ValueObjects\Fio;
use JsonSerializable;


/**
 * Представляет value object подписанта из сообщения
 *
 * Включает в себя данные о подписанте и результаты проверки подписи
 *
 */
final class Signer implements JsonSerializable
{


    /**
     * Конструктор класса
     *
     * @param Fio $fio
     * @param bool $signatureResult
     * @param string $signatureMessage
     * @param Certificate $certificate
     * @param bool $certificateResult
     * @param string $certificateMessage
     * @throws CspInvalidArgumentException
     */
    public function __construct(
        private Fio $fio,
        private bool $signatureResult,
        private string $signatureMessage,
        private Certificate $certificate,
        private bool $certificateResult,
        private string $certificateMessage,
    ) {
        if (empty($signatureMessage) || empty($certificateMessage)) {
            throw new CspInvalidArgumentException('Сообщение проверки сертификата/подписи не может быть пустым');
        }
    }


    /**
     * Возвращает объект фио
     *
     * @return Fio
     */
    public function getFio(): Fio
    {
        return $this->fio;
    }


    /**
     * Возвращает объект сертификата
     *
     * @return Certificate
     */
    public function getCertificate(): Certificate
    {
        return $this->certificate;
    }


    /**
     * Возвращает результат проверки подписи
     *
     * @return bool
     */
    public function getSignatureResult(): bool
    {
        return $this->signatureResult;
    }


    /**
     * Возвращает сообщение проверки подписи
     *
     * @return string
     */
    public function getSignatureMessage(): string
    {
        return $this->signatureMessage;
    }


    /**
     * Возвращает результат проверки сертификата
     *
     * @return bool
     */
    public function getCertificateResult(): bool
    {
        return $this->certificateResult;
    }


    /**
     * Возвращает сообщение проверки сертификата
     *
     * @return string
     */
    public function getCertificateMessage(): string
    {
        return $this->certificateMessage;
    }


    /**
     * Предназначен для сериализации объекта
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'fio'                => $this->fio->getLongFio(),
            'signatureResult'    => $this->getSignatureResult(),
            'signatureMessage'   => $this->getSignatureMessage(),
            'certificate'        => $this->certificate->getStringArray(),
            'certificateResult'  => $this->getCertificateResult(),
            'certificateMessage' => $this->getCertificateMessage(),
        ];
    }
}
