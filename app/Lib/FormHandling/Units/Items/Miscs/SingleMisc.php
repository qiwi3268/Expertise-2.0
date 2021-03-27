<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Miscs;

use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;

use App\Repositories\Miscs\MiscRepository;


final class SingleMisc extends MiscItem
{


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     * @throws FormInvalidArgumentException
     */
    protected function validate(string $value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        $rep = new MiscRepository($this->miscClassName);

        return $rep->existsById((int) $value);
    }
}