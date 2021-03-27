<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use App\Lib\Singles\NodeStructure;
use App\Lib\Singles\TypeOfObjectBridge;
use App\View\Utils\FilesHelper;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;


final class DocumentationFiles extends Component
{

    public Collection $structure;
    public string $maxFileSize;
    public string $allowableExtensions;
    public string $forbiddenSymbols;


    /**
     * Инициализирует сущность компонента
     *
     * @param string $snakeMappings
     * @param string $minColor
     */
    public function __construct(
        public string $snakeMappings,
        public string $minColor = 'orange'
    ) {
        $rep = TypeOfObjectBridge::createByDocumentationMappings($snakeMappings)
            ->getStructureDocumentationRepository();

        $this->structure = $rep->getAllWhereActive();

        NodeStructure::calculateDepthStructure($this->structure);

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
        return view('x-components.forms.documentation-files');
    }
}
