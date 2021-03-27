<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Passport extends FormInput
{
    public string $maxLength = '11';
    public string $pattern = PatternLibrary::PASSPORT;
}
