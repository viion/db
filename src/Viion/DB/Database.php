<?php

namespace Viion\DB;

use Viion\DB\Utils\QueryBuilder;

/**
 * Class Database
 *
 * @package Viion\DB
 */
class Database extends Connection
{
    /** @var QueryBuilder */
    public $QueryBuilder;
    /** @var array */
    private $data;
    
    /**
     * Database constructor.
     */
    function __construct()
    {
        $this->setQueryBuilder();
    }
    
    /**
     * Set query builder
     *
     * @param bool $queryBuilder
     * @return $this
     */
    public function setQueryBuilder($queryBuilder = false)
    {
        $this->QueryBuilder = $queryBuilder ? $queryBuilder : new QueryBuilder();
        return $this;
    }
    
    /**
     * Run raw SQL
     *
     * @param $sql
     * @param array $binds
     * @return mixed
     */
    public function sql($sql, $binds = [])
    {
        return $this->query([
            'sql' => $sql,
            'binds' => $binds,
        ]);
    }
    
    /**
     * Get data from database using the internal QueryBuilder
     *
     * @param bool $isCount
     * @return $this
     */
    public function get($isCount = false)
    {
        $sql = $this->QueryBuilder->get($isCount);
        $this->data = $this->query($sql);
        return $this;
    }
    
    /**
     * Alias for get, used normally for inserts/updates that do not expect data back.
     *
     * @return Database
     */
    public function execute()
    {
        return $this->get();
    }
    
    /**
     * Return all data fetched with "get()"
     *
     * @return mixed
     */
    public function all()
    {
        return $this->data;
    }
    
    /**
     * Return just 1 data fetched with "get()"
     *
     * @return bool
     */
    public function one()
    {
        return isset($this->data[0]) ? $this->data[0] : false;
    }
    
    /**
     * Return the last insert id
     *
     * @return bool
     */
    public function id()
    {
        return isset($this->data) ? $this->data : false;
    }
    
    /**
     * Get the total from a count query
     *
     * @return int
     */
    public function total()
    {
        return isset($this->data[0]['total']) ? $this->data[0]['total'] : 0;
    }
}