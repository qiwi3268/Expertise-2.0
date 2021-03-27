<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Ogrnip extends FormInput
{
    public string $maxLength = '15';
    public string $pattern = PatternLibrary::OGRNIP;
}
