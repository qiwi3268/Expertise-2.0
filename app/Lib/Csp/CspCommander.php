<?php

declare(strict_types=1);

namespace App\Lib\Csp;


/**
 * Общий тип для всех команд приложения
 *
 */
abstract class CspCommander
{

    /**
     * Путь в ФС сервера к утилите cryptcp
     *
     */
    protected const CPROCSP = '/opt/cprocsp/bin/amd64/cryptcp';


    /**
     * Путь в ФС сервера к утилите certmgr
     *
     */
    protected const CERTMGR = '/opt/cprocsp/bin/amd64/certmgr';
}
