<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Snils extends FormInput
{
    public string $maxLength = '14';
    public string $pattern = PatternLibrary::SNILS;
}
