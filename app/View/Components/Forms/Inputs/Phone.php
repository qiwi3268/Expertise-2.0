<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Phone extends FormInput
{
    public string $maxLength = '25';
    public string $pattern = PatternLibrary::PHONE;
}
