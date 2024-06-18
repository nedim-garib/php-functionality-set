<?php

class Connection {
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    private $connection;

    public function __construct($host, $username, $password, $database, $port = 3306)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    public function connect() {
        $dsn = "mysql:{$this->host};port={$this->port};dbname={$this->database}";

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        }
        catch (PDOException $ex) {
            echo "Connection to the server failed! " .$ex->getMessage();
        }
    }

    public function getConnection() {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }
}