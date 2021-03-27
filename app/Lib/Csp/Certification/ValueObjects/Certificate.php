<?php

declare(strict_types=1);

namespace App\Lib\Csp\Certification\ValueObjects;

use App\Exceptions\Lib\Csp\CspInvalidArgumentException as Ex;

use App\Lib\Date\DateHelper;
use App\Lib\Date\DateFormatter;


/**
 * Представляет value object информации о сертификате
 *
 * Гарантированно включает в себя данные о:
 *
 * - Серийном номере (serial)
 * - Издателе (issuer)
 * - Владельце (subject)
 * - Дате и времени начала действия (notValidBefore)
 * - Дате и времени окончания действия (notValidAfter)
 *
 */
final class Certificate
{

    /**
     * Конструктор класса
     *
     * @param array $certificate
     * @throws Ex
     */
    public function __construct(private array $certificate)
    {
        [$err, $debug] = info_implode(
            arr_missing_keys($certificate, ['issuer', 'subject', 'serial', 'not_valid_before', 'not_valid_after'])
        );

        // На данном этапе уже невозможно удостовериться, что даты в зоне UTC
        $before = $certificate['not_valid_before'];
        $after = $certificate['not_valid_after'];

        if ($err)                               throw new Ex("В массиве сертификата отсутствуют обязательные ключи: '{$debug}'");
        if (!is_array($certificate['issuer']))  throw new Ex('Данные об издателе сертификата должны быть массивом');
        if (!is_array($certificate['subject'])) throw new Ex('Данные о владельце сертификата должны быть массивом');
        if (!is_string($before))                throw new Ex("Элемент массива по ключу: 'not_valid_before' должен быть строкой");
        if (!is_string($after))                 throw new Ex("Элемент массива по ключу: 'not_valid_after' должен быть строкой");
        if (!DateHelper::validate($before))     throw new Ex("Строка с датой и временем начала действия сертификата: '{$before}' некорректна");
        if (!DateHelper::validate($after))      throw new Ex("Строка с датой и временем окончания действия сертификата: '{$after}' некорректна");
    }


    /**
     * Возвращает данные об издателе сертификата
     *
     * @return string[]
     */
    public function getIssuer(): array
    {
        return $this->certificate['issuer'];
    }


    /**
     * Возвращает данные о владельце сертификата
     *
     * @return string[]
     */
    public function getSubject(): array
    {
        return $this->certificate['subject'];
    }


    /**
     * Возвращает серийный номер сертификата
     *
     * @return string
     */
    public function getSerial(): string
    {
        return $this->certificate['serial'];
    }


    /**
     * Возвращает дату и время начала действия сертифката в зоне UTC
     *
     * Формат Y-m-d H:i:s
     *
     * @return string
     */
    public function getNotValidBefore(): string
    {
        return $this->certificate['not_valid_before'];
    }


    /**
     * Возвращает дату и время окончания действия сертифката в зоне UTC
     *
     * Формат Y-m-d H:i:s
     *
     * @return string
     */
    public function getNotValidAfter(): string
    {
        return $this->certificate['not_valid_after'];
    }


    /**
     * Возвращает диапазон дат действия сертификата
     *
     * @return string
     */
    public function getValidRange(): string
    {
        $f = new DateFormatter(DateFormatter::DATETIME_FORMAT, DateFormatter::d_m_Y_FORMAT);

        return "c {$f($this->getNotValidBefore())} по {$f($this->getNotValidAfter())}";
    }



    /**
     * Возвращает массив данных, которые гарантированно присутствуют в сертификате
     *
     * @return string[]
     */
    public function getStringArray(): array
    {
        return [
            'issuer'     => assoc_implode($this->getIssuer()),
            'subject'    => assoc_implode($this->getSubject()),
            'serial'     => $this->getSerial(),
            'validRange' => $this->getValidRange()
        ];
    }


    /**
     * Возвращает полный массив данных о сертификате
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->certificate;
    }
}