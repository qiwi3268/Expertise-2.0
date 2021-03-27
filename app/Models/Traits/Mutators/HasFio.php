<?php

declare(strict_types=1);

namespace App\Models\Traits\Mutators;

use App\Lib\ValueObjects\Fio;


trait HasFio
{

    /**
     * Мутатор фио
     *
     * @param Fio $fio
     */
    public function setFioAttribute(Fio $fio): void
    {
        $this->last_name   = $fio->getLastName();
        $this->first_name  = $fio->getFirstName();
        $this->middle_name = $fio->getMiddleName();
    }


    /**
     * Аксессор фио
     *
     * @return Fio
     */
    public function getFioAttribute(): Fio
    {
        $this->existsAttributes(['last_name', 'first_name', 'middle_name']);
        return new Fio($this->last_name, $this->first_name, $this->middle_name);
    }
}
