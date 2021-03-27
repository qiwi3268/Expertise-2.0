<?php

declare(strict_types=1);

namespace App\Lib\Settings;

use App\Exceptions\Lib\Settings\SettingsLogicException;
use App\Repositories\Docs\DocRepository;
use App\Models\Docs\DocModel;


/**
 * Предназначен для управления всеми зависимостями документов экспертизы
 *
 * Менеджер файла documents
 *
 * @property string application
 */
final class DocumentsManager
{
    private const YML_PATH = 'documents';

    private static self $instance;

    private array $schema;


    /**
     * Закрытый конструктор класса
     *
     */
    private function __construct()
    {
        $this->schema = yml(self::YML_PATH);
    }


    /**
     * Возвращает сущность класса
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * Возвращает проверенный тип документа
     *
     * Данный метод является единственной проверкой на кооректность названия документа в системе.
     * В программном коде использовать названия документов следует только через данный метод
     *
     * @param string $dt тип документа
     * @return string
     * @throws SettingsLogicException
     */
    public function __get(string $dt): string
    {
        if (!isset($this->schema[$dt])) {
            throw new SettingsLogicException("Указанный тип документа: '{$dt}' не определен в файле настроек");
        }
        return $dt;
    }


    /**
     * Предназначен для корректных проверок на существование при использовании __get
     *
     * @param string $dt
     * @return bool
     */
    public function __isset(string $dt): bool
    {
        return isset($this->schema[$dt]);
    }


    /**
     * Возвращает полное название класса модели по её типу документа
     *
     * @param string $dt
     * @return string
     */
    public function getModelClassNameByDocumentType(string $dt): string
    {
        return $this->schema[$this->{$dt}]['model'];
    }


    /**
     * Возвращает экземпляр модели по её типу документа
     *
     * @param string $dt тип документа
     * @return DocModel
     */
    public function getModelByDocumentType(string $dt): DocModel
    {
        return new $this->schema[$this->{$dt}]['model'];
    }


    /**
     * Возвращает репозиторий модели по её типу документа
     *
     * @param string $dt тип документа
     * @return DocRepository
     */
    public function getRepositoryByDocumentType(string $dt): DocRepository
    {
        return new $this->schema[$this->{$dt}]['repository'];
    }


    /**
     * Возвращает тип документа по экземпляру модели
     *
     * @param DocModel $model
     * @return string
     * @throws SettingsLogicException
     */
    public function getDocumentTypeByModel(DocModel $model): string
    {
        $className = $model::class;

        foreach ($this->schema as $dt => $document) {
            if ($document['model'] == $className) {
                return $dt;
            }
        }
        throw new SettingsLogicException("Переданный экземпляр модели: '{$className}' не определен в файле настроек");
    }
}
