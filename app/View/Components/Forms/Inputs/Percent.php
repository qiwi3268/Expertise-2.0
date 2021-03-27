<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Percent extends FormInput
{
    public string $maxLength = '3';
    public string $pattern = PatternLibrary::PERCENT;
}
