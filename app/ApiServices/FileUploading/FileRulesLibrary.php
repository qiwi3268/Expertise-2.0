<?php

declare(strict_types=1);

namespace App\ApiServices\FileUploading;


/**
 * Библиотека правил валидации загруженных файлов
 *
 */
final class FileRulesLibrary
{

    /**
     * Максимальный допустимый размер файла в Мб
     *
     */
    public const MAX_FILE_SIZES = [
        null,
        80
    ];

    /**
     * Допустимые расширения файла
     *
     */
    public const ALLOWABLE_EXTENSIONS = [
        null,
        ['xls', 'xlsx', 'doc', 'docx', 'pdf']
    ];

    /**
     * Запрещенные символы в наименовании файла
     *
     */
    public const FORBIDDEN_SYMBOLS = [
        null,
        [',']
    ];


    /**
     * Конструктор класса
     *
     * @param int|null $maxFileSize
     * @param string[]|null $allowableExtensions
     * @param string[]|null $forbiddenSymbols
     */
    public function __construct(
        private ?int $maxFileSize,
        private ?array $allowableExtensions,
        private ?array $forbiddenSymbols
    ) {}


    /**
     * Возвращает максимально допустимый размер в Мб
     *
     * @return int|null
     */
    public function getMaxFileSize(): ?int
    {
        return $this->maxFileSize;
    }


    /**
     * Возвращает допустимые расширения
     *
     * @return string[]|null
     */
    public function getAllowableExtensions(): ?array
    {
        return $this->allowableExtensions;
    }


    /**
     * Возвращает запрещенные символы в наименовании
     *
     * @return string[]|null
     */
    public function getForbiddenSymbols(): ?array
    {
        return $this->forbiddenSymbols;
    }
}
