<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Dadata\Banks;

use App\Exceptions\ExternalApiException;

use Illuminate\Http\JsonResponse;

use App\Http\ApiControllers\Dadata\DadataController;
use App\Http\Requests\AppRequest;
use App\ApiServices\Validation\Dadata\Banks\BankByBikValidator as SelfValidator;


/*
 * Предоставляет данные о банке по его БИК
 *
 */
final class BankByBikController extends DadataController
{

    /**
     * Конструктор класса
     *
     * @param AppRequest $req
     * @param SelfValidator $selfValidator
     */
    public function __construct(private AppRequest $req, SelfValidator $selfValidator)
    {
        // Валидация входных параметров
        $selfValidator->inputParametersValidation();

        parent::__construct();
    }


    /**
     * Основной метод
     *
     * @return JsonResponse
     * @throws ExternalApiException
     */
    public function show(): JsonResponse
    {
        $result['found'] = false;

        if ($this->canSendRequest) {

            $info = $this->dadata->getBankInfoByBik($this->req->bik);

            if (!is_null($info)) {

                $data = [
                    'name' => $info['value'],
                ];
                $result = ['found' => true, 'data' => $data];
            }
        }
        return $this->makeSuccessfulResponse(data: $result);
    }
}
