<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use App\Lib\Singles\PatternLibrary;


final class CorrespondentAccount extends FormInput
{
    public string $maxLength = '20';
    public string $pattern = PatternLibrary::CORRESPONDENT_ACCOUNT;
}
