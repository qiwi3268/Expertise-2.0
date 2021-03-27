<?php


namespace App\ApiServices\FormBlocks;

use App\Exceptions\Lib\FormHandling\InvalidFormUnitException;

use App\Lib\FormHandling\Units\Items\Toggle;
use App\Lib\FormHandling\Units\Items\Texts\Name;
use App\Lib\FormHandling\Units\Items\Texts\Postcode;
use App\Lib\FormHandling\Units\Items\Texts\Text;
use App\Lib\FormHandling\Units\Items\Texts\Phone;
use App\Lib\FormHandling\Units\Items\Texts\Email;
use App\Lib\FormHandling\Units\Items\Texts\Kpp;
use App\Lib\FormHandling\Units\Items\Texts\OrgInn;
use App\Lib\FormHandling\Units\Items\Texts\Ogrn;
use App\Lib\FormHandling\Units\Items\Texts\Passport;
use App\Lib\FormHandling\Units\Items\Miscs\SingleMisc;
use App\Lib\FormHandling\Units\Items\Dates\PastDate;
use App\Lib\FormHandling\Units\Items\Dates\FutureDate;

use App\Lib\Singles\Strings\NotationTransformer;
use App\Lib\Singles\Strings\Prefix;
use App\Lib\Singles\Arrays\ObjectAccess;


final class FormTemplater
{

    /**
     * Шаблон адреса
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @param bool $withTextMunicipalDistrict наименование района - необязательное текстовое поле.
     * В противном случае - справочник
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function address(ObjectAccess $o, Prefix $p, bool $withTextMunicipalDistrict = true): array
    {
        $arr = [
            'postcode'                => new Postcode($o->postcode, $p('Почтовый индекс')),
            'misc_region_code_id'     => new SingleMisc($o->regionCode, $p('Субъект Российской Федерации'), 'regionCode'),
            'city'                    => new Text($o->city, $p('Город'), 200, false),
            'locality'                => new Text($o->locality, $p('Населенный пункт'), 200, false),
            'street'                  => new Text($o->street, $p('Улица'), 200, false),
            'building'                => new Text($o->building, $p('Номер здания/сооружения'), 200, false),
            'room'                    => new Text($o->room, $p('Номер помещения'), 200, false),
            'address_additional_info' => new Text($o->addressAdditionalInfo, $p('Дополнительные адресные данные'), 1500, false),
        ];

        if ($withTextMunicipalDistrict) {
            $arr['municipal_district'] = new Text($o->municipalDistrict, $p('Наименование района'), 200, false);
        } else {
            $arr['misc_municipal_district_id'] = new SingleMisc($o->municipalDistrict, $p('Наименование района'), 'municipalDistrict');
        }
        return $arr;
    }


    /**
     * Шаблон сведений об организации
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function organization(ObjectAccess $o, Prefix $p): array
    {
        return arr_assoc_merge([
            [
                'full_name' => new Text($o->fullName, $p('Полное наименование'), 1000),
                'ogrn'      => new Ogrn($o->ogrn, $p('ОГРН')),
                'org_inn'   => new OrgInn($o->orgInn, $p('ИНН')),
                'kpp'       => new Kpp($o->kpp, $p('КПП'))
            ],
            self::contact($o, $p)
        ]);
    }


    /**
     * Шаблон контактных данных. Почта и телефон
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function contact(ObjectAccess $o, Prefix $p): array
    {
        return [
            'email' => new Email($o->email, $p('Адрес электронной почты')),
            'phone' => new Phone($o->phone, $p('Телефон')),
        ];
    }


    /**
     * Шаблон фио
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @param string|null $propertyPrefix
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function fio(ObjectAccess $o, Prefix $p, ?string $propertyPrefix = null): array
    {
        $last = new NotationTransformer($propertyPrefix, 'lastName');
        $first = new NotationTransformer($propertyPrefix, 'firstName');
        $middle = new NotationTransformer($propertyPrefix, 'middleName');

        return [
            $last->toNumericSnake()   => new Name($o->{$last->toCamel()}, $p('Фамилия')),
            $first->toNumericSnake()  => new Name($o->{$first->toCamel()}, $p('Имя')),
            $middle->toNumericSnake() => new Name($o->{$middle->toCamel()}, $p('Отчество'), false),
        ];
    }


    /**
     * Шаблон фио с должностью
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @param string|null $propertyPrefix
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function fioWithPost(ObjectAccess $o, Prefix $p, ?string $propertyPrefix = null): array
    {
        $post = new NotationTransformer($propertyPrefix, 'post');

        return arr_assoc_merge([
            self::fio($o, $p, $propertyPrefix),
            [$post->toNumericSnake() => new Text($o->{$post->toCamel()}, $p('Должность'), 300)]
        ]);
    }


    /**
     * Шаблон паспорта
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function passport(ObjectAccess $o, Prefix $p): array
    {
        return [
            'passport'            => new Passport($o->passport, $p('Серия и номер паспорта')),
            'passport_issuer'     => new Text($o->passportIssuer, $p('Кем выдан'), 1000),
            'passport_issue_date' => new PastDate($o->passportIssueDate, $p('Когда')),
        ];
    }


    /**
     * Шаблон документов, подтверждающих полномочия
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function credentials(ObjectAccess $o, Prefix $p): array
    {
        return [
            'misc_legal_basis_id' => new SingleMisc($o->legalBasis, $p('Действует на основании'), 'legalBasis'),
            'signer_basis_number' => new Text($o->signerBasisNumber, $p('Номер'), 100),
            'signer_basis_date'   => new PastDate($o->signerBasisDate, $p('Дата')),
        ];
    }


    /**
     * Сведения о доверенности
     *
     * Не включает в себя лицо, выдавшее доверенность
     *
     * @param ObjectAccess $o
     * @param Prefix $p
     * @return array
     * @throws InvalidFormUnitException
     */
    public static function legalBasisDetails(ObjectAccess $o, Prefix $p): array
    {
        return [
            'legal_basis_duration'    => new FutureDate($o->legalBasisDuration, $p('Срок действия')),
            'can_entrust_legal_basis' => new Toggle($o->canEntrustLegalBasis, $p('Возможность передоверия')),
        ];
    }

}