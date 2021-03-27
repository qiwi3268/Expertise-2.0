<?php

namespace App\Rules\Files;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;


/**
 * Правила валидации загруженного файла на сервер
 *
 * Проверяет файл на допустимые расширения
 *
 */
final class FileAllowableExtensionsRule implements Rule
{
    private const SIG_EXTENSIONS = ['sig', 'p7z'];
    private string $lastFileName;


    /**
     * Конструктор класса
     *
     * @param array $extensions
     */
    public function __construct(private array $extensions)
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

        return Str::of($name)
            ->explode('.')
            ->filter(function (string $value) {
                return !is_numeric($value)
                    && mb_strlen($value) > 2
                    && !in_array($value, self::SIG_EXTENSIONS);
            })->hasAny($this->extensions);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Файл: '{$this->lastFileName}' должен иметь одно из расширений: " . implode(', ', $this->extensions);
    }
}
