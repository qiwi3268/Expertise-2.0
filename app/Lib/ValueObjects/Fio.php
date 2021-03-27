<?php

declare(strict_types=1);

namespace App\Lib\ValueObjects;

use InvalidArgumentException;

use Illuminate\Support\Str;
use App\Lib\Singles\PatternLibrary;


/**
 * Представляет value object фио
 *
 */
final class Fio
{

    private string $lastName;
    private string $firstName;
    private ?string $middleName;

    /**
     * Конструктор класса
     *
     * @param string $lastName фамилия
     * @param string $firstName имя
     * @param string|null $middleName отчество
     * @throws InvalidArgumentException
     */
    public function __construct(string $lastName, string $firstName, ?string $middleName) {

        $this->lastName = self::handlePart($lastName);
        $this->firstName = self::handlePart($firstName);
        $this->middleName = is_null($middleName) ? null : self::handlePart($middleName);
    }


    /**
     * Возвращает массив с разобранной строкой фио
     *
     * Методу без разницы, в каком порядке будут следовать фамилия, имя и отчество.
     * Строк, разделенных пробелом, должно быть 2 или 3
     *
     * @param string $string строка
     * @return array|null null, если строка некорректна
     */
    public static function parseString(string $string): ?array
    {
        $result = explode(' ', $string);

        $count = count($result);

        if ($count != 3 && $count != 2) {
            return null;
        }

        try {

            foreach ($result as &$name) {

                $name = self::handlePart($name);
            }
            unset($name);
        } catch (InvalidArgumentException) {

            return null;
        }
        $result[2] ??= null; // Отсутствие отчества

        return $result;
    }


    /**
     * Внутренний метод для обработки части фио
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    private static function handlePart(string $name): string
    {
        if (!PatternLibrary::name($name)) {
            throw new InvalidArgumentException("Данные фио: '{$name}' некорректны");
        }
        return Str::ucfirst($name);
    }


    /**
     * Возвращает фамилию
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }


    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }


    /**
     * Возвращает отчество
     *
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }


    /**
     * Возвращает длинное фио
     *
     * @return string
     */
    public function getLongFio(): string
    {
        $result = "{$this->lastName} {$this->firstName}";
        return $this->middleName ? "{$result} {$this->middleName}" : $result;
    }


    /**
     * Возвращает короткое фио
     *
     * @return string
     */
    public function getShortFio(): string
    {
        $result = "{$this->lastName} " . Str::limit($this->firstName, 1, '') . '.';
        return $this->middleName
            ? $result . Str::limit($this->middleName, 1, '') . '.'
            : $result;
    }
}
