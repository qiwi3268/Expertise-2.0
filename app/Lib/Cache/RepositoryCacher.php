<?php

declare(strict_types=1);

namespace App\Lib\Cache;

use Illuminate\Support\Facades\Cache;
use App\Repositories\Repository;


/**
 * Предназначен для кэширования данных, полученных из репозитория
 *
 */
final class RepositoryCacher
{

    /**
     * Конструктор класса
     *
     * @param Repository $repository
     */
    public function __construct(private Repository $repository)
    {
    }

    
    /**
     * Обёртка для вызова метода репозитория
     *
     * @param string $method
     * @param array $params массив параметров должен быть конвертируемым в json
     * Если массив содержит объекты, то они должны быть сериализуемы, иначе будет "{}"
     * @return mixed
     */
    public function call(string $method, array $params = []): mixed
    {
        $key = KeyNaming::create(
            [$this::class, 'call'],
            [$this->repository::class, $method, $params]
        );

        return Cache::remember($key, now()->addDays(7), function () use (
            $method,
            $params
        ) {
            return $this->repository->{$method}(...$params);
        });
    }
}
