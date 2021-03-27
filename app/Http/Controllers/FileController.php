<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\Lib\Filesystem\FilesystemException;

use App\Lib\Filesystem\StarPathHandler;
use App\Repositories\Files\FileRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class FileController extends Controller
{


    /**
     * Конструктор класса
     *
     * @param FileRepository $repository
     */
    public function __construct(
        private FileRepository $repository,
    ) {}


    /**
     * Отдает файл на скачивание
     *
     * @param string $starPath
     * @return BinaryFileResponse
     * @throws FilesystemException
     */
    public function download(string $starPath): BinaryFileResponse
    {
        $vo = StarPathHandler::createUnvalidated($starPath);

        $originalName = $this->repository->getOriginalNameByHashName($vo->getHashName());

        return response()->download($vo->getAbsPath(), $originalName);
    }
}
