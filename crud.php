<?php
session_start();
class Crud
{
    private $conn;
    private $db_name = "pistonpitdb";
    private $id;
    public function __construct($db)
    {
        $this->conn = $db;
    }
}