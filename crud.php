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

public function readCli()
{
    $query = "SELECT * FROM pistonpit.cliente";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
}

public function readEmplo()
{
    $query = "SELECT * FROM pistonpit.funcionario where func_email = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}