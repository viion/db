<?php

namespace Viion\DB\Exceptions;

use Throwable;

/**
 * Class ConnectionGeneralException
 *
 * @package Viion\DB\Exceptions
 */
class ConnectionGeneralException extends \Exception
{
    const MESSAGE = 'General PDO Exception Error: %s';
    
    /**
     * ConnectionGeneralException constructor.
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