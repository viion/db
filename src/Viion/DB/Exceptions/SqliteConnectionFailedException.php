<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class SqliteConnectionFailedException
 *
 * @package Viion\DB\Exceptions
 */
class SqliteConnectionFailedException extends \Exception
{
    const MESSAGE = 'Connection to Sqlite file failed or dropped';
    
    /**
     * SqliteConnectionFailedException constructor.
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