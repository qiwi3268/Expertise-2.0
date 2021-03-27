<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Files;

use Throwable;
use App\Exceptions\Lib\Csp\CspException;
use App\Exceptions\Lib\Csp\CspHandledException;
use App\Exceptions\Api\ExceptionContext;
use App\Exceptions\Api\SaveModelException;
use App\Exceptions\Lib\Filesystem\FilesystemException;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\AppRequest;

use App\Models\Files\FileValidationResult;
use App\Models\Files\FileInternalSign;

use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\Files\InternalSignatureValidationValidator as SelfValidator;
use App\Lib\Filesystem\StarPathHandler;
use App\Lib\Filesystem\StarPath;
use App\Repositories\Files\FileRepository;

use App\Lib\Csp\Validation\Commands\InternalSignatureCommander;
use App\Lib\Csp\Validation\SignatureValidator;
use App\Lib\Csp\Validation\ErrorDecoder;



/*
 * Предназначен для обработки файла со встроенной подписью
 *
 */
final class InternalSignatureValidationController extends ApiController
{

    /**
     * Проверяемый файл не является встроенной подписью
     */
    private const FILE_IS_NOT_INTERNAL_SIGN_ERROR_CODE = 'finisec';

    /**
     * Проверяемый файл является открепленной подписью
     */
    private const FILE_IS_EXTERNAL_SIGN_ERROR_CODE = 'fiesec';

    /**
     * Проверяемый файл подписи некорректен
     */
    private const FILE_IS_INCORRECT_ERROR_CODE = 'fiiec';

    private StarPath $starPath;


    /**
     * Конструктор класса
     *
     * @param AppRequest $req
     * @param SelfValidator $selfValidator
     * @param FileRepository $fileRepository
     * @throws FilesystemException
     */
    public function __construct(
        private AppRequest $req,
        private SelfValidator $selfValidator,
        private FileRepository $fileRepository
    ) {
        // Валидация входных параметров
        $this->selfValidator->inputParametersValidation();

        $this->starPath = StarPathHandler::createUnvalidated($req->internalSignatureStarPath);
    }


    /**
     * Основной метод
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function validateSignature(): JsonResponse
    {
        [$fileId, $fileSize] = $this->fileRepository->getIdAndFileSizeByHashName($this->starPath->getHashName());

        $commander = new InternalSignatureCommander($this->starPath->getAbsPath());
        $validator = new SignatureValidator($commander);

        try {

            $validationResult = $validator->validate();
        } catch (CspHandledException $e) {

            $decoder = new ErrorDecoder($validator->getLastErrorCode());

            if ($decoder->isInvalidMessageType()) {

                return $this->makeClientErrorResponse(
                    'Проверяемый файл не является встроенной подписью',
                    code: self::FILE_IS_NOT_INTERNAL_SIGN_ERROR_CODE
                );
            }

            if ($decoder->isIncorrectParameter()) {

                if ($fileSize / 1024 < 35) {

                    return $this->makeClientErrorResponse(
                        'Проверяемый файл является открепленной подписью',
                        code: self::FILE_IS_EXTERNAL_SIGN_ERROR_CODE
                    );
                }

                return $this->makeClientErrorResponse(
                    'Проверяемый файл подписи некорректен',
                    code: self::FILE_IS_INCORRECT_ERROR_CODE
                );
            }
            ExceptionContext::create(shortContext: [$e, 'Неизвестный тип ошибки обрабатываемой ошибки']);
        } catch (CspException $e) {
            ExceptionContext::create(shortContext: [$e, 'Внутренняя ошибка валидатора']);
        } catch (Throwable $e) {
            ExceptionContext::create(shortContext: [$e, 'Неизвестная ошибка валидатора']);
        }

        ExceptionContext::whenExist(function (ExceptionContext $ec) {
            $ec->message = 'Внутренняя ошибка при проверке встроенной подписи';
            $ec->throwServerException();
        });

        DB::beginTransaction();

        try {

            foreach ($validationResult->getSigners() as $signer) {

                $validationResultId = $this->saveModel(new FileValidationResult([
                    'signer' => $signer
                ]))->id;

                $this->saveModel(new FileInternalSign([
                    'validation_result_id'       => $validationResultId,
                    'internal_signature_file_id' => $fileId
                ]));
            }
        } catch (SaveModelException $e) {

            DB::rollBack();
            ExceptionContext::create('Ошибка при сохранении результатов проверки подписи в БД')
                ->addContextThrowable($e)
                ->throwServerException();
        }
        DB::commit();

        return $this->makeSuccessfulResponse(data: [
            'validationResult' => $validationResult
        ]);
    }
}
