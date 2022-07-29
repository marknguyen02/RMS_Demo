<?php
namespace Src\System;

class DatabaseConnector {

    private $dbConnection = null;

    public function __construct(Array $DB_SETTINGS)
    {
        $host = $DB_SETTINGS['DB_HOST'];
        $port = $DB_SETTINGS['DB_PORT'];
        $db   = $DB_SETTINGS['DB_DATABASE'];
        $user = $DB_SETTINGS['DB_USERNAME'];
        $pass = $DB_SETTINGS['DB_PASSWORD'];

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
                $user,
                $pass
            );
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}

