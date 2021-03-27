<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Decimal extends FormInput
{
    public string $maxLength = '18';
    public string $pattern = PatternLibrary::DECIMAL;
}