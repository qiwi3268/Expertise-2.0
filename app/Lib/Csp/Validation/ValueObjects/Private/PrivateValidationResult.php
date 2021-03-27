<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\ValueObjects\Private;

use App\Exceptions\Lib\Csp\CspInvalidArgumentException;


/**
 * Представляет value object результата проверки ЭЦП
 *
 * Предназначен только для внутреннего использования
 *
 */
final class PrivateValidationResult
{
    /**
     * @var PrivateSigner[]
     */
    private array $signers = [];


    /**
     * Добавляет подписанта
     *
     * @param PrivateSigner $signer
     */
    public function addSigner(PrivateSigner $signer): void
    {
        $this->signers[] = $signer;
        $signer->index = count($this->signers) - 1;
    }


    /**
     * Проверяет массив подписантов на пустоту
     *
     * @return bool
     */
    public function isSignersEmpty(): bool
    {
        return empty($this->signers);
    }


    /**
     * Возвращает массив подписантов
     *
     * @return PrivateSigner[]
     */
    public function getSigners(): array
    {
        return $this->signers;
    }


    /**
     * Возвращает подписанта по его индексу
     *
     * @param int $index
     * @return PrivateSigner
     * @throws CspInvalidArgumentException
     */
    public function getSignerByIndex(int $index): PrivateSigner
    {
        if (!isset($this->signers[$index])) {
            throw new CspInvalidArgumentException("Подписант по индексу: {$index} отсутствует");
        }
        return $this->signers[$index];
    }
}
