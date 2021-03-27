<?php

declare(strict_types=1);

namespace App\Lib\Files;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Files\File;
use App\Models\Files\FileExternalSign;
use App\Models\Files\FileInternalSign;

use App\Repositories\Files\FileInternalSignRepository;
use App\Repositories\Files\FileExternalSignRepository;


/**
 * Ищет и добавляет подписи к коллекции файлов
 *
 */
final class SignedFiles
{

    private FileInternalSignRepository $internalRep;
    private FileExternalSignRepository $externalRep;


    /**
     * Конструктор класса
     *
     * @param Collection $files
     */
    public function __construct(private Collection $files)
    {
        $this->internalRep = new FileInternalSignRepository;
        $this->externalRep = new FileExternalSignRepository;
    }


    /**
     * Предназначен для "прикрепления" подписей к файлам в их результат проверки ЭЦП
     *
     * @param bool $filter требуется ли удалять из исходной коллекции
     * файлы открепленных подписей
     */
    public function calculate(bool $filter = true): void
    {
        $ids = $this->files->pluck('id')->toArray();

        // Коллекция открепленных подписей
        $externalSigns = $this->externalRep->getByFileIds($ids);
        // Коллекция встроенных подписей
        $internalSigns = $this->internalRep->getByInternalSignatureFileIds($ids);

        // Удаляем те файлы, id которых присутствуют в списке открепленных подписей
        $files = $this->files->whereNotIn('id', $externalSigns->pluck('external_signature_file_id'));

        foreach ($files as $file) {

            /** @var File $file */
            /** @var Collection $i Коллекция открепленных подписей для текущего файла */
            /** @var Collection $e Коллекция встроенных подписей для текущего файла */

            $fileId = $file->id;

            // Находим подписи для текущего файла и одновременно перезаписываем исходную
            // коллекцию всех подписей, чтобы сократить последующие итерации
            [$i, $internalSigns] = $internalSigns->partition(function (FileInternalSign $sign) use ($fileId) {
                return $sign->internal_signature_file_id == $fileId;
            });
            [$e, $externalSigns] = $externalSigns->partition(function (FileExternalSign $sign) use ($fileId) {
                return $sign->file_id == $fileId;
            });

            $e->each(function (FileExternalSign $sign) use ($file) {
                $file->addSigner($sign->validationResult->signer);
            });
            $i->each(function (FileInternalSign $sign) use ($file) {
                $file->addSigner($sign->validationResult->signer);
            });
        }

        if ($filter) $this->files = $files;
    }

}
