<?php
namespace RobinDort\PslzmeLinks\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Contao\Database;
use Contao\Message;

use RobinDort\PslzmeLinks\Exceptions\InvalidDataException;
use RobinDort\PslzmeLinks\Exceptions\DatabaseException;

#[AsController]
class BackendRequestHandlerController {

    #[Route('/saveDatabaseData', name: "save_database_data", defaults: ['_token_check' => true, '_scope' => 'backend'],  methods: ['POST'])] 
    public function saveDatabaseData(Request $request): JsonResponse {
        $requestData = $request->request->get('data');
        $requestData = json_decode($requestData, false);

        if (!$requestData) {
            throw new InvalidDataException("Unable to extract request data out of /saveDatabaseData object");
        }

        try {
            $databaseName = $requestData->dbName;
            $databaseUser = $requestData->dbUsername;
            $databasePassword = $requestData->dbPW;


            if (!$databaseName || !$databaseUser || !$databasePassword) {
                throw new InvalidDataException("Unable to extract database information out of request object");
            }

            // encrypt the password before saving
            $timestamp = time();
            $encryptedPassword = $this->encryptPassword($databasePassword,$timestamp);

            // check if database options are already saved
            $selectResult = Database::getInstance()->prepare("SELECT * FROM tl_pslzme_config")->execute();

            if ($selectResult->numRows > 0) {
                // database data has been found. Update it.
                $updateResult = Database::getInstance()->prepare("UPDATE tl_pslzme_config SET pslzme_db_name = ?, pslzme_db_user = ?, pslzme_db_pw = ?")->execute($databaseName, $databaseUser, $encryptedPassword);

                if ($updateResult->affectedRows > 0) {
                    Message::addConfirmation("Update successful!");
                    return new JsonResponse("Sucessfully updated pslzme database data.");
                } else {
                    Message::addError("An error occurred while updating database data.");
                    throw new DatabaseException("Unable to update pslzme configuration data into tl_pslzme_config table");
                }

            } else {
                // no database data found. Insert the new data

                // save the database data into the pslzme config table
                $insertResult = Database::getInstance()->prepare("INSERT INTO tl_pslzme_config (pslzme_db_name, pslzme_db_user, pslzme_db_pw, timestamp) VALUES (?,?,?,?)")->execute($databaseName, $databaseUser, $encryptedPassword, $timestamp);

                if ($insertResult->affectedRows > 0) {
                    Message::addConfirmation("Successfully inserted database data");
                    return new JsonResponse("Sucessfully inserted pslzme database data.");
                } else {
                    Message::addError("An error occurred while inserting new database data.");
                    throw new DatabaseException("Unable to insert pslzme configuration data into tl_pslzme_config table");
                }
            }
        } catch (InvalidDataException $ide) {
            error_log($ide->getErrorMsg());
            return new JsonResponse($ide->getErrorMsg());
        } catch(DatabaseException $dbe) {
            error_log($dbe->getErrorMsg());
            return new JsonResponse($dbe->getErrorMsg());
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JsonResponse($e->getMessage());
        }
    }


    private function encryptPassword($password, $timestamp) {
        $secretKey = hash('sha256', $timestamp); // Create a key from the timestamp
        $iv = random_bytes(16); // Generate IV
    
        $ciphertext = openssl_encrypt($password, 'aes-256-cbc', $secretKey, 0, $iv);
        $encryptedData = base64_encode($iv . $ciphertext);
    
        return $encryptedData;
    }  

    private function decryptPassword($encryptedPassword, $timestamp) {
        $secretKey = hash('sha256', $timestamp); // Recreate key from timestamp
        $data = base64_decode($encryptedPassword);
        
        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);
        
        return openssl_decrypt($ciphertext, 'aes-256-cbc', $secretKey, 0, $iv);
    }
}

?>