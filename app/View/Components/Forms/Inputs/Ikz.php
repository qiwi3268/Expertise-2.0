<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


/**
 * Идентификационный код закупки
 *
 */
final class Ikz extends FormInput
{
    public string $maxLength = '36';
    public string $pattern = PatternLibrary::IKZ;
}
