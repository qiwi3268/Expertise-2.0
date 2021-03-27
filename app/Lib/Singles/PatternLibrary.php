<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use LogicException;
use InvalidArgumentException;


/**
 * Библиотека используемых паттернов
 *
 * @method static bool org_inn(string $subject)
 * @method static bool bik(string $subject)
 * @method static bool ikz(string $subject)
 * @method static bool checking_account(string $subject)
 * @method static bool correspondent_account(string $subject)
 * @method static bool pers_inn(string $subject)
 * @method static bool kpp(string $subject)
 * @method static bool ogrn(string $subject)
 * @method static bool snils(string $subject)
 * @method static bool ogrnip(string $subject)
 * @method static bool passport(string $subject)
 * @method static bool postcode(string $subject)
 * @method static bool date(string $subject)
 * @method static bool email(string $subject)
 * @method static bool name(string $subject)
 * @method static bool percent(string $subject)
 * @method static bool phone(string $subject)
 * @method static bool integer(string $subject)
 * @method static bool decimal(string $subject)
 */
final class PatternLibrary
{
    /** ИНН юридического лица */
    public const ORG_INN = '/^\d{10}$/';
    /** Банковский идентификационный код */
    public const BIK = '/^\d{9}$/';
    /** Идентификационный код закупки */
    public const IKZ = '/^\d{36}$/';
    /** Расчетный счет */
    public const CHECKING_ACCOUNT = '/^\d{20}$/';
    /** Корреспондентский/казначейский счет */
    public const CORRESPONDENT_ACCOUNT = '/^\d{20}$/';
    /** ИНН индивидуального предпринимателя */
    public const PERS_INN = '/^\d{12}$/';
    public const KPP      = '/^\d{9}$/';
    public const OGRN     = '/^\d{13}$/';
    public const SNILS    = '/^[0-9]{3}-[0-9]{3}-[0-9]{3} [0-9]{2}$/';
    public const OGRNIP   = '/^\d{15}$/';
    /** Серия и номер паспорта через тире */
    public const PASSPORT = '/^[0-9]{4}\-[0-9]{6}$/';
    /** Почтовый индекс */
    public const POSTCODE = '/^\d{6}$/';
    public const DATE     = '/^(\d{2})\.(\d{2})\.(\d{4})$/';
    public const EMAIL    = '/^\S+@\S+\.\S+$/u';
    /** Фамилия, Имя или Отчество */
    public const NAME     = '/^[а-яё]{2,}(-[а-яё]+)*$/iu';
    public const PERCENT  = '/^(0|[1-9][0-9]?|100)$/';
    public const PHONE    = '/^.{5,25}$/';

    public const INTEGER  = '/^(0|-?[1-9][0-9]{0,8})$/';               // Максимум 10 символов
    public const DECIMAL  = '/^(0|-?[1-9][0-9]{0,8})(,[0-9]{1,7})?$/'; // Максимум 18 символов



    /**
     * Проверяет вхождение шаблона
     *
     * @param string $name название константы в lower case
     * @param array $arguments
     * @return bool
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    public static function __callStatic(string $name, array $arguments): bool
    {
        $constantName = self::class . '::' . mb_strtoupper($name);

        if (!defined($constantName)) {
            throw new LogicException("Константа: '{$constantName}' не определена");
        }
        if (count($arguments) != 1) {
            throw new LogicException('Количество входных параметров не равно одному');
        }

        $subject = $arguments[0];

        if (!is_string($subject)) {
            throw new InvalidArgumentException('Входной параметр должен быть строкой');
        }
        return pm(constant($constantName), $subject);
    }
}
