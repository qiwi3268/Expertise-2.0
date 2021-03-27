<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Dadata\Organizations;

use App\Exceptions\ExternalApiException;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\AppRequest;
use App\Lib\ValueObjects\Fio;
use App\ApiServices\Validation\Dadata\Organizations\OrganizationByOrgInnValidator as SelfValidator;


/*
 * Предоставляет данные об организации по ИНН юридического лица из api dadata
 *
 */
final class OrganizationByOrgInnController extends OrganizationController
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

            $info = $this->dadata->getOrganizationInfoByOrgInn($this->req->orgInn);

            if (!is_null($info)) {

                $info = $info['data'];

                $data = [
                    'orgInn'       => $info['inn'],
                    'fullName'     => $info['name']['full_with_opf'],
                    'shortName'    => $info['name']['short_with_opf'],
                    'ogrn'         => $info['ogrn'],
                    'kpp'          => $info['kpp'],
                    'directorPost' => $info['management']['post'],
                ];

                [
                    $data['directorLastName'],
                    $data['directorFirstName'],
                    $data['directorMiddleName']
                ] = Fio::parseString((string) $info['management']['name']) ?? [null, null, null];

                $this->fillAddress($data, $info);

                $result = ['found' => true, 'data' => $data];
            }
        }
        return $this->makeSuccessfulResponse(data: $result);
    }
}
