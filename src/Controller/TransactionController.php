<?php

declare(strict_types=1);

namespace Src\Controller;

use Src\TableGateways\TransactionGateway;
use Src\TableGateways\WalletGateway;

/***
 * Controller will process user action, perform connect DB and return data to user view ***/
class TransactionController
{
    private $db;
    private $transactionGateway;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->transactionGateway = new TransactionGateway($db);
    }

    /***
     * Get all function
     ***/
    public function getAllTransactions(): array
    {
        //start get all transactions list inn DB
        $result = $this->transactionGateway->findAll();
        if (!is_array($result) && !$result) {
            return $this->unprocessableEntityResponse("Invalid query");
        }
        return $this->successResponse(count($result) > 0 ? $result : null);
    }

    /***
     * Create transaction function
     * Will validate input
     * Check if info existing in DB
     * Return record just created
     ***/
    public function createTransactionFromRequest(array $input): array
    {
        //start validate before process. if false will return back http err status code
        if (!$this->validatetransaction($input)) {
            return $this->unprocessableEntityResponse("Invalid input");
        }

        //check if match with existing wallet name in DB
        $wallGateway = new WalletGateway($this->db);
        $existed = $wallGateway->findByName($input['name']);
        if (count($existed) <= 0) {
            return $this->unprocessableEntityResponse("Cannot find wallet name");
        }

        // do create
        $input['wallet_id'] = $existed[0]['id'];
        $insertedID = $this->transactionGateway->insert($input);

        //if create err => return err code
        if ($insertedID <= 0) {
            return $this->unprocessableEntityResponse("Insert fail");
        }

        //re-get all info of record just created and return back to view
        $result = $this->transactionGateway->find($insertedID);
        if (!$result) {
            //return err if cannot find record by id
            return $this->unprocessableEntityResponse("Cannot find record");
        }
        return $this->successResponse($result);
    }

    /***
     * Common function to validate user input before process
     ***/
    private function validatetransaction(array $input): bool
    {
        $name = $input['name'];
        $amount = $input['amount'];
        $reference = $input['reference'];
        $type = $input['type'];
        if (!isset($name) || strlen($name) < 3 || strlen($name) > 255 || preg_match('/[^a-z0-9]+/i', $name))
            return false;
        elseif ($type !== "BET" && $type !== "WIN")
            return false;
        elseif (!intval($amount) || ($type == "BET" && intval($amount) > 0) || ($type == "WIN" && intval($amount) < 0))
            return false;
        elseif (substr($reference, 0, 3) !== 'TR-' || strlen($reference) < 3 || strlen($reference) > 255)
            return false;

        /*
         - hash_key should be subsequently loaded from the table wallets using the name as a reference. 
         - hask_check needs to be MD5(hash_key.name.type.amount.reference.hash_check)
         - If hash_check doesn’t match MD5(hash_key.name.type.amount.reference.hash_check), code 404 must be sent without any details. 
         => TODO: not sure what need to check here cos after build hash_check as md5(hash_key.name.type.amount.reference.hash_check), 
         the compare mention in last point make no sense. and don't see any place using this or db structure.
         */

        return true;
    }

    private function unprocessableEntityResponse(string $msg = ""): array
    {
        $response['status_code_header'] = 'HTTP/1.1 403 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => $msg
        ]);
        return $response;
    }

    private function successResponse(array $body = null): array
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($body);

        return $response;
    }
}
