<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs\Dadata;

use App\Lib\Singles\PatternLibrary;


final class Bik extends DadataFormInput
{
    public string $maxLength = '9';
    public string $pattern = PatternLibrary::BIK;
    public string $dadataType = 'bik';
}
