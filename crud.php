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

public function readCli($email)
{
    $query = "SELECT * FROM pistonpit.cliente WHERE cli_email = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function readEmplo($email)
{
    $query = "SELECT * FROM pistonpit.funcionario WHERE func_email = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function readCliId($id)
{
    $query = "SELECT * FROM pistonpit.cliente WHERE id_cli = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function listCli()
{
    $query = "SELECT * FROM pistonpit.cliente ORDER BY cli_nome";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function existsCli($email, $cpf)
{
    $query = "SELECT id_cli FROM pistonpit.cliente WHERE cli_email = :email OR cli_cpf = :cpf LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}

public function createCli($nome, $cpf, $email, $telefone, $senha)
{
    $query = "INSERT INTO pistonpit.cliente (cli_nome, cli_cpf, cli_email, cli_telefone, cli_senha, cli_ativo, cli_termo_data)
              VALUES (:nome, :cpf, :email, :telefone, :senha, 1, NOW())";
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':senha', $hash);
    return $stmt->execute();
}

public function updateCli($id, $nome, $telefone)
{
    $query = "UPDATE pistonpit.cliente SET cli_nome = :nome, cli_telefone = :telefone WHERE id_cli = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

public function readEmploId($id)
{
    $query = "SELECT * FROM pistonpit.funcionario WHERE id_func = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function listEmplo()
{
    $query = "SELECT * FROM pistonpit.funcionario ORDER BY func_nome";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function existsEmplo($email)
{
    $query = "SELECT id_func FROM pistonpit.funcionario WHERE func_email = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}

public function createEmplo($nome, $email, $senha, $perfil)
{
    $query = "INSERT INTO funcionario (func_nome, func_email, func_senha, func_perfil, func_ativo)
              VALUES (:nome, :email, :senha, :perfil, 1)";
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $hash);
    $stmt->bindParam(':perfil', $perfil);
    return $stmt->execute();
}

}