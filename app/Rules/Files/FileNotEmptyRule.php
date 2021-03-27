<?php

namespace App\Rules\Files;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;


/**
 * Правила валидации загруженного файла на сервер
 *
 * Проверяет файл, что он не пустой
 *
 */
final class FileNotEmptyRule implements Rule
{
    private string $lastFileName;


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param UploadedFile $file
     * @return bool
     */
    public function passes($attribute, $file): bool
    {
        $this->lastFileName = $file->getClientOriginalName();
        return $file->getSize() > 0;
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Файл: '{$this->lastFileName}' является пустым";
    }
}
