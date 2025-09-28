<?php

class Database
{

    private $host;
    private $user;
    private $password;
    private $database;

    function __construct()
    {
        $this->host = 'localhost';
        $this->user = 'root';
        $this->password = '';
        $this->database = 'users-service';
    }


    function getConnection()
    {
        $connection = new mysqli($this->host, $this->user, $this->password, $this->database);

        if ($connection->connect_error) {
            throw new Exception('La conexion ha fallado' . $connection->connect_error);
        } else {
            return $connection;
        }
    }
    function closeConnection($conecction)
    {
        if ($conecction){
            $conecction->close();
        }
    }
}
