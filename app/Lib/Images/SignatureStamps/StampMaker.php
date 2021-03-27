<?php

declare(strict_types=1);

namespace App\Lib\Images\SignatureStamps;

use RuntimeException;
use LogicException;
use Intervention\Image\Exception\ImageException;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\AbstractFont;


final class StampMaker
{

    private ImageManager $manager;
    private Image $image;


    /**
     * Конструктор класса
     *
     * @param string $templatePath абсолютный путь в ФС сервера к файлу шаблона
     */
    public function __construct(private string $templatePath)
    {
        assertion()->isFile($templatePath);

        $this->manager = new ImageManager([
            'driver' => 'gd'
        ]);
    }


    /**
     * Создает объект изображения и вносит в него данные
     *
     * @param string $serial
     * @param string $longFio
     * @param string $validRange
     * @return $this
     * @throws RuntimeException
     */
    public function generate(
        string $serial,
        string $longFio,
        string $validRange
    ): self {

        try {
            $image = $this->manager->make($this->templatePath);
        } catch (ImageException $e) {
            throw new RuntimeException("Ошибка при создании объекта изображения из пути: '{$this->templatePath}'", 0, $e);
        }

        $this->addData($image, $serial, $longFio, $validRange);

        $this->image = $image;

        return $this;
    }


    /**
     * Сохраняет изображение
     *
     * @param string $path абсолютный путь в ФС сервера, по которому будет сохранено изображения.
     * Путь включает в себя расширение файла
     * @throws RuntimeException
     * @throws LogicException
     */
    public function save(string $path): void
    {
        if (!isset($this->image)) {
            throw new LogicException('Объект изображения не инициализирован');
        }
        try {
            $this->image->save($path);
        } catch (ImageException $e) {
            throw new RuntimeException("Ошибка при сохранении объекта изображения по пути: '{$path}'", 0, $e);
        }
    }


    /**
     * Добавляет данные в изображение
     *
     * @param Image $image
     * @param string $serial
     * @param string $longFio
     * @param string $validRange
     */
    private function addData(
        Image $image,
        string $serial,
        string $longFio,
        string $validRange
    ): void {

        $image->text($serial, 280, 308, function (AbstractFont $font) {
            $this->setFontSettings($font);
        });

        $image->text($longFio, 280, 366, function (AbstractFont $font) {
            $this->setFontSettings($font);
        });

        $image->text($validRange, 280, 431, function (AbstractFont $font) {
            $this->setFontSettings($font);
        });
    }


    /**
     * Устанавливает настройки шрифта
     *
     * @param AbstractFont $font
     */
    private function setFontSettings(AbstractFont $font): void
    {
        $ttf = public_path('fonts/Calibri_light.ttf');
        assertion()->isFile($ttf);

        $font->file = $ttf;
        $font->size(30);
        $font->color('#3b4770');
    }
}