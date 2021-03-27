<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;


/*
 * Обеспечивает работу с деньгами
 *
 */
final class Money
{

    private string $kopeck;


    /**
     * Конструктор класса
     *
     * @param string $kopeck деньги в копейках
     */
    public function __construct(string $kopeck = '0')
    {
        $this->kopeck = $this->check($kopeck);
    }


    /**
     * Добавляет указанное число копеек
     *
     * @param string $kopeck
     */
    public function add(string $kopeck): void
    {
        $this->kopeck = bcadd($this->kopeck, $this->check($kopeck), 0);
    }


    /**
     * Вычитает указанное число копеек
     *
     * @param string $kopeck
     */
    public function sub(string $kopeck): void
    {
        $this->kopeck = bcsub($this->kopeck, $this->check($kopeck), 0);
    }


    /**
     * Проверяет на равество с другим экземпляром денег
     *
     * @param Money $money
     * @return bool
     */
    public function equals(Money $money): bool
    {
        return $this->kopeck === $money->toKopeck();
    }


    /**
     * Возвращает величину процента, которую составляет текущий экземпляр от другого экземпляра
     *
     * @param Money $money
     * @param int $precision количество десятичных знаков, до которых производится округление.
     * Принимаются значения от 0 до 3
     * @param string $separator
     * @param int $mode константа способа округления функции round
     * @return string
     * @throws InvalidArgumentException
     */
    public function percentOf(Money $money, int $precision = 0, string $separator = ',', int $mode = PHP_ROUND_HALF_UP): string
    {
        if ($money->toKopeck() === '0') {
            throw new InvalidArgumentException("Невозможно получить процент от нуля");
        }
        if ($this->lessThenZero() || $money->lessThenZero()) {
            throw new InvalidArgumentException("Невозможно получить процент при отрицательных числах");
        }
        if ($precision < 0 || $precision > 3) {
            throw new InvalidArgumentException("Некорректное количество знаков округления: {$precision}");
        }

        $a = bcmul($this->kopeck, '100');
        $b = bcdiv($a, $money->toKopeck(), 3);

        $result = (string) round($b, $precision, $mode);

        return str_replace('.', $separator, $result);
    }


    /**
     * Возвращает количество рублей
     *
     * @param string $separator
     * @return string
     */
    public function toRubles(string $separator = ','): string
    {
        $kopeck = $this->kopeck;
        $prefix = '';

        // Отрицательное число
        if (pm('/^-+(.+)$/', $kopeck, $m)) {
            $kopeck = $m;
            $prefix = '-';
        }

        $len = mb_strlen($kopeck);

        if ($len <= 2) {
            $rubles = $len == 2 ? "0{$separator}{$kopeck}" : "0{$separator}0{$kopeck}";
        } else {
            pm('/^(.+)(.{2})$/', $kopeck, $m);
            $rubles = "{$m[0]}{$separator}{$m[1]}";
        }
        return "{$prefix}{$rubles}";
    }


    /**
     * Возвращает количество копеек
     *
     * @return string
     */
    public function toKopeck(): string
    {
        return $this->kopeck;
    }


    /**
     * Проверяет, что деньги меньше нуля
     *
     * @return bool
     */
    public function lessThenZero(): bool
    {
        return pm('/^-{1}.+$/', $this->kopeck);
    }


    /**
     * Валидирует строку копеек
     *
     * @param string $kopeck
     * @return string
     * @throws InvalidArgumentException
     */
    private function check(string $kopeck): string
    {
        if (
            !pm('/^-?[1-9]+[0-9]*$/', $kopeck)
            && $kopeck !== '0'
        ) {
            throw new InvalidArgumentException("Некорректное количество копеек: '{$kopeck}'");
        }
        return $kopeck;
    }
}
