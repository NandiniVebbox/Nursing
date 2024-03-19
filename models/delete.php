<?php
include_once '../../../../config/database.php';

class Delete
{
    public $conn;
    public $response;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }



}
?>