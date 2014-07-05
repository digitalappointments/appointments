<?php

abstract class DBManager
{
    const MYSQL_CODE_DUPLICATE_KEY = 1062;
    const MYSQL_CODE_RECORD_NOT_FOUND = 1032;
    /**
     * Name of database
     * @var resource
     */
    public $dbConfig = null;

    /**
     * Connects to the database backend
     *
     * Will open a persistent or non-persistent connection.
     */
    abstract public function connect();

    /**
     * Parses and runs queries
     *
     * @param  string   $sql        SQL Statement to execute
     * @return resource|bool result set or success/failure bool
     */
    abstract public function query($sql);
}
