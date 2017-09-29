<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class ConnectionQueryFailException
 *
 * @package Viion\DB\Exceptions
 */
class ConnectionQueryFailException extends \Exception
{
    const MESSAGE = 'Query Failed: %s';
    
    /**
     * ConnectionQueryFailException constructor.
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