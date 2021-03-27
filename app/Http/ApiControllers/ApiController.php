<?php


namespace App\Http\ApiControllers;

use Throwable;
use App\Exceptions\Api\SaveModelException;
use App\Exceptions\Api\ExceptionContext;
use App\Exceptions\Api\ClientException;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

use App\Models\AppModel;
use App\Repositories\Repository;
use App\Lib\Formats\ApiResponse\ApiResponseFormatter;


/*
 * Базовый класс для всех api приложения
 *
 * Предоставляет общий функционал для дочерних классов
 *
 */
abstract class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Входные параметры со стороны js невалидны
     *
     */
    public const CLIENT_INVALID_INPUT_ERROR_CODE = 'ciiec';


    /**
     * Обрабатываемо сохраняет модель
     *
     * @param AppModel $model
     * @return AppModel
     * @throws SaveModelException
     */
    protected function saveModel(AppModel $model): AppModel
    {
        try {
            $result = $model->save();
        } catch (Throwable $e) {
            throw new SaveModelException('Исключение во время сохранения модели', $e);
        }
        return $result ? $model : throw new SaveModelException("Метод сохранения модели 'save' вернул false");
    }


    /**
     * Проверяет существование записи из репозитория по её id
     *
     * @param string|Repository $repository
     * @param int $id
     * @param bool $throwClientException
     * @return bool
     * @throws ClientException
     */
    protected function existsById(string|Repository $repository, int $id, bool $throwClientException = true): bool
    {
        if (is_string($repository)) {
            $repository = new $repository;
        }

        if (!$repository->existsById($id)) {

            if ($throwClientException) {

                $className = $repository::class;

                ExceptionContext::create("id записи: {$id} не существует в репозитории: '{$className}'")
                    ->setCode(ApiController::CLIENT_INVALID_INPUT_ERROR_CODE)
                    ->throwClientException();
            }
            return false;
        }
        return true;
    }


    /**
     * Создает json ответ успешного выполнения
     *
     * @param string $message
     * @param array $data
     * @param array $meta
     * @return JsonResponse
     */
    protected function makeSuccessfulResponse(
        string $message = '',
        array $data = [],
        array $meta = []
    ): JsonResponse {

        return response()->json(
            ApiResponseFormatter::getSuccessBody($message, $data, $meta),
            200
        );
    }


    /**
     * Создает json ответ серверной ошибки
     *
     * @param string $message
     * @param array $errors
     * @param string $code
     * @param array $meta
     * @return JsonResponse
     */
    protected function makeServerErrorResponse(
        string $message = '',
        array $errors = [],
        string $code = '',
        array $meta = [],
    ): JsonResponse {

        return response()->json(
            ApiResponseFormatter::getErrorBody($message, $errors, $code, $meta),
            500
        );
    }


    /**
     * Создает json ответ клиентской ошибки
     *
     * @param string $message
     * @param array $errors
     * @param string $code
     * @param array $meta
     * @return JsonResponse
     */
    protected function makeClientErrorResponse(
        string $message = '',
        array $errors = [],
        string $code = '',
        array $meta = []
    ): JsonResponse {

        return response()->json(
            ApiResponseFormatter::getErrorBody($message, $errors, $code, $meta),
            422
        );
    }
}
