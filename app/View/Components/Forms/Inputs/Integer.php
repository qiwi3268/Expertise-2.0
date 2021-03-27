<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Integer extends FormInput
{
    public string $maxLength = '10';
    public string $pattern = PatternLibrary::INTEGER;
}