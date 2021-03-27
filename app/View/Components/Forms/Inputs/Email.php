<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class Email extends FormInput
{
    public string $maxLength = '100';
    public string $pattern = PatternLibrary::EMAIL;
}
