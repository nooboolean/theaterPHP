<?php

class DBRepository
{
    
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function execute($sql, $params = array())
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function fetch($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}