<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class SqliteFileDoesNotExistException
 *
 * @package Viion\DB\Exceptions
 */
class SqliteFileDoesNotExistException extends \Exception
{
    const MESSAGE = 'Sqlite file does not exist: %s';
    
    /**
     * SqliteFileDoesNotExistException constructor.
     *
     * @param array $data
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($data = [], $code = 0, Throwable $previous = null)
    {
        $message = vsprintf(self::MESSAGE, $data);
        throw parent::__construct($message, $code, $previous);
    }
}