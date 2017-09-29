<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class ConnectionNoConfigException
 *
 * @package Viion\DB\Exceptions
 */
class ConnectionNoConfigException extends \Exception
{
    const MESSAGE = 'No config provided to the database connection.';
    
    /**
     * ConnectionNoConfigException constructor.
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