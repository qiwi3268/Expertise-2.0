<?php

declare(strict_types=1);

namespace App\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\MySqlGrammar as BaseMySqlGrammar;


final class MySqlGrammar extends BaseMySqlGrammar
{

    /**
     * Хранение даты с микросекундами
     *
     * @return string
     */
    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s.u';
    }
}