<?php

declare(strict_types=1);

namespace App\View\Utils;

use App\Lib\Settings\Miscs\SingleMiscsManager;
use App\Repositories\Miscs\MiscRepository;

use App\Lib\Cache\KeyNaming;
use App\Lib\Cache\CacheArray;


/**
 * Содержит вспомогательные функции для работы со справочниками
 *
 */
final class MiscsHelper
{
    static CacheArray $cache;


    /**
     * Возвращает массив одиночных справочников
     *
     * @param string $alias
     * @return MiscItem[]
     */
    public static function getSingleMiscItems(string $alias): array
    {
        $key = KeyNaming::create([self::class, 'getSingleMiscItems'], [$alias]);

        return self::getCacheArray()->remember($key, function () use ($alias) {

            $mgr = SingleMiscsManager::getInstance();
            $rep = new MiscRepository($mgr->getClassNameByAlias($alias));
            $items = [];

            foreach ($rep->getAllWhereActive() as $item) {

                $items[] = new MiscItem($item->id, $item->label);
            }
            return $items;
        });
    }


    /**
     * Возвращает кэш массив
     *
     * @return CacheArray
     */
    private static function getCacheArray(): CacheArray
    {
        if (!isset(self::$cache)) {
            self::$cache = new CacheArray;
        }
        return self::$cache;
    }
}
