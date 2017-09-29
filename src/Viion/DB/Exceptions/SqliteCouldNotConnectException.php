<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class SqliteCouldNotConnectException
 *
 * @package Viion\DB\Exceptions
 */
class SqliteCouldNotConnectException extends \Exception
{
    const MESSAGE = 'Could not connect to the Sqlite file: %s';
    
    /**
     * SqliteCouldNotConnectException constructor.
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