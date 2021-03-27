<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;


final class Input extends FormInput
{

    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $name
     * @param string $required
     * @param string $maxLength
     * @param string|null $pattern
     */
    public function __construct(
        string $title,
        string $name,
        string $required,
        public string $maxLength = '1000',
        public ?string $pattern = null
    ) {
        parent::__construct($title, $name, $required);
    }
}
