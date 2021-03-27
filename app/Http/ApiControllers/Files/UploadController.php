<?php


namespace App\Http\ApiControllers\Files;

use Throwable;
use Exception;
use App\Exceptions\Repositories\ResultDoesNotExistException;
use App\Exceptions\Api\ExceptionContext;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\AppRequest;

use App\Http\ApiControllers\ApiController;
use App\Lib\Settings\FileMappingsManager;
use App\Lib\Filesystem\StarPathHandler;
use App\Repositories\Sys\SysModelClassNameRepository;
use App\Repositories\Sys\SysFilesystemDiskRepository;
use App\Repositories\Sys\SysFileMappingsRepository;

use App\ApiServices\Validation\Files\UploadValidator as SelfValidator;
use App\ApiServices\FileUploading\FileUploader;
use App\ApiServices\FileUploading\UploadedFilesStorage;
use App\Models\Files\File;


/*
 * Представляет единую точку загрузки файлов
 *
 */
final class UploadController extends ApiController
{

    private string $snakeMappings;
    private int $targetDocumentId;

    /**
     * Загруженные в запросе файлы
     * @var UploadedFile[]
     */
    private array $files;


    /**
     * Конструктор класса
     *
     * @param AppRequest $req
     * @param SelfValidator $selfValidator
     * @param UploadedFilesStorage $uploadedFilesStorage
     * @param SysFilesystemDiskRepository $filesystemDiskRepository
     * @param SysFileMappingsRepository $fileMappingsRepository
     * @param SysModelClassNameRepository $modelClassNameRepository
     */
    public function __construct(
        private AppRequest $req,
        private SelfValidator $selfValidator,
        private UploadedFilesStorage $uploadedFilesStorage,
        private SysFilesystemDiskRepository $filesystemDiskRepository,
        private SysFileMappingsRepository $fileMappingsRepository,
        private SysModelClassNameRepository $modelClassNameRepository
    ) {
        // Валидация общих входных параметров
        $selfValidator->commonInputParametersValidation();

        // Инициализация входных параметров
        $this->snakeMappings = $req->mappings;
        $this->targetDocumentId = $req->targetDocumentId;

        /** @var array $files */
        $files = $req->file('files');
        $this->files = $files;
    }


    /**
     * Основной метод загрузки файлов
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function upload(): JsonResponse
    {
        $mgr = new FileMappingsManager($this->snakeMappings);

        $targetDocumentType = $mgr->getDocumentType();
        $documentRepository = doc()->getRepositoryByDocumentType($targetDocumentType);
        $documentClassName = doc()->getModelClassNameByDocumentType($targetDocumentType);

        // Проверка существования целевого документа
        $this->existsById($documentRepository, $this->targetDocumentId);

        $uploaderClassName = $mgr->getUploaderClassName();

        /** @var FileUploader $uploader */
        $uploader = new $uploaderClassName(
            $this->req->except(['files', 'targetDocumentId']),
            $this->targetDocumentId,
            $targetDocumentType,
        );


        // Блок валидации
        $this->selfValidator->uploaderInputParametersValidation($uploader->getInputParametersValidationRules());
        $this->selfValidator->serverUploadedFilesValidation();
        $this->selfValidator->uploaderFilesValidation($uploader->getFilesValidationRules());

        // Параметры файлового хранилища
        $storageParameters = $uploader->getStorageParameters();
        $storage = $storageParameters->getFilesystemAdapter();

        // Предзаполненная модель с общими данными для быстрого копирования
        try {

            $templateModel = new File([
                'doc_id'                 => $this->targetDocumentId,
                'doc_type'               => $this->modelClassNameRepository->getIdByClassName($documentClassName),
                'user_id'                => 1,
                'sys_filesystem_disk_id' => $this->filesystemDiskRepository->getIdByName($storageParameters->getDiskName()),
                'sub_directory'          => $storageParameters->getSubDirectory(),
                'sys_file_mappings_id'   => $this->fileMappingsRepository->getIdBySnakeMappings($this->snakeMappings),
            ]);
        } catch (ResultDoesNotExistException) {

            ExceptionContext::create('В БД отсутствуют данные синхронизации настроек')
                ->addContext('target_document_type', $targetDocumentType)
                ->addContext('disk_name', $storageParameters->getDiskName())
                ->addContext('snake_mappings', $this->snakeMappings)
                ->throwServerException();
        }

        DB::beginTransaction();

        try {

            foreach ($this->files as $file) {

                $hashName = Str::uuid()->toString();

                if (!$storage->putFileAs($storageParameters->getSubDirectory(), $file, $hashName)) {
                     throw new Exception("Метод 'putFileAs' вернул false");
                }

                /** @var File $model */
                $model = $this->saveModel($templateModel->replicate()->fill([
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                    'hash_name'     => $hashName
                ]));

                $this->uploadedFilesStorage->attachFile(
                    $model,
                    StarPathHandler::createString($storageParameters, $hashName)
                );
            }
            // Обработка данных загрузчиком
            $uploader->processStorage($this->uploadedFilesStorage);
        } catch (Throwable $e) {

            DB::rollBack();
            ExceptionContext::create('Ошибка при сохранении файла')
                ->addContextThrowable($e)
                ->addContext('data_array', $this->uploadedFilesStorage->getDataArray())
                ->throwServerException();
        }
        DB::commit();
        return $this->makeSuccessfulResponse(data: $this->uploadedFilesStorage->getDataArray());
    }
}
