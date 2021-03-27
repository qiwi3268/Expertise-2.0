<?php

declare(strict_types=1);

namespace App\Repositories\Calendars;

use Illuminate\Support\Collection;
use App\Repositories\Repository;
use App\Models\Calendars\CalendarWorkday;


final class CalendarWorkdayRepository extends Repository
{
    protected string $modelClassName = CalendarWorkday::class;


    /**
     * Возвращает коллекцию всех дат
     *
     * @return Collection
     */
    public function getAllDates(): Collection
    {
        return $this->getDataBaseBuilder()->select(['date'])->get();
    }
}
