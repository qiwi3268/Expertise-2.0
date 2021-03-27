<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Ogrn extends FormInput
{
    public string $maxLength = '13';
    public string $pattern = PatternLibrary::OGRN;
}
