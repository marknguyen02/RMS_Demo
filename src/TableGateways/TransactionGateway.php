<?php
namespace Src\TableGateways;
/*
* Default CRUD functions
*/
class TransactionGateway {

    private $db = null;
    private $table_name = 'transactions';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                `id`, `wallet_id`, `type`, `amount`, `reference`, `timestamp`
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
                `id`, `wallet_id`, `type`, `amount`, `reference`, `timestamp`
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

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO $this->table_name 
                (`wallet_id`, `type`, `amount`, `reference`, `timestamp`)
            VALUES
                (:wallet_id, :type, :amount, :reference, CURRENT_TIMESTAMP);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'wallet_id' => $input['wallet_id'],
                'type'  => $input['type'],
                'amount' => $input['amount'],
                'reference'  => $input['reference'],
            ));
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}
