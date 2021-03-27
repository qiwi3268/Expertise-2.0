<?php


namespace App\Http\ApiControllers\Miscs;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\AppRequest;

use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\Miscs\DependencyValidator as SelfValidator;
use App\Lib\Settings\Miscs\DependentMiscsManager;
use App\Repositories\Miscs\MiscRepository;


/*
 * Предоставляет данные о зависимых справочниках
 *
 */
final class DependencyController extends ApiController
{

    private int $selectedId;
    private array $subMiscAliases;
    private string $mainMiscAlias;



    /**
     * Конструктор класса
     *
     * @param AppRequest $req
     * @param SelfValidator $selfValidator
     */
    public function __construct(
        private AppRequest $req,
        private SelfValidator $selfValidator
    ) {
        // Валидация входных параметров
        $this->selfValidator->inputParametersValidation();

        $this->selectedId = $req->selectedId;
        $this->subMiscAliases = html_arr_decode($req->subMiscAliases);
        $this->mainMiscAlias = $req->mainMiscAlias;
    }


    /**
     * Основной метод
     *
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        $mgr = DependentMiscsManager::getInstance();

        $result = [];

        foreach ($this->subMiscAliases as $subMiscAlias) {

            $obj = $mgr->getObjectByAliases($this->mainMiscAlias, $subMiscAlias);
            $rep = new MiscRepository($obj->main['class']);

            $subResult = [
                'subAlias' => $subMiscAlias,
                'items'    => []
            ];

            $subCollection = $rep->getRelatedWhereActiveById($this->selectedId, $obj->main['relation']);

            foreach ($subCollection as $item) {

                $subResult['items'][] = [
                    'id'    => $item->id,
                    'label' => $item->label
                ];
            }
            $result[] = $subResult;
        }
        return $this->makeSuccessfulResponse(data: $result);
    }
}
