<?php

namespace Viion\DB;

use \PDO;
use Viion\DB\Exceptions\{
    SqliteConnectionFailedException,
    SqliteCouldNotConnectException,
    SqliteFileDoesNotExistException,
    SqliteQueryFailedException
};

/**
 * Class SQLite
 *
 * @package Viion\DB
 */
class SQLite
{
    /** @var \PDO */
    protected $Connection;
    
    /**
     * Set the Sqlite connection
     *
     * @param string $filename
     * @throws SqliteCouldNotConnectException
     * @throws SqliteFileDoesNotExistException
     */
    public function connect(string $filename)
    {
        if (!file_exists($filename)) {
            throw new SqliteFileDoesNotExistException([ $filename ]);
        }
        
        // create a new connection
        $this->Connection = new PDO('sqlite:'. $filename);
        
        // if connection failed
        if (!$this->Connection) {
            throw new SqliteCouldNotConnectException([ $filename ]);
        }
    }
    
    /**
     * Run some SQL on the sqlite file
     *
     * @param string $sql
     * @return array
     * @throws SqliteConnectionFailedException
     * @throws SqliteQueryFailedException
     */
    public function sql(string $sql)
    {
        if (!$this->Connection) {
            throw new SqliteConnectionFailedException();
        }
        
        // try run query
        try {
            $query = $this->Connection->prepare($sql);
            $query->execute();
            
            return $query->fetchAll();
        } catch (\Exception $ex) {
            throw new SqliteQueryFailedException([ $ex->getMessage() ], 0, $ex);
        }
    }
}