<?php

namespace App\Rules\Files;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;


/**
 * Правила валидации загруженного файла на сервер
 *
 * Проверяет файл на максимальный размер
 *
 */
final class FileMaxSizeRule implements Rule
{
    private string $lastFileName;


    /**
     * Конструктор класса
     *
     * @param int $maxSizeMB
     */
    public function __construct(private int $maxSizeMB)
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
        $this->lastFileName = $file->getClientOriginalName();
        return $file->getSize() <= ($this->maxSizeMB * 1024 * 1024);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Файл: '{$this->lastFileName}' превысил максимальный размер: {$this->maxSizeMB} Мб";
    }
}
