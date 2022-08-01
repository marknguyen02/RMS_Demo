<?php

declare(strict_types=1);

namespace Src\TableGateways;

/*** * Default CRUD functions ***/
class WalletGateway
{
    private $db = null;
    private $table_name = 'wallets';

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $statement = "
            SELECT 
                id, name, hash_key
            FROM
                $this->table_name;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find(int $id): array
    {
        $statement = "
            SELECT 
                id, name, hash_key
            FROM
                $this->table_name
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findByName(string $name): array
    {
        $statement = "
            SELECT 
                id, name, hash_key
            FROM
                $this->table_name
            WHERE name = ?
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($name));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findByCondition(array $input): array
    {
        $statement = "
            SELECT 
                id, name, hash_key
            FROM
                $this->table_name
            WHERE name = :name AND hash_key = :hash_key
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'hash_key' => $input['hash_key'],
            ));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(array $input): int
    {
        $statement = "
            INSERT INTO $this->table_name 
                (name, hash_key)
            VALUES
                (:name, :hash_key);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'hash_key' => $input['hash_key'],
            ));

            $lastInsertId = $this->db->lastInsertId();
            if (!$lastInsertId || intval($lastInsertId) <= 0) {
                return 0;
            }
            return intval($lastInsertId);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete(int $id): int
    {
        $statement = "
            DELETE FROM $this->table_name
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}
