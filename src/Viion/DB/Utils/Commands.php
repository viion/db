<?php

namespace Viion\DB\Utils;

use \PDO;

use Ramsey\Uuid\Uuid;
use Viion\DB\SQL;
use Viion\DB\Traits\{
    BuildColumns,
    BuildWhere,
    BuildJoins
};

/**
 * Class Commands
 *
 * @package Viion\DB\Utils
 */
class Commands
{
    use BuildColumns;
    use BuildWhere;
    use BuildJoins;
    
    /**
     * Create a table
     *
     * @param $table
     * @param $options
     * @param $columns
     * @return mixed
     */
    public function create($table, $options, $columns)
    {
        $arr = [];
        
        if (isset($options['add_id'])) {
            $arr[] = SQL::CREATE_ID;
        }
        
        if (isset($options['add_updated'])) {
            $arr[] = SQL::CREATE_UPDATED;
        }
        
        // create table
        foreach($columns as $column) {
            $arr[] = $this->getColumnSqlType($column);
        }
        
        $sql = sprintf(SQL::CREATE_TABLE, $table, implode(',', $arr));
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Show tables if it exists
     *
     * @param $table
     * @return mixed
     */
    public function exists($table)
    {
        return $this
            ->reset()
            ->addToString('ACTION', sprintf(SQL::SHOW_TABLES_LIKE, $table));
    }
    
    /**
     * @return mixed
     */
    public function showTables()
    {
        return $this
            ->reset()
            ->addToString('ACTION', SQL::SHOW_TABLES);
    }
    
    /**
     * Check if index exists
     *
     * @param $table
     * @param $index
     * @return mixed
     */
    public function existsIndex($table, $index)
    {
        return $this
            ->reset()
            ->addToString('ACTION', sprintf(SQL::INDEX_GET, $table, $index));
    }
    
    /**
     * Describe a table
     *
     * @param $table
     * @return mixed
     */
    public function describe($table)
    {
        return $this
            ->reset()
            ->addToString('ACTION', sprintf(SQL::DESCRIBE, $table));
    }
    
    /**
     * depreciated
     *
     * @param $table
     * @param $column
     * @return mixed
     */
    public function alter($table, $column)
    {
        return $this->alterAdd($table, $column);
    }
    
    /**
     * Add column to table
     *
     * @param $table
     * @return mixed
     */
    public function alterAdd($table, $column)
    {
        $sql = $this->getColumnSqlType($column);
        $sql = sprintf(SQL::ALTER_TABLE_ADD, $table, $sql);
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Rename a column
     *
     * @param $table
     * @param $old
     * @param $new
     * @return mixed
     */
    public function alterRename($table, $old, $new, $type)
    {
        $sql = sprintf(SQL::ALTER_TABLE_RENAME, $table, $old, $new, $type);
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Add an index
     *
     * @param $table
     * @param $columns
     */
    public function index($table, $column)
    {
        $sql = sprintf(SQL::ALTER_TABLE_ADD, $table, sprintf(SQL::INDEX, $column, $column));
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Add multiple indexes to 1 group
     *
     * @param $table
     * @param $indexes
     * @param $name
     * @return mixed
     */
    public function indexMulti($table, $indexes, $name)
    {
        $sql = sprintf(SQL::ALTER_TABLE_ADD, $table, sprintf(SQL::INDEX, $name, implode('`,`', $indexes)));
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Add multiple unique indexes to 1 group
     *
     * @param $table
     * @param $indexes
     * @param $name
     * @return mixed
     */
    public function uniqueMulti($table, $indexes, $name)
    {
        $sql = sprintf(SQL::ALTER_TABLE_ADD, $table, sprintf(SQL::UNIQUE, $name, implode('`,`', $indexes)));
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Add an unique index
     *
     * @param $table
     * @param $columns
     */
    public function unique($table, $column)
    {
        $sql = sprintf(SQL::ALTER_TABLE_ADD, $table, sprintf(SQL::UNIQUE, $column, $column));
        
        return $this
            ->reset()
            ->addToString('ACTION', $sql);
    }
    
    /**
     * Show tables create
     *
     * @param $table
     * @return mixed
     */
    public function showcreate($table)
    {
        return $this
            ->reset()
            ->addToString('ACTION', sprintf(SQL::SHOW_TABLES_CREATE, $table));
    }
    
    /**
     * Truncate a table
     *
     * @param $table
     * @return $this
     */
    public function truncate($table)
    {
        return $this
            ->reset()
            ->addToString('ACTION', sprintf(SQL::TRUNCATE, $table));
    }
    
    /**
     * Select something
     *
     * @param bool $columns
     * @param bool $isDistinct
     * @return $this
     */
    public function select($columns = false, $isDistinct = false)
    {
        $this->reset();
        
        // No select (assumes they will be added later)
        if (!$columns) {
            return $this->addToString('ACTION', SQL::SELECT_BLANK);
        }
        
        // Build columns
        $columns = $this->buildColumns($columns);
        $sql = $isDistinct
            ? sprintf(SQL::SELECT_DISTINCT, $columns)
            : sprintf(SQL::SELECT, $columns);
        
        return $this->addToString('ACTION', $sql);
    }
    
    /**
     * Select a TOTAL, this will empty all columns
     * in the query!
     *
     * @return $this
     */
    public function count()
    {
        return $this
            ->reset()
            ->addToString('ACTION', SQL::SELECT_COUNT);
    }
    
    /**
     * Perform a FROM query
     *
     * @param $table
     * @return $this
     */
    public function from($table)
    {
        return $this->addToString('FROM', sprintf(SQL::FROM, $table));
    }
    
    /**
     * Perform a where
     *
     * @param $condition
     * @param string $equal
     * @return $this
     */
    public function where($condition, $join = 'AND')
    {
        $join = $join ? trim($join) : trim(SQL::WHERE_AND);
        $sql = $this->buildWheres($condition, $join);
        return $this->addToString('WHERE', $sql);
    }
    
    /**
     * @param $condition
     * @param $value
     * @return mixed
     */
    public function whereValue($condition, $value)
    {
        $sql = $this->buildWheres($condition);
        
        // bind value
        list ($column, $bind) = explode(' = ', $condition);
        $this->bind($bind, $value);
        
        return $this->addToString('WHERE', $sql);
    }
    
    /**
     * Perform a join
     *
     * @param $main
     * @param $table
     * @param null $as
     * @return $this
     */
    public function join($left, $right, $as = null)
    {
        // if main is an array
        if (is_array($left)) {
            $left = sprintf('%s.%s', key($left), reset($left));
            $right = sprintf('%s.%s', key($right), reset($right));
        }
        
        return $this->addToString('JOIN', $this->buildJoin($left, $right, $as));
    }
    
    /**
     * Perform a group
     *
     * Group a column
     * @param $main
     * @return $this
     */
    public function group($main)
    {
        return $this->addToString('GROUP', $main);
    }
    
    /**
     * Perform an order
     *
     * @param $order
     * @param string $direction
     * @return $this
     */
    public function order($order, $direction = 'desc')
    {
        $direction = (strtolower($direction) == 'desc') ? SQL::ORDER_DESC : SQL::ORDER_ASC;
        return $this->addToString('ORDER', sprintf('%s %s', $order, $direction));
    }
    
    /**
     * Perform a limit
     *
     * @param $start
     * @param $amount
     * @return $this
     */
    public function limit($start, $amount)
    {
        return $this->addToString('LIMIT', sprintf(SQL::LIMIT, $start, $amount));
    }
    
    /**
     * Perform an update
     *
     * @param $table
     * @return $this
     */
    public function update($table)
    {
        return $this
            ->reset()
            ->addToString('UPDATE', sprintf(SQL::UPDATE, $table));
    }
    
    /**
     * Perform an insertion
     *
     * @param $table
     * @return $this
     */
    public function insert($table)
    {
        return $this
            ->reset()
            ->addToString('INSERT', sprintf(SQL::INSERT, $table));
    }
    
    /**
     * Perform a deletion
     *
     * @param $table
     * @return $this
     */
    public function delete($table)
    {
        return $this
            ->reset()
            ->addToString('DELETE', sprintf(SQL::DELETE, $table));
    }
    
    /**
     * Perform set queries, for updates
     *
     * @param $column
     * @param null $value
     * @return $this
     */
    public function set($column, $value = null)
    {
        if (is_array($column)) {
            foreach($column as $col => $value) {
                $sql = sprintf(SQL::UPDATE_SET, $col, $value);
                $this->addToString('SET', $sql);
            }
        } else {
            $sql = sprintf(SQL::UPDATE_SET, $column, $value);
            $this->addToString('SET', $sql);
        }
        
        return $this;
    }
    
    /**
     * Set values for an insert
     *
     * @param $values
     * @param bool $autoBind
     * @return $this
     */
    public function values($values, $autoBind = false)
    {
        $arr = [];
        
        foreach($values as $value) {
            // if auto binding
            if ($autoBind) {
                $bind = ':a'. substr(preg_replace("/[^\w]+/", "", Uuid::uuid4()), 0, 8) . mt_rand(0,99999999);
                $this->bind($bind, $value);
                $value = $bind;
            }
            
            if (isset($value[0]) && $value[0] !== ':' && $value) {
                $value = sprintf("'%s'", $value);
            }
            
            if (!$value) {
                $value = 'NULL';
            }
            
            $arr[] = $value;
        }
        
        return $this->addToString('VALUES', sprintf("(%s)", implode(",", $arr)));
    }
    
    /**
     * Set schema for insert values
     *
     * @param $values
     * @return $this
     */
    public function schema($values)
    {
        $this->columns = $values;
        return $this->addToString('SCHEMA', sprintf(SQL::SCHEMA, implode('`,`', $values)));
    }
    
    /**
     * Bind a parameter
     *
     * @param $param
     * @param $variable
     * @param bool $isWild
     * @return $this
     */
    public function bind($param, $variable, $isWild = false)
    {
        return $this->addToBinds([
            trim(str_ireplace(':', null, $param)),
            $isWild ? '%'. $variable .'%' : $variable,
            is_numeric($variable) ? PDO::PARAM_INT : PDO::PARAM_STR,
        ]);
    }
    
    /**
     * Replace stuff
     *
     * @param $find
     * @param $replace
     * @return $this
     */
    public function replace($find, $replace)
    {
        return $this->addToReplace($find, $replace);
    }
    
    /**
     * Handle on duplicate key update
     *
     * @param array $columns
     * @param bool $include
     */
    public function duplicate($columns = [], $include = false)
    {
        if (!$include) {
            $columns = array_diff($this->columns, $columns);
        } else if ($include && !$columns) {
            $columns = $this->columns;
        }
        
        $duplicate = [];
        foreach($columns as $c) {
            $duplicate[] = sprintf(SQL::DUPLICATE_ENTRY, $c, $c);
        }
        
        $sql = sprintf(SQL::DUPLICATE, implode(',', $duplicate));
        return $this->addToString('DUPLICATE', $sql);
    }
    
    /**
     * Column must be not null
     *
     * @param $column
     * @return $this
     */
    public function notnull($column)
    {
        return $this->where(sprintf(SQL::IS_NOT_NULL, $column), trim(SQL::WHERE_AND));
    }
    
    /**
     * Column must not be empty
     *
     * @param $column
     * @return $this
     */
    public function notempty($column)
    {
        return $this->where(sprintf(SQL::IS_NOT_EMPTY, $column), trim(SQL::WHERE_AND));
    }
    
    /**
     * Column must be empty
     *
     * @param $column
     * @return $this
     */
    public function isempty($column)
    {
        return $this->where(sprintf(SQL::IS_EMPTY, $column), trim(SQL::WHERE_AND));
    }
    
    /**
     * Column is not a value
     *
     * @param $column
     * @param $value
     * @return $this
     */
    public function not($column, $value)
    {
        return $this->where(sprintf(SQL::IS_NOT, $column, $value), trim(SQL::WHERE_AND));
    }
    
    /**
     * Add columns
     *
     * @param string $columns
     * @return $this
     */
    public function addColumns($columns = '*')
    {
        return $this->addToString('ADDCOLUMNS', $this->buildColumns($columns));
    }
    
    /**
     * Add prefixed columns
     *
     * @param $table
     * @param $prefix
     * @param $columns
     * @return mixed
     */
    public function addPrefixColumns($table, $prefix, $columns)
    {
        foreach($columns as $i => $col) {
            $columns[$i] = sprintf('%s AS %s_%s', $col, $prefix, str_ireplace('_{lang}', null, $col));
        }
        
        return $this->addToString('ADDCOLUMNS', $this->buildColumns([
            $table => $columns
        ]));
    }
    
    /**
     * Force index
     *
     * @param $keys
     * @return $this
     */
    public function forceIndex($keys)
    {
        return $this->addToString('FORCE', sprintf(SQL::FORCE_INDEX, $keys));
    }
}