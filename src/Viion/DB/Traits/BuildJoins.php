<?php

namespace Viion\DB\Traits;

/**
 * Class BuildJoins
 * @package XIVDB\Utils\Database
 */
trait BuildJoins
{
    /**
     * Build join statement
     *
     * @param $main
     * @param $join
     * @param $as
     * @return string
     */
    protected function buildJoin($left, $right, $as)
    {
        list($lTable, $lColumn) = explode('.', $left);
        list($rTable, $rColumn) =  explode('.', $right);
        
        // normal left join, with:
        // join LEFT_TABLE with LEFT_TABLE.LEFT_COLUMN = RIGHT_TABLE.RIGHT_COLUMN
        $sql = sprintf(SQL::LEFT_JOIN, $rTable, $rTable, $rColumn, $lTable, $lColumn);
        
        // if adding an affix
        if ($as) {
            // join LEFT_TABLE with LEFT_TABLE.LEFT_COLUMN = RIGHT_TABLE.RIGHT_COLUMN
            // eg: LEFT JOIN `posts` AS reply ON reply.`reply` = `posts`.`id`
            $sql = sprintf(SQL::LEFT_JOIN_AS, $rTable, $as, $as, $rColumn, $lTable, $lColumn);
        }
        
        return $sql;
    }
    
}