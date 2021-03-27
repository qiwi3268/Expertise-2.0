<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Utils;

use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;
use App\Exceptions\Lib\FormHandling\FileBoxException;
use App\Exceptions\Lib\Filesystem\FilesystemException;

use App\Lib\Filesystem\StarPathHandler;
use App\Lib\Settings\FileMappingsManager;
use App\Lib\Singles\Arrays\HashArray;


/**
 * Бокс файлов
 *
 * Хранит в себе данные о файлах, которые были загружены в форму
 */
final class FileBox
{

    private static self $instance;

    private HashArray $starPaths;

    /**
     * Ключ - маппинги в snake нотации.
     * Значение (секция) - индексный массив, где: 0 - result, 1 - starPaths
     */
    private array $box;


    /**
     * Закрытый конструктор класса
     *
     * @param array $fileBox
     * @throws FileBoxException
     */
    private function __construct(array $fileBox)
    {
        $box = [];
        $this->starPaths = new HashArray;

        foreach ($fileBox as $snakeMappings => ['result' => $result, 'starPaths' => $starPaths]) {

            // Проверка маппингов
            //
            if (!FileMappingsManager::validate($snakeMappings)) {
                throw new FileBoxException("Маппинги: '{$snakeMappings}' некорректны");
            }

            // Проверка структуры массива
            //
            if (
                !(is_bool($result) && is_array($starPaths) && count($starPaths) > 0) &&
                !(is_null($result) && is_array($starPaths) && empty($starPaths))
            ) {
                throw new FileBoxException("Входной массив с маппингами: '{$snakeMappings}' некорректен");
            }

            // Полная проверка starPaths
            //
            foreach ($starPaths as $starPath) {

                if ($this->starPaths->has($starPath)) {
                    throw new FileBoxException("Строка starPath: '{$starPath}' неуникальна");
                }
                try {
                    StarPathHandler::fullValidate($starPath, true);
                } catch (FilesystemException $e) {
                    throw new FileBoxException("Ошибка при полной проверки starPath строки: '{$starPath}'", 0 , $e);
                }
                $this->starPaths->add($starPath);
            }
            // Переформирование входного параметра таким образом, чтобы секция была индексным массивом
            $box[$snakeMappings] = [$result, $starPaths];
        }
        $this->box = $box;
    }


    /**
     * Возвращает сущность класса
     *
     * @param array|null $fileBox если null, то бокс файлов будет получен из объекта запроса
     * @return self
     * @throws FormLogicException
     * @throws FileBoxException
     */
    public static function getInstance(?array $fileBox = null): self
    {
        if (!isset(self::$instance)) {

            $fileBox ??= request()->get('fileBox');

            if (!is_array($fileBox)) {
                throw new FormLogicException('Ошибка при получении бокса файлов из объекта запроса');
            }
            self::$instance = new self($fileBox);
        }
        return self::$instance;
    }


    /**
     * Возвращает секцию
     *
     * @param string $snakeMappings
     * @return array
     * @throws FormInvalidArgumentException
     */
    public function getSection(string $snakeMappings): array
    {
        return array_key_exists($snakeMappings, $this->box) // Проверка присутствия секции
            ? $this->box[$snakeMappings]
            : throw new FormInvalidArgumentException("Указанные маппинги: '{$snakeMappings}' не существуют в боксе файлов");
    }
}