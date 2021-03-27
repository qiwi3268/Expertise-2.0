<?php

declare(strict_types=1);

namespace App\View\Utils;


final class MiscItem
{

    /**
     * Конструктор класса
     *
     * @param int $id
     * @param string $label
     */
    public function __construct(
        public int $id,
        public string $label
    ) {}
}