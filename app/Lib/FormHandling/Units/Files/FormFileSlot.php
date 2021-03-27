<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Files;

use App\Exceptions\Lib\FormHandling\FileBoxException;

use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Lib\FormHandling\Units\FormUnit;
use App\Lib\FormHandling\Units\Utils\FileBox;


/**
 * Слот файлов
 *
 */
final class FormFileSlot extends FormUnit
{
    private FileBox $fileBox;
    private ?bool $result;
    private array $starPaths;


    /**
     * Конструктор класса
     *
     * @param string $snakeMappings
     * @param string $name
     * @param bool $required
     * @throws FileBoxException
     */
    public function __construct(
        private string $snakeMappings,
        string $name,
        bool $required = true
    ) {
        parent::__construct($name, $required);

        $this->fileBox = FileBox::getInstance();
        [$this->result, $this->starPaths] = $this->fileBox->getSection($snakeMappings);
        $this->filled = count($this->starPaths) > 0;
    }


    /**
     * Имеются ли ошибки при проверке ЭЦП
     *
     * @return bool
     * @throws FormLogicException
     */
    public function hasSignatureValidationError(): bool
    {
        return $this->result ??
            throw new FormLogicException('Отсутствуют загруженные в слот файлы');
    }
}