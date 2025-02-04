<?php
namespace RobinDort\PslzmeLinks\Service;

use RobinDort\PslzmeLinks\Service\DatabaseConnection;


class Api {

    private $db;

    private $ciphering;
    private $ivLength;
    private $options;

    public function __construct(DatabaseConnection $dbConn) {
        // create / inject database connection
        $this->db = $dbConn;
    }

    function handleQueryAcception($requestData) {
        $linkCreator = $requestData->linkCreator;
        $title = $requestData->title;
        $firstname = $requestData->firstname;
        $lastname = $requestData->lastname;
        $company = $requestData->company;
        $companyGender = $requestData->companyGender;
        $gender = $requestData->gender;
        $position = $requestData->position;
        $curl = $requestData->curl;
        $fc = $requestData->fc;
        $cookieAccepted = $requestData->cookieAccepted;
        $timestamp = $requestData->timestamp;
        $acceptedOn = time();

        $queryLocked = $requestData->queryIsLocked;

        $dbResponses = "";

        try {
            $sqlExecutor = new DatabaseStatementExecutor($db);

            // Get the customer with its ID and its encrypt ID.
            $selectStmtResponse = $sqlExecutor->selectCustomerInformationCustomerDB();

            $dbResponses .= $selectStmtResponse["response"];
            $customerID = $selectStmtResponse["customerID"];
            $encryptID = $selectStmtResponse["encryptID"];

            $insertQueryData = array(
                    "query" => "?q1=" . $linkCreator . "&q2=" . $title . "&q3=" . $firstname . "&q4=" . $lastname . "&q5=" . $company . "&q6=" . $gender . "&q7=" . $position . "&q8=" . $curl . "&q9=" . $fc . "&q10=" . $timestamp . "&q11=" . $companyGender,
                    "timestamp" => $timestamp,
                    "acceptedOn" => $acceptedOn,
                    "cookieAccepted" => $cookieAccepted,
                    "queryLocked" => $queryLocked,
                    "customerID" => $customerID,
                    "encryptID" => $encryptID
                );

             $insertStmtResponse = $sqlExecutor->insertCustomerDBQuery($insertQueryData);
             $dbResponses .= $insertStmtResponse;
        } catch(Exception $e) {
            $dbResponses .=  "Error while trying to use database: " . $e;
            echo $dbResponses;
        } finally {
            $db->closeConnection();
        }

        return $dbResponses;
    }

}

?>