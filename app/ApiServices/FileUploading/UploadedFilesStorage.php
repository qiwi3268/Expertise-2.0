<?php


namespace App\ApiServices\FileUploading;

use LogicException;
use InvalidArgumentException;

use Illuminate\Support\Arr;
use App\Models\Files\File;
use App\Lib\Singles\FileUtils;

use SplObjectStorage;


/**
 * Представляет собой хранилище загруженных файлов
 *
 * Содержит связь созданной файловой модели и ассоциированные к ней данные,
 * которые будут содержаться в json ответе
 *
 */
final class UploadedFilesStorage
{

    private SplObjectStorage $storage;


    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $this->storage = new SplObjectStorage;
    }


    /**
     * Добавляет модель созданной записи файла в контейнер
     *
     * @param File $fileModel
     * @param string $starPath
     * @throws InvalidArgumentException
     */
    public function attachFile(File $fileModel, string $starPath): void
    {
        if ($this->storage->contains($fileModel)) {
            throw new InvalidArgumentException('Модель файла уже содержится в хранилище');
        }
        $fileModel->existsAttributes(['original_name', 'file_size']);

        $this->storage->attach($fileModel, [
            'starPath'      => $starPath,
            'originalName'  => $fileModel->original_name,
            'humanFileSize' => FileUtils::getHumanFileSize($fileModel->file_size)
        ]);
    }


    /**
     * Возвращает хранилище с целью последующей итерации
     *
     * @return SplObjectStorage
     */
    public function getStorage(): SplObjectStorage
    {
        return $this->storage;
    }


    /**
     * Добавляет свойство к текущему итерируемому объекту
     *
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function addProperty(string $key, mixed $value): void
    {
        $info = $this->storage->getInfo();

        if (array_key_exists($key, $info)) {
            throw new InvalidArgumentException("Ассоциированные данные по ключу: '{$key}' уже существуют к текущему файлу");
        }
        $info[$key] = $value;
        $this->storage->setInfo($info);
    }


    /**
     * Возвращает массив с ассоциированными данными
     *
     * Проверяет, чтобы все массивы имели одинаковую структуру
     *
     * @return array
     * @throws LogicException
     */
    public function getDataArray(): array
    {
        $info = [];

        $s = $this->storage;
        $s->rewind();

        // Получение всех ассоциированных данных
        while ($s->valid()) {
            $info[] = $s->getInfo();
            $s->next();
        }

        if (Arr::hasDiffKeys($info)) {
            throw new LogicException('Ассоциированные данные имеют расхождение');
        }
        return $info;
    }
}
