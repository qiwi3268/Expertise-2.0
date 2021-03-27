<?php

declare(strict_types=1);

namespace App\Http\ApiControllers\Dadata;

use Illuminate\Support\Facades\Log;

use App\Http\ApiControllers\ApiController;
use App\Lib\Singles\DadataFacade;


/*
 * Базовый класс для работы с api dadata
 *
 */
abstract class DadataController extends ApiController
{
    protected DadataFacade $dadata;
    protected bool $canSendRequest = true;

    /**
     * Сколько запросов к api dadata будет выполнено из дочернего класса
     *
     * Требуется переопределить, если количество отличается от 1
     */
    protected int $willBeSent = 1;


    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $dadata = DadataFacade::createFromEnv();

        if (!$dadata->canSendRequest($this->willBeSent)) {
            Log::channel('informing')->info('Исчерпан дневной лимит запросов');
            $this->canSendRequest = false;
        }
        $this->dadata = $dadata;
    }
}
