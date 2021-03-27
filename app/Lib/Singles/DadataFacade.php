<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use Throwable;
use App\Exceptions\ExternalApiException;

use Dadata\DadataClient;


final class DadataFacade
{
    private DadataClient $client;


    /**
     * Конструктор класса
     *
     * @param string $token api ключ
     * @param string $secret секретный ключ для стандартизации
     */
    public function __construct(string $token, string $secret)
    {
        $this->client = new DadataClient($token, $secret);
    }


    /**
     * Статический конструктор класса
     *
     * Создаёт экземпляр на основе настроек приложения
     *
     * @return self
     */
    public static function createFromEnv(): self
    {
        return new self(
            required_env('DADATA_TOKEN'),
            required_env('DADATA_SECRET')
        );
    }


    /**
     * Можно ли отправлять запросы на api
     *
     * Считает, не исчерпан ли дневной лимит запросов
     *
     * @param int $willBeSent сколько запросов планируется отправить
     * @param int|null $dailyLimit дневной лимит запросов.
     * Если null, то будет взято значение из переменной окружения
     * @return bool
     */
    public function canSendRequest(int $willBeSent = 1, ?int $dailyLimit = null): bool
    {
        $dailyLimit ??= required_env('DADATA_DAILY_LIMIT', true);

        // Отправлено запросов за сегодня
        $sent = $this->client->getDailyStats()['services']['suggestions'];

        return ($dailyLimit - $sent - $willBeSent) >= 0;
    }


    /**
     * Возвращает данные об организации по ИНН юридического лица
     *
     * @param string $orgInn
     * @return array|null
     * @throws ExternalApiException
     */
    public function getOrganizationInfoByOrgInn(string $orgInn): ?array
    {
        try {
            $data = $this->client->suggest('party', $orgInn, 1, [
                'branch_type' => 'MAIN',
                'type'        => 'LEGAL'
            ]);
        } catch (Throwable $e) {
            ExternalApiException::rethrow('Dadata party. type: LEGAL', $e);
        }
        return $data[0] ?? null;
    }


    /**
     * Возвращает данные об организации по ИНН индивидуального предпринимателя
     *
     * @param string $persInn
     * @return array|null
     * @throws ExternalApiException
     */
    public function getOrganizationInfoByPersInn(string $persInn): ?array
    {
        try {
            $data = $this->client->suggest('party', $persInn, 1, [
                'branch_type' => 'MAIN',
                'type'        => 'INDIVIDUAL'
            ]);
        } catch (Throwable $e) {
            ExternalApiException::rethrow('Dadata party. type: INDIVIDUAL', $e);
        }
        return $data[0] ?? null;
    }


    /**
     * Возвращает данные о банке по его БИК
     *
     * @param string $bik
     * @return array|null
     * @throws ExternalApiException
     */
    public function getBankInfoByBik(string $bik): ?array
    {
        try {
            $data = $this->client->suggest('bank', $bik, 1);
        } catch (Throwable $e) {
            ExternalApiException::rethrow('Dadata bank', $e);
        }
        return $data[0] ?? null;
    }
}