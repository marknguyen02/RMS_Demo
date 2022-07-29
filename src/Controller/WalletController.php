<?php
namespace Src\Controller;

use Src\TableGateways\WalletGateway;

/***
 * Controller will process user action, perform connect DB and return data to user view
***/
class WalletController {

    private $db;
    private $requestMethod;

    private $walletGateway;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->walletGateway = new WalletGateway($db);
    }

    /***
     * Main process will parse request method and call to relevant function
    ***/
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getAllWallets();
                break;
            case 'POST':
                $response = $this->createWalletFromRequest($_POST);
                break;
            case 'DELETE':
                $input = (array) json_decode(file_get_contents('php://input'), TRUE);
                $response = $this->deleteWallet($input);
                break;
            default:
                $response = $this->unprocessableEntityResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    /***
     * Get all function
    ***/
    private function getAllWallets()
    {
        //start get all wallets list inn DB
        $result = $this->walletGateway->findAll();
        if (! is_array( $result) && !$result) {
            return $this->unprocessableEntityResponse("Invalid query");
        }
        return $this->successResponse(count($result) > 0 ? $result : null);
    }

    /***
     * Create wallet function
     * Will validate input
     * Check if info existing in DB
     * Return record just created
    ***/
    private function createWalletFromRequest(Array $input)
    {
        //start validate before process. if false will return back http err status code
        if (! $this->validatewallet($input)) {
            return $this->unprocessableEntityResponse("Invalid input");
        }
        //check if existing wallet(name) in database
        $exists = $this->walletGateway->findByName($input['name']);
        if (count($exists) > 0) {
            //if existed => return error code
            return $this->unprocessableEntityResponse("Duplicated info");
        }
        // do create
        $input['hash_key'] = md5($input['hash_key']);//encrypt hash_key as md5
        $insertedID = $this->walletGateway->insert($input);
        
        //if create err => return err code
        if(!$insertedID) {
            return $this->unprocessableEntityResponse("Insert fail");
        }

        //re-get all info of record just created and return back to view
        $result = $this->walletGateway->find($insertedID);
        if (! $result) {
            //return err if cannot find record by id
            return $this->unprocessableEntityResponse("Cannot find record");
        }

        //return wallet just created
        return $this->successResponse($result);
    }

    /***
     * Delete wallet function
     * Will validate input
     * Check if info existing in DB
     * After deleted will return deleted id
    ***/
    private function deleteWallet(Array $input)
    {
        //start validate before process. if false will return back http err status code
        if (! $this->validatewallet($input)) {
            return $this->unprocessableEntityResponse("Invalid input");
        }
        //find wallet need to delete
        $input['hash_key'] = md5($input['hash_key']);//encrypt hash_key as md5
        $wallet = $this->walletGateway->findByCondition($input);
        if (!$wallet) {
            return $this->unprocessableEntityResponse("Cannot find wallet");
        }
        //do delete
        if(!$this->walletGateway->delete($wallet[0]['id'])){
            return $this->unprocessableEntityResponse("Cannot delete");          
        }

        return $this->successResponse($wallet);
    }


    /***
     * Common function to validate user input before process
    ***/
    private function validatewallet(Array $input)
    {
        $name = $input['name'];
        $hash_key = $input['hash_key'];
        if(!isset($name) || strlen($name) < 3 || strlen($name) > 255 || preg_match('/[^a-z0-9]+/i', $name))
            return false;
        else if(!isset($hash_key) || strlen($hash_key) < 3 || strlen($hash_key) > 255)
            return false;

        return true;
    }

    private function unprocessableEntityResponse($msg = "")
    {
        $response['status_code_header'] = 'HTTP/1.1 403 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => $msg
        ]);
        return $response;
    }

    private function successResponse($body = "OK")
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($body);
        
        return $response;
    }
}
