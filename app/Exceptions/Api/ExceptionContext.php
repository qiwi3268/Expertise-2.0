<?php


namespace App\Exceptions\Api;

use Throwable;
use LogicException;


/**
 * Инкапсулирует данные, которые будут распределены между json ответом и логируемым сообщением
 *
 */
final class ExceptionContext
{

    private static self $instance;


    /**
     * Закрытый конструктор класса
     *
     * @param string $message
     * @param array $errors
     * @param string $code
     * @param array $meta
     * @param array $context
     */
    private function __construct(
        public string $message,
        public array $errors,
        public string $code,
        public array $meta,
        public array $context,
    ) {}


    /**
     * Статический конструктор класса
     *
     * @param string $message общее описательное сообщение с ошибкой
     * @param array $errors индексный массив с перечислением конкретных ошибок
     * @param string $code уникальный код ответа
     * @param array $meta мета информация
     * @param array $shortContext контекст, который будет отображен только для лог файлов.
     * Значение без ключа, являющиеся Throwable, будет записано в throwable.
     * Значение без ключа будет записано в description
     * @return self
     * @throws LogicException
     */
    public static function create(
        string $message = '',
        array $errors = [],
        string $code = '',
        array $meta = [],
        array $shortContext = []
    ): self {

        if (isset(self::$instance)) {
            throw new LogicException('ExceptionContext уже инициализирован');
        }

        $context = [];

        foreach ($shortContext as $key => $value) {

            if (is_int($key)) {
                if ($value instanceof Throwable) {
                    $context['throwable'] = $value;
                } else {
                    $context['description'] = $value;
                }
            } else {
                $context[$key] = $value;
            }
        }
        self::$instance = new self($message, $errors, $code, $meta, $context);
        return self::$instance;
    }


    /**
     * Исполняет пользовательскую функциию, когда класс был инициализирован
     *
     * Функция должна принимать первым параметром созданный экземпляр
     *
     * @param callable $callback
     */
    public static function whenExist(callable $callback): void
    {
        if (isset(self::$instance)) {
            $callback(self::$instance);
        }
    }


    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }


    /**
     * @param string $error
     * @return $this
     */
    public function addError(string $error): self
    {
        $this->errors[] = $error;
        return $this;
    }


    /**
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }


    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addMeta(string $key, mixed $value): self
    {
        $this->meta[$key] = $value;
        return $this;
    }


    /**
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addContext(string $key, mixed $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }


    /**
     * @param Throwable $e
     * @return $this
     */
    public function addContextThrowable(Throwable $e): self
    {
        return $this->addContext('throwable', $e);
    }


    /**
     * @param string $description
     * @return $this
     */
    public function addContextDescription(string $description): self
    {
        return $this->addContext('description', $description);
    }


    /**
     * Вспомогательная функция для выбрасывания исключения
     *
     * @throws ClientException
     */
    public function throwClientException(): void
    {
        throw new ClientException($this);
    }


    /**
     * Вспомогательная функция для выбрасывания исключения
     *
     * @throws ServerException
     */
    public function throwServerException(): void
    {
        throw new ServerException($this);
    }


    /**
     * Вспомогательная функция для выбрасывания исключения
     *
     * @throws ClientExceptionWithoutReport
     */
    public function throwClientExceptionWithoutReport(): void
    {
        throw new ClientExceptionWithoutReport($this);
    }
}
