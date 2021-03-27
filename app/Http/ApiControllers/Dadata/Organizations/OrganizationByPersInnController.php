<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Dadata\Organizations;

use App\Exceptions\ExternalApiException;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\AppRequest;
use App\Lib\ValueObjects\Fio;
use App\ApiServices\Validation\Dadata\Organizations\OrganizationByPersInnValidator as SelfValidator;


/*
 * Предоставляет данные об организации по ИНН индивидуального предпринимателя из api dadata
 *
 */
final class OrganizationByPersInnController extends OrganizationController
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

            $info = $this->dadata->getOrganizationInfoByPersInn($this->req->persInn);

            if (!is_null($info)) {

                $info = $info['data'];

                $data = [
                    'persInn' => $info['inn'],
                    'ogrnip'  => $info['ogrn'],
                ];

                // У ИП нет свойства с отдельным фио, поэтому отделяем часть строки в виде ИП/ГКФ/ГКФХ и др.
                if (
                    !pm('/^[А-ЯЁ]+\s(.*)$/u', (string) $info['name']['short_with_opf'], $fioString)
                    || is_null($fioArray = Fio::parseString($fioString))
                ) {
                    $fioArray = [null, null, null];
                }

                [
                    $data['lastName'],
                    $data['firstName'],
                    $data['middleName']
                ] = $fioArray;

                $this->fillAddress($data, $info);

                $result = ['found' => true, 'data' => $data];
            }
        }
        return $this->makeSuccessfulResponse(data: $result);
    }
}
