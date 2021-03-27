<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs\Dadata;

use App\Lib\Singles\PatternLibrary;


final class PersInn extends DadataFormInput
{
    public string $maxLength = '12';
    public string $pattern = PatternLibrary::PERS_INN;
    public string $dadataType = 'persInn';
}
