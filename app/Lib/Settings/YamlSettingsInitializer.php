<?php

declare(strict_types=1);

namespace App\Lib\Settings;

use Symfony\Component\Yaml\Exception\ParseException;
use App\Exceptions\Lib\Settings\SettingsLogicException;

use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Cache;

use App\Lib\Cache\KeyNaming;


/**
 * Предназначен для работы с yml настройками в директории settings
 *
 * Выполняет кэширование полученных настроек
 *
 */
final class YamlSettingsInitializer
{

    /**
     * Ключ в кэш хранилище
     *
     */
    private string $key;

    private string $path;


    /**
     * Конструктор класса
     *
     * @param string $path путь к yml файлу от директории settings
     */
    public function __construct(string $path)
    {
        $this->key = KeyNaming::create(
            [$this::class, '__construct'],
            [$path]
        );
        
        $this->path = base_path() . "/settings/{$path}.yml";
    }


    /**
     * Возвращает данные yml файла
     *
     * @return array
     * @throws SettingsLogicException
     */
    public function get(): array
    {
        //todo
        $this->forgetCache();

        return Cache::remember($this->key, now()->addDays(7), function () {

            try {
                return Yaml::parseFile($this->path, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
            } catch (ParseException $e) {
                throw new SettingsLogicException($e->getMessage(), $e->getCode(), $e);
            }
        });
    }


    /**
     * Удаляет кэш yml файла
     *
     * @return bool
     */
    public function forgetCache(): bool
    {
        return Cache::forget($this->key);
    }
}
