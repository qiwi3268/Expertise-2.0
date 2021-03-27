<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Files;

use App\Exceptions\Lib\Csp\CspParsingException;
use League\Flysystem\FileNotFoundException;
use App\Exceptions\Api\ExceptionContext;
use App\Exceptions\Lib\Filesystem\FilesystemException;

use Illuminate\Http\JsonResponse;
use Symfony\Component\Process\Process;

use App\Http\Requests\AppRequest;

use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\Files\HashValidator as SelfValidator;
use App\Lib\Filesystem\StarPathHandler;
use App\Lib\Filesystem\StarPath;
use App\Lib\Filesystem\SimpleStorageParameters;
use App\Lib\Csp\AlgorithmsManager;
use App\Lib\Csp\Hashing\Commands\HashCommander;
use App\Lib\Csp\MessageParser;


/*
 * Предназначен для создания hash'a для исходного файла
 *
 */
final class HashController extends ApiController
{

    private string $hashAlg;
    private StarPath $starPath;


    /**
     * Конструктор класса
     *
     * @param AppRequest $req
     * @param SelfValidator $selfValidator
     * @throws FilesystemException
     */
    public function __construct(
        private AppRequest $req,
        private SelfValidator $selfValidator,
    ) {
        // Валидация входных параметров
        $this->selfValidator->inputParametersValidation();

        $this->hashAlg = AlgorithmsManager::getHashBySignAlgorithm($req->signAlgorithm);

        $this->starPath = StarPathHandler::createUnvalidated($req->starPath);
    }


    /**
     * Основной метод
     *
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function hash(): JsonResponse
    {
        $filePath = $this->starPath->getAbsPath();

        $storageParameters = new SimpleStorageParameters('tmp');

        // Выполнение shell команды и получение результата
        $commander = new HashCommander($storageParameters->getDiscPath(), $this->hashAlg, $filePath);
        $process = new Process($commander->getCommand());
        $process->run();
        $message = $process->getOutput();

        // Проверка результирующего сообщения
        try {

            if (!(new MessageParser)->isOkErrorCode($message)) {

                ExceptionContext::create(shortContext: ['Код ошибки не соответствует успешному выполнению команды']);
            }
        } catch (CspParsingException) {

            ExceptionContext::create(shortContext: ['В сообщении отсутствует ErrorCode']);
        }

        ExceptionContext::whenExist(function (ExceptionContext $ec) use ($message) {
            $ec->setMessage('Ошибка при выполнении shell команды')
                ->addContext('output', $message)
                ->throwServerException();
        });

        $hashFileName = $this->starPath->getHashName() . '.hsh';

        $storage = $storageParameters->getFilesystemAdapter();

        if ($storage->missing($hashFileName)) {
            ExceptionContext::create('Ошибка при выполнении shell команды')
                ->addContextDescription('Отсутствует сгенерированный hash файл')
                ->throwServerException();
        }

        $hash = $storage->readAndDelete($hashFileName);

        if (!$hash) {
            ExceptionContext::create('Ошибка при выполнении shell команды')
                ->addContextDescription('Ошибка при чтении и удалении hash файла')
                ->throwServerException();
        }

        return $this->makeSuccessfulResponse(data: [
            'hash' => $hash
        ]);
    }
}
