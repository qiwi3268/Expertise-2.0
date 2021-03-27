<?php


namespace App\Exceptions\Repositories;

use Exception;


/**
 * Случаи, когда запрос к БД не вернул требуемый результат
 *
 */
final class ResultDoesNotExistException extends Exception
{
}
