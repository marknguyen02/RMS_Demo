<?php
namespace Src\TableGateways;
/*
* Default CRUD functions
*/
class WalletGateway {

    private $db = null;
    private $table_name = 'wallets';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
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

    public function find($id)
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

    public function findByName($name)
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

    public function findByCondition(Array $input)
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
                'hash_key'  => $input['hash_key'],
            ));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(Array $input)
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
                'hash_key'  => $input['hash_key'],
            ));
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
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
