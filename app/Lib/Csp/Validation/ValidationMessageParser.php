<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation;

use App\Exceptions\Lib\Csp\CspParsingException;

use Illuminate\Support\Collection;

use App\Repositories\PeopleNameRepository;
use App\Lib\Cache\RepositoryCacher;
use App\Lib\Csp\MessageParser;
use App\Lib\ValueObjects\Fio;
use App\Lib\Singles\Arrays\HashArray;
use stdClass;


final class ValidationMessageParser extends MessageParser
{

    private HashArray $names;


    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $cacher = new RepositoryCacher(new PeopleNameRepository);

        /** @var Collection $names */
        $names = $cacher->call('getAllNames');

        $this->names = HashArray::createByCallback($names, fn (stdClass $obj) => $obj->name);
    }


    /**
     * Возвращает фио
     *
     * @param string $part
     * @return Fio
     * @throws CspParsingException
     */
    public function getFio(string $part): Fio
    {
        // Замена всех ё на е, т.к. в БД хранятся только е
        $part = str_replace(['ё', 'Ё'], ['е', 'Е'], $part);

        $arr = [];

        // Разбивка на отдельные слова по пробелу или запятой
        foreach (preg_split('/[,\s]/u', $part) as $str) {
            if ($str !== '') {
                $arr[] = $str;
            }
        }

        // Фильтрация разбитых частей
        $groups = [];

        for ($i = 0; $i < (count($arr) - 1); $i++) {

            $subject = $arr[$i] . ' ' . $arr[$i + 1];

            // Впереди двух элементов может не быть (в случае отсутствия отчетства)
            if (array_key_exists($i + 2, $arr)) {
                $subject .= ' ' . $arr[$i + 2];
            }

            $matches = Fio::parseString($subject);

            if (!is_null($matches)) {
                $groups[] = $matches;
            }
        }

        // Вычисление ФИО
        foreach ($groups as $group => [$first, $second, $third]) {

            if ($this->names->has($second)) {
                // Фамилия, Имя Отчество
                return new Fio($first, $second, $third);
            } elseif ($this->names->has($first)) {
                // Имя Отчество Фамилия
                return new Fio($third, $first, $second);
            }
        }

        // Соединение строки отладки
        $d = [];
        foreach ($groups as $group) {
            $d[] = implode(' ', $group);
        }

        $debug = empty($d)
            ? 'Элементы фио не найдены'
            : "debug: '" . implode(' | ', $d) . "'";

        throw new CspParsingException("Ошибка при распознании имени из фио. {$debug}");
    }
}
