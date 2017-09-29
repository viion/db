<?php

namespace Viion\DB\Traits;

/**
 * Class BuilderWhere
 * @package XIVDB\Utils\Database
 */
trait BuildWhere
{
    /**
     * Build where statement
     *
     * @param $condition
     * @param string $equal
     * @return string
     */
    protected function buildWheres($condition, $equal = 'AND')
    {
        if (is_string($condition)) {
            return $condition;
        }
        
        $equal = strtoupper(sprintf(' %s ', $equal));
        return sprintf('(%s)', implode($equal, $condition));
    }
}