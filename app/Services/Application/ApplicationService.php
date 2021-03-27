<?php


namespace App\Services\Application;

use RuntimeException;

use Illuminate\Support\Facades\Storage;
use App\Models\Application\ApplicationCounter;
use App\Repositories\Application\ApplicationCounterRepository;


class ApplicationService
{

    /**
     * Создает директорию заявления
     *
     * @param int $id
     * @throws RuntimeException
     */
    public function createDirectory(int $id): void
    {
        if (!Storage::makeDirectory(date('Y') . "/{$id}")) {
            throw new RuntimeException('Ошибка при создании директории заявления');
        }
    }


    /**
     * Создает счетчик заявиления
     *
     * @param int $id
     * @return string
     */
    public function createCounter(int $id): string
    {
        $rep = new ApplicationCounterRepository;
        $lastEntry = $rep->getLast();
        $currentYear = date('Y');

        if (is_null($lastEntry) || $currentYear > $lastEntry->year) {
            // Первая запись в новом году
            $counter = 1;
        } else {
            $counter = $lastEntry->counter + 1;
        }

        ApplicationCounter::create([
            'year' => $currentYear,
            'counter' => $counter,
            'doc_application_id' => $id
        ]);

        return "{$currentYear}-{$counter}";
    }
}
