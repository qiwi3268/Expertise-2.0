<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Dadata\Organizations;

use App\Http\ApiControllers\Dadata\DadataController;
use App\Repositories\Miscs\MiscRegionCodeRepository;


/*
 * Абстрактный класс для предоставления общих методов
 *
 */
abstract class OrganizationController extends DadataController
{


    /**
     * Заполяет адресные данные
     *
     * @param array $r ссылка на массив, в который будут записаны данные
     * @param array $data массив с исходными данными
     */
    protected function fillAddress(array &$r, array $data): void
    {
        //addressData
        $aD = $data['address']['data'];

        $r['postcode']          = $aD['postal_code'];
        $r['regionCode']        = $this->getMiscRegionCodeId($aD['region_kladr_id']);
        $r['municipalDistrict'] = $aD['area'];
        $r['city']              = $aD['city'];
        $r['locality']          = $aD['settlement'];
        $r['street']            = $aD['street_with_type'];
        $r['building']          = $aD['house'];
        $r['room']              = is_null($aD['flat_type_full']) ? $aD['flat'] : "{$aD['flat_type_full']} {$aD['flat']}";
    }


    /**
     * Возвращает id справочника кода региона
     *
     * @param string|null $kladr КЛАДР
     * @return int|null
     */
    protected function getMiscRegionCodeId(?string $kladr): ?int
    {
        if (is_null($kladr) || !pm('/^(\d{2}){1}\d+$/', $kladr, $code)) {
            return null;
        }
        return (new MiscRegionCodeRepository)->getIdByCode($code, false);
    }
}
