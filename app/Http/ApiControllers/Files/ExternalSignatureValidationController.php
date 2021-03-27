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
use App\Models\Files\FileExternalSign;

use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\Files\ExternalSignatureValidationValidator as SelfValidator;
use App\Lib\Filesystem\StarPathHandler;
use App\Lib\Filesystem\StarPath;
use App\Repositories\Files\FileRepository;

use App\Lib\Csp\Validation\Commands\ExternalSignatureCommander;
use App\Lib\Csp\Validation\SignatureValidator;
use App\Lib\Csp\Validation\ErrorDecoder;


/*
 * Предназначен для обработки файла с открепленной подписью
 *
 */
final class ExternalSignatureValidationController extends ApiController
{

    /**
     * Проверяемый файл не является открепленной
     */
    private const FILE_IS_NOT_EXTERNAL_SIGN_ERROR_CODE = 'finesec';

    private StarPath $originalStarPath;
    private StarPath $externalSignatureStarPath;


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

        $this->originalStarPath = StarPathHandler::createUnvalidated($req->originalStarPath);
        $this->externalSignatureStarPath = StarPathHandler::createUnvalidated($req->externalSignatureStarPath);
    }


    /**
     * Основной метод
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function validateSignature(): JsonResponse
    {
        $originalFileId = $this->fileRepository->getIdByHashName($this->originalStarPath->getHashName());

        [$externalSignatureFileId, $externalSignatureFileSize] = $this->fileRepository->getIdAndFileSizeByHashName(
            $this->externalSignatureStarPath->getHashName()
        );

        if ($externalSignatureFileSize / 1024 > 35) {
            return $this->makeClientErrorResponse(
                'Проверяемый файл не является открепленной подписью',
                code: self::FILE_IS_NOT_EXTERNAL_SIGN_ERROR_CODE
            );
        }

        $commander = new ExternalSignatureCommander(
            $this->originalStarPath->getAbsPath(),
            $this->externalSignatureStarPath->getAbsPath()
        );

        $validator = new SignatureValidator($commander);

        try {

            $validationResult = $validator->validate();
        } catch (CspHandledException $e) {

            $decoder = new ErrorDecoder($validator->getLastErrorCode());

            if ($decoder->isSignatureVerifyingNotStarted()) {

                return $this->makeClientErrorResponse(
                    'Проверяемый файл не является открепленной подписью',
                    code: self::FILE_IS_NOT_EXTERNAL_SIGN_ERROR_CODE
                );
            }

            ExceptionContext::create(shortContext: [$e, 'Неизвестный тип ошибки обрабатываемой ошибки']);
        } catch (CspException $e) {
            ExceptionContext::create(shortContext: [$e, 'Внутренняя ошибка валидатора']);
        } catch (Throwable $e) {
            ExceptionContext::create(shortContext: [$e, 'Неизвестная ошибка валидатора']);
        }

        ExceptionContext::whenExist(function (ExceptionContext $ec) {
            $ec->message = 'Внутренняя ошибка при проверке открепленной подписи';
            $ec->throwServerException();
        });

        DB::beginTransaction();

        try {

            // Открепленная подпись имеет только одного подписанта
            $validationResultId = $this->saveModel(new FileValidationResult([
                'signer' => $validationResult->getSigners()[0],
            ]))->id;

            $this->saveModel(new FileExternalSign([
                'validation_result_id'       => $validationResultId,
                'file_id'                    => $originalFileId,
                'external_signature_file_id' => $externalSignatureFileId
            ]));
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
