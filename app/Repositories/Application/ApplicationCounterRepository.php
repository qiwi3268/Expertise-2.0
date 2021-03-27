<?php


namespace App\Repositories\Application;

use App\Repositories\Repository;
use App\Models\Application\ApplicationCounter;


final class ApplicationCounterRepository extends Repository
{
    protected string $modelClassName = ApplicationCounter::class;


    /**
     * Предназначен для получения крайней записи в таблице
     *
     * @return ApplicationCounter|null
     */
    public function getLast(): ?ApplicationCounter
    {
        /** @var ApplicationCounter|null $result */
        $result = $this->m()
            ->select(['id', 'year', 'counter', 'doc_application_id'])
            ->latest('id')
            ->first();

        return $result;
    }
}
