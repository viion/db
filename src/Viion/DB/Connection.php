<?php

namespace Viion\DB;

use \PDO,
    \PDOException;

use Viion\DB\Exceptions\{
    ConnectionGeneralException,
    ConnectionNoConfigException,
    ConnectionQueryFailException,
    ConnectionToDatabaseFailedException
};

/**
 * Class Connection
 *
 * @package Viion\DB
 */
class Connection
{
    /** @var \PDO $Connection */
    protected $Connection;
    protected $language;
    
    const ATTR_PERSISTENT = false;
    const ATTR_EMULATE_PREPARES = false;
    const ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION;
    const ATTR_TIMEOUT = 15;
    
    /**
     * @param $config
     * @throws ConnectionGeneralException
     * @throws ConnectionNoConfigException
     * @throws ConnectionToDatabaseFailedException
     */
    public function connect($config)
    {
        if (!$config) {
            throw new ConnectionNoConfigException();
        }
        
        // Convert config
        $config = is_object($config) ?: (Object)$config;
        
        // create connection string
        $string = sprintf('mysql:%s', implode(';', [
            'host='. $config->host,
            'port='. $config->port,
            'dbname='. $config->name,
            'charset='. $config->char,
        ]));
        
        try {
            // setup connection
            $this->Connection = new PDO($string, $config->user, $config->password, [
                PDO::ATTR_PERSISTENT => self::ATTR_PERSISTENT,
            ]);
            
            // if failed connection
            if (!$this->Connection) {
                throw new ConnectionToDatabaseFailedException();
            }
            
            $this->Connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, self::ATTR_EMULATE_PREPARES);
            $this->Connection->setAttribute(PDO::ATTR_ERRMODE, self::ATTR_ERRMODE);
            $this->Connection->setAttribute(PDO::ATTR_TIMEOUT, self::ATTR_TIMEOUT);
        } catch (PDOException $ex) {
            throw new ConnectionGeneralException([ $ex->getMessage() ], 0, $ex);
        }
    }
    
    /**
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }
    
    /**
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
     * Run a query
     *
     * @param $data
     * @param bool $isSingle
     * @return array|mixed|string
     * @throws ConnectionQueryFailException
     */
    public function query($data, $isSingle = false)
    {
        $sql = $data['sql'];
        $binds = isset($data['binds']) ? $data['binds'] : [];
        
        // replace language params if one set
        $sql = str_ireplace('{lang}', $this->language, $sql);
        
        try {
            // prepare and execute SQL statement
            $query = $this->Connection->prepare($sql);
            
            // handle any bound parameters
            if ($binds) {
                foreach($binds as $param) {
                    list($name, $value, $type) = $param;
                    $query->bindValue($name, $value, $type);
                }
            }
            
            // execute query
            $query->execute();
            
            // get action
            $action = strtolower(explode(' ', $sql)[0]);
            
            // perform action
            if (in_array($action, ['insert'])) {
                return $this->Connection->lastInsertId();
            }
            
            return $isSingle ?
                $query->fetch(PDO::FETCH_ASSOC) :
                $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $ex) {
            throw new ConnectionQueryFailException([ $ex->getMessage() ], 0, $ex);
        }
    }
}