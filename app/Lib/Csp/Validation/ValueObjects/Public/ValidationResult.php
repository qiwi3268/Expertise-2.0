<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\ValueObjects\Public;

use JsonSerializable;


/**
 * Представляет value object результата проверки ЭЦП
 *
 */
final class ValidationResult implements JsonSerializable
{

    /**
     * Результаты проверки всех подписей true
     * Результаты проверки всех сертификатов true
     */
    public const GREEN_RESULT = 'green';

    /**
     * Результаты проверки всех подписей true
     * Результат проверки хотя бы одного сертификата false
     */
    public const ORANGE_RESULT = 'orange';

    /**
     * Результат проверки хотя бы одной подписи false
     */
    public const RED_RESULT = 'red';


    private string $result = '';


    /**
     * @var Signer[]
     */
    private array $signers = [];


    /**
     * Добавляет подписанта
     *
     * @param Signer $signer
     */
    public function addSigner(Signer $signer): void
    {
        $this->signers[] = $signer;

        $resultIsset = $this->result !== '';

        if (!$signer->getSignatureResult()) {

            $this->result = self::RED_RESULT;
        } elseif ($signer->getCertificateResult()) {

            if (!$resultIsset) {
                $this->result = self::GREEN_RESULT;
            }
        } else {

            if (!$resultIsset || $this->result == self::GREEN_RESULT) {
                $this->result = self::ORANGE_RESULT;
            }
        }
    }


    /**
     * Проверяет наличие подписантов
     *
     * @return bool
     */
    public function hasSigners(): bool
    {
        return !empty($this->signers);
    }


    /**
     * Возвращает массив подписантов
     *
     * @return Signer[]
     */
    public function getSigners(): array
    {
        return $this->signers;
    }


    /**
     * Предназначен для сериализации объекта
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'result'  => $this->result,
            'signers' => $this->signers
        ];
    }
}
