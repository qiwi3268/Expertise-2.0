<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Postcode extends FormInput
{
    public string $maxLength = '6';
    public string $pattern = PatternLibrary::POSTCODE;
}
