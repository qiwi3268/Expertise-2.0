<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs\Dadata;

use App\Lib\Singles\PatternLibrary;


final class OrgInn extends DadataFormInput
{
    public string $maxLength = '10';
    public string $pattern = PatternLibrary::ORG_INN;
    public string $dadataType = 'orgInn';
}
