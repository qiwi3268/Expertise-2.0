<?php

namespace App\Rules\Files;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;


/**
 * Правила валидации загруженного файла на сервер
 *
 * Проверяет файл на запрещенные символы в названии
 *
 */
final class FileForbiddenSymbolsRule implements Rule
{
    private string $lastFileName;


    /**
     * Конструктор класса
     *
     * @param array $forbiddenSymbols
     */
    public function __construct(private array $forbiddenSymbols)
    {
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param UploadedFile $file
     * @return bool
     */
    public function passes($attribute, $file): bool
    {
        $this->lastFileName = $name = $file->getClientOriginalName();
        return !str_contains_any($name, $this->forbiddenSymbols);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Файл: '{$this->lastFileName}' имеет в названии запрещенные символы: '" . implode(' или ', $this->forbiddenSymbols) . "'";
    }
}
