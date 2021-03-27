<?php


namespace App\Http\ApiControllers\Files;

use App\Exceptions\Lib\Filesystem\FilesystemException;
use App\Exceptions\Api\ExceptionContext;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\AppRequest;
use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\Files\CheckValidator as SelfValidator;
use App\Lib\Filesystem\StarPathHandler;


/*
 * Предназначен для проверки файла по его starPath
 *
 */
final class CheckController extends ApiController
{

    /**
     * Конструктор класса
     *
     * @param SelfValidator $selfValidator
     */
    public function __construct(
        private SelfValidator $selfValidator,
    ) {
        // Валидация входных параметров
        $this->selfValidator->inputParametersValidation();
    }


    /**
     * Основной метод проверки файлов
     *
     * @param AppRequest $req
     * @return JsonResponse
     */
    public function check(AppRequest $req): JsonResponse
    {
        try {
            StarPathHandler::fullValidate($req->starPath, false);
        } catch (FilesystemException $e) {
            ExceptionContext::create('Ошибка при полной валидации starPath')
                ->addContextThrowable($e)
                ->addContext('star_path', $req->starPath)
                ->throwServerException();
        }
        return $this->makeSuccessfulResponse();
    }
}
