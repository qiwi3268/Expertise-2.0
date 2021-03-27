<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\Requests\RequiredRequestParameterDoesNotExist;
use Illuminate\Http\Request as BaseRequest;


/**
 * Расширенный класс корневого запроса
 *
 */
final class TestRequest extends BaseRequest
{

    /**
     * Возвращает обязательный параметр запроса
     *
     * gr - get required
     *
     * @param string $key
     * @return mixed
     * @throws RequiredRequestParameterDoesNotExist
     */
    public function gr(string $key): mixed
    {
        if (!$this->exists($key)) {
            throw new RequiredRequestParameterDoesNotExist("Параметр: '{$key}' не существует в запросе");
        }
        return $this->get($key);
    }
}
