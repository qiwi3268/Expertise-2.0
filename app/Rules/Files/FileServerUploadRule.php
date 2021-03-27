<?php

namespace App\Rules\Files;

use RuntimeException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;


/**
 * Правила валидации загруженного файла на сервер
 *
 * Проверяет файл на ошибки при загрузке на сервер
 *
 * Вызову данного правила должен предшествовать вызов {@see \App\Rules\Files\UploadedFileRule}
 *
 */
final class FileServerUploadRule implements Rule
{

    private string $lastFileError;
    private string $lastFileName;


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param UploadedFile $file
     * @return bool
     * @throws RuntimeException
     */
    public function passes($attribute, $file): bool
    {
        $this->lastFileName = $file->getClientOriginalName();

        if (!$file->isValid()) {

            $this->lastFileError = match($file->getError()) {
                UPLOAD_ERR_INI_SIZE   => "Размер принятого файла: '%s' превысил максимально допустимый размер, который задан директивой upload_max_filesize",
                UPLOAD_ERR_FORM_SIZE  => "Размер загружаемого файла: '%s' превысил значение MAX_FILE_SIZE, указанное в HTML-форме",
                UPLOAD_ERR_PARTIAL    => "Загружаемый файл: '%s' был получен только частично",
                UPLOAD_ERR_NO_FILE    => "Файл: '%s' не был загружен",
                UPLOAD_ERR_NO_TMP_DIR => "Невозможно загрузить файл: '%s' так как отсутствует временная папка",
                UPLOAD_ERR_CANT_WRITE => "Файл: '%s' не удалось записать на диск",
                UPLOAD_ERR_EXTENSION  => "PHP-расширение остановило загрузку файла: '%s'",
                default => throw new RuntimeException("Не найден код ошибки при загрузке файла на сервер: '{$file->getError()}'")
            };
            return false;
        }
        return true;
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return sprintf($this->lastFileError, $this->lastFileName);
    }
}
