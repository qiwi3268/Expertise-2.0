<?php

declare(strict_types=1);

namespace App\Lib\Settings;

use App\Exceptions\Lib\Settings\SettingsLogicException;


/**
 * Менеджер файла file_mappings
 *
 * Работает в контексте конкретного маппинга, а не всей схемы
 *
 */
final class FileMappingsManager
{

    private const YML_PATH = 'file_mappings';

    private array $schema;
    private array $mappings;


    /**
     * Конструктор класса
     *
     * @param string $snakeMappings маппинги в snake нотации
     * @throws SettingsLogicException
     */
    public function __construct(string $snakeMappings)
    {
        if (!self::validate($snakeMappings)) {
            throw new SettingsLogicException("Указанные snakeMappings: '{$snakeMappings}' не прошли валидацию");
        }
        [$m1, $m2, $m3] = explode('_', $snakeMappings);

        $this->schema = yml(self::YML_PATH)[$m1][$m2][$m3];

        $this->mappings = [
            (int) $m1,
            (int) $m2,
            (int) $m3
        ];
    }


    /**
     * Возвращает массив маппингов
     *
     * @return array
     */
    public function getMappings(): array
    {
        return $this->mappings;
    }


    /**
     * Возвращает тип документа
     *
     * @return string
     */
    public function getDocumentType(): string
    {
        return $this->schema['document'];
    }


    /**
     * Возвращает полное название класса загрузчика файлов
     *
     * @return string
     */
    public function getUploaderClassName(): string
    {
        return $this->schema['uploader'];
    }


    /**
     * Валидирует маппинги
     *
     * @param string $snakeMappings маппинги в snake нотации
     * @return bool
     */
    public static function validate(string $snakeMappings): bool
    {
        return pm('/^(\d+)_(\d+)_(\d+)$/', $snakeMappings, $m)
            && isset(yml(self::YML_PATH)[$m[0]][$m[1]][$m[2]]);
    }
}
