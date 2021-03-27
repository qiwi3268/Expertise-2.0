<?php

declare(strict_types=1);

namespace App\Lib\Images\SignatureStamps;

use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

use App\Lib\Csp\Validation\ValueObjects\Public\Signer;


final class StampManager
{
    private const TEMPLATE_NO_LOGO = 'signatureStampNoLogo.png';
    private const TEMPLATE_WITH_LOGO = 'signatureStampWithLogo.png';

    /**
     * Расширение оттиска, который будет сгенерирован
     *
     */
    private const EXTENSION = 'png';

    private FilesystemAdapter $templateAdapter;
    private FilesystemAdapter $fileAdapter;


    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $this->templateAdapter = Storage::disk('signature_stamp_template');
        $this->fileAdapter = Storage::disk('signature_stamp_file');
    }


    /**
     * Возвращает абсолютный путь в ФС сервера к файлу оттиска
     *
     * В случае, если оттиск уже был сгенирирован с другим шаблоном,
     * то новой генерации не будет
     *
     * @param Signer $signer
     * @param bool $withLogo
     * @return string
     */
    public function getStampPath(Signer $signer, bool $withLogo): string
    {
        $certificate = $signer->getCertificate();
        $file = "{$certificate->getSerial()}." . self::EXTENSION;
        $filePath = $this->fileAdapter->path($file);

        if ($this->fileAdapter->missing($file)) {

            $templatePath = $this->templateAdapter->path(
                $withLogo
                    ? self::TEMPLATE_WITH_LOGO
                    : self::TEMPLATE_NO_LOGO
            );

            $maker = new StampMaker($templatePath);

            $maker->generate(
                $certificate->getSerial(),
                $signer->getFio()->getLongFio(),
                $certificate->getValidRange()
            )->save($filePath);
        }
        return $filePath;
    }
}