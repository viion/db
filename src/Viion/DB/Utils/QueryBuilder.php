<?php

namespace Viion\DB\Utils;

use Viion\DB\SQL;
use Viion\DB\Traits\{
    BuildColumns,
    BuildWhere,
    BuildJoins
};

/**
 * Class QueryBuilder
 *
 * @package Viion\DB\Utils
 */
class QueryBuilder extends Commands
{
    use BuildColumns;
    use BuildWhere;
    use BuildJoins;
    
    protected $sql = [];
    protected $binds = [];
    protected $replace = [];
    protected $columns = [];
    
    //
    // Array string to build the SQL statement
    //
    protected $string =
    [
        'ACTION' => [],
        'ADDCOLUMNS' => [],
        'UPDATE' => [],
        'INSERT' => [],
        'DELETE' => [],
        'SCHEMA' => [],
        'COLUMNS' => [],
        'SET' => [],
        'VALUES' => [],
        'FROM' => [],
        'JOIN' => [],
        'FORCE' => [],
        'WHERE' => [],
        'DUPLICATE' => [],
        'GROUP' => [],
        'ORDER' => [],
        'LIMIT' => [],
    ];
    
    /**
     * Reset query builder
     *
     * @return $this
     */
    public function reset()
    {
        $this->sql = [];
        $this->binds = [];
        $this->replace = [];
        $this->columns = [];
        
        //
        // Array string to build the SQL statement
        //
        $this->string =
            [
                'ACTION' => [],
                'ADDCOLUMNS' => [],
                'UPDATE' => [],
                'INSERT' => [],
                'DELETE' => [],
                'SCHEMA' => [],
                'COLUMNS' => [],
                'SET' => [],
                'VALUES' => [],
                'FROM' => [],
                'JOIN' => [],
                'FORCE' => [],
                'WHERE' => [],
                'DUPLICATE' => [],
                'GROUP' => [],
                'ORDER' => [],
                'LIMIT' => [],
            ];
        
        return $this;
    }
    
    /**
     * Add to the string
     *
     * @param $type
     * @param $sql
     * @return $this
     */
    protected function addToString($type, $sql)
    {
        $this->string[$type][] = $sql;
        return $this;
    }
    
    /**
     * Add something to the binds
     *
     * @param $bind
     * @return $this
     */
    protected function addToBinds($bind)
    {
        $this->binds[] = $bind;
        return $this;
    }
    
    /**
     * Add something to the find/replace section
     *
     * @param $find
     * @param $replace
     */
    protected function addToReplace($find, $replace)
    {
        $this->replace[$find] = $replace;
        return $this;
    }
    
    /**
     * Empty part of a string
     *
     * @param $index
     * @return $this
     */
    protected function emptyStringIndex($index)
    {
        $this->string[$index] = [];
        return $this;
    }
    
    /**
     * Build the final query
     *
     * @param bool $reduce
     */
    protected function build($reduce = true)
    {
        $this->sql = [];
        
        // go through string and implode everything
        foreach($this->string as $type => $block) {
            if (!$block) {
                continue;
            }
            
            // defaults
            $implode = ' ';
            $prefix = '';
            
            if ($type == 'ADDCOLUMNS') {
                $implode = ',';
            }
            
            if ($type == 'WHERE') {
                $implode = SQL::WHERE_AND;
                $prefix = SQL::WHERE;
            }
            
            if ($type == 'ORDER') {
                $implode = ',';
                $prefix = SQL::ORDER_BY;
            }
            
            if ($type == 'GROUP') {
                $prefix = SQL::GROUP_BY;
            }
            
            if ($type == 'SET') {
                $implode = ',';
                $prefix = 'SET';
            }
            
            if ($type == 'VALUES') {
                $implode = ',';
                $prefix = SQL::INSERT_VALUES;
            }
            
            $stmt = implode($implode, $block);
            $stmt = sprintf('%s %s', $prefix, $stmt);
            $stmt = trim($stmt);
            $this->sql[] = $stmt;
        }
        
        // if reducing to one line
        if ($reduce) {
            $this->sql = implode(' ', $this->sql);
            $this->sql = trim($this->sql);
        }
        
        // replace any custom variables
        $this->sql = str_ireplace(array_keys($this->replace), $this->replace, $this->sql);
        
        // return query builder
        return $this;
    }
    
    /**
     * Get the SQL Query, built up + binds
     *
     * @param bool $isCount
     * @return array
     */
    public function get($isCount = false)
    {
        // if count, reset limit
        if ($isCount) {
            $this->string['LIMIT'] = [];
            $this->string['GROUP'] = [];
            $this->string['ACTION'] = [ sprintf(SQL::SELECT_COUNT, $isCount) ];
        }
        
        // build
        $this->build();
        
        // return
        return [
            'sql' => $this->sql,
            'binds' => $this->binds,
        ];
    }
    
    /**
     * Get the query
     *
     * @return array
     */
    public function getSql($reduce = true)
    {
        return $this->build($reduce)->sql;
    }
    
    /**
     * Get the binds
     *
     * @return array
     */
    public function getBinds()
    {
        return $this->binds;
    }
    
    /**
     * Get a symbol from a prefix
     *
     * @param $symbol
     * @return string
     */
    public function getSymbol($symbol)
    {
        switch($symbol)
        {
            default: return '='; break;
            case 'gt': return '>='; break;
            case 'lt': return '<='; break;
            case 'et': return '='; break;
        }
    }
    
    /**
     * Get query direction
     *
     * @param $direction
     * @return string
     */
    public function getDirection($direction)
    {
        return $direction ? trim($direction) : trim(SQL::ORDER_DESC);
    }
    
    /**
     * Get correct and/or
     *
     * @param $andor
     * @return string
     */
    public function getAndOr($andor)
    {
        return $andor ? trim($andor) : trim(SQL::WHERE_AND);
    }
    
    /**
     * Show query
     */
    public function show()
    {
        print_r($this->get());
        die;
    }
}