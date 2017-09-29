<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class SqliteQueryFailedException
 *
 * @package Viion\DB\Exceptions
 */
class SqliteQueryFailedException extends \Exception
{
    const MESSAGE = 'Sqlite query failed for reason: %s';
    
    /**
     * SqliteQueryFailedException constructor.
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