<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\View\Utils\FilesHelper;


final class File extends Component
{

    public string $maxFileSize;
    public string $allowableExtensions;
    public string $forbiddenSymbols;


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $snakeMappings
     * @param string $required
     * @param string $multiple
     * @param string $minColor
     */
    public function __construct(
        public string $title,
        public string $snakeMappings,
        public string $required,
        public string $multiple,
        public string $minColor = 'orange'
    ) {
        [
            $this->maxFileSize,
            $this->allowableExtensions,
            $this->forbiddenSymbols
        ] = FilesHelper::getRules($snakeMappings);
    }


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.forms.file');
    }
}
