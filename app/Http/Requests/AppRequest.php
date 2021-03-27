<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\Requests\RequiredRequestParameterDoesNotExist;
use Illuminate\Foundation\Http\FormRequest;


/**
 * Расширенный класс корневого запроса
 *
 */
final class AppRequest extends FormRequest
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
        if (!$this->has($key)) {
            throw new RequiredRequestParameterDoesNotExist("Параметр: '{$key}' не существует в запросе");
        }
        return $this->get($key);
    }


    /**
     * Возвращает индексный массив входных данных в правильном подярке,
     * если все ключи присутствуют в запросе. false в противном случае
     *
     * @param array $keys
     * @return bool|array
     */
    public function unpackInput(array $keys): bool|array
    {
        if ($this->has($keys)) {

            $result = [];

            foreach ($keys as $key) {

                $result[] = $this->get($key);
            }
            return $result;
        }
        return false;
    }










    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
