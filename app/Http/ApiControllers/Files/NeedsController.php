<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Files;

use Throwable;
use Exception;
use App\Exceptions\Api\ExceptionContext;

use App\Http\Requests\AppRequest;
use App\Lib\Filesystem\StarPathHandler;
use App\Repositories\Files\FileRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\ApiServices\Validation\Files\NeedsValidator as SelfValidator;

use App\Http\ApiControllers\ApiController;


/*
 * Предназначен для обработки "нужности" файла
 *
 */
final class NeedsController extends ApiController
{

    private array $toSave;
    private array $toDelete;


    /**
     * Конструктор класса
     *
     * @param FileRepository $rep
     * @param AppRequest $req
     * @param SelfValidator $selfValidator
     */
    public function __construct(
        private FileRepository $rep,
        private AppRequest $req,
        private SelfValidator $selfValidator,
    ) {
        // Валидация входных параметров
        $selfValidator->inputParametersValidation();

        $this->toSave = $req->toSave;
        $this->toDelete = $req->toDelete;
    }


    /**
     * Основной метод
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function needs(): JsonResponse
    {
        $toSave = $this->toSave;
        $toDelete = $this->toDelete;

        if (
            (empty($toSave) && empty($toDelete))
            || arr_has_duplicates([...$toSave, ...$toDelete])
        ) {
            ExceptionContext::create('Оба массива пусты или имеются неуникальные элементы')
                ->setCode(ApiController::CLIENT_INVALID_INPUT_ERROR_CODE)
                ->addContext('to_save', $toSave)
                ->addContext('to_delete', $toDelete)
                ->throwClientException();
        }

        DB::beginTransaction();

        try {

            $this->handleArray($toDelete, false);
            $this->handleArray($toSave, true);
        } catch (Throwable $e) {

            DB::rollBack();
            ExceptionContext::create('Ошибка при обновлении файла')
                ->addContextThrowable($e)
                ->addContext('to_save', $toSave)
                ->addContext('to_delete', $toDelete)
                ->throwServerException();
        }
        DB::commit();
        return $this->makeSuccessfulResponse();
    }


    /**
     * Обрабатывает массив со starPath элементами
     *
     * @param array $array
     * @param bool $isNeeds
     * @throws Exception
     */
    private function handleArray(array $array, bool $isNeeds): void
    {
        foreach ($array as $el) {

            $hashName = StarPathHandler::createUnvalidated($el)->getHashName();

            $file = $this->rep->getByHashName($hashName, ['id', 'is_needs'])
                ?? throw new Exception("Файл с hashName: '{$hashName}' не существует в БД");

            $file->is_needs = $isNeeds;
            $file->save();
        }
    }
}
