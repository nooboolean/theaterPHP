<?php

class DBManager
{
    
    protected $connections = array();
    protected $repositoryConnectionMap = array();
    protected $repositories = array();

    public function connect($name, $params)
    {

        // 初期値指定あり
        $params = array_merge(array(
            'dsn' => null,
            'user' => '',
            'password' => '',
            'options'  => array(),
        ), $params);

        $connections = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );

        $connections->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connections[$name] = $connections;
    }

    public function getConnection($name = null)
    {
        if (is_null($name)) {
            return current($this->connections);
        }

        return $this->connections[$name];
    }

    public function setRepositoryConnectionMap($repositoryName, $name)
    {
        $this->repositoryConnectionMap[$repositoryName] = $name;
    }

    public function getConnectionForRepository($repositoryName)
    {
        if (isset($this->repositoryConnectionMap[$repositoryName])) {
            $name = $this->repositoryConnectionMap[$repositoryName];
            $connection = $this->getConnection($name);
        } else {
            $connection = $this->getConnection();
        }

        return $connection;
    }

    public function get($repositoryName)
    {
        if (!isset($this->repositories[$repositoryName])) {
            $repositoryClass = $repositoryName . 'Repository';
            $connection = $this->getConnectionForRepository($repositoryName);

            $repository = new $repositoryClass($connection);
            $this->repositories[$repositoryName] = $repository;
        }

        return $this->repositories[$repositoryName];
    }

    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

        foreach ($this->connections as $connection) {
            unset($connection);
        }
    }

}
