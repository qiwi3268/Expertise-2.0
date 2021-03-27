<?php

namespace App\Rules\Files;


use Illuminate\Contracts\Validation\Rule;
use SplFileInfo;


/**
 * Правила валидации загруженного файла на сервер
 *
 * Проверяет являетли ли элемент экземпляром загруженного файла
 *
 */
final class UploadedFileRule implements Rule
{


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param mixed $file
     * @return bool
     */
    public function passes($attribute, $file): bool
    {
        return $file instanceof SplFileInfo;
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Элемент ':attribute' не является экземпляром загруженного на сервер файла";
    }
}
