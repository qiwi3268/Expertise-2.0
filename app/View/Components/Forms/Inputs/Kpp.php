<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Kpp extends FormInput
{
    public string $maxLength = '9';
    public string $pattern = PatternLibrary::KPP;
}
