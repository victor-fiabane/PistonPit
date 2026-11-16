<?php
session_start();
class Crud
{
    private $conn;
    private $db_name = "pistonpit";
    private $id;
    public $erroSql = "";
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
public function existsCliEmail($email, $id)
{
    $query = "SELECT id_cli FROM pistonpit.cliente WHERE cli_email = :email AND id_cli <> :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
public function updateCliAdmin($id, $email, $telefone, $ativo)
{
    $query = "UPDATE pistonpit.cliente SET cli_email = :email, cli_telefone = :telefone, cli_ativo = :ativo WHERE id_cli = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function updateCliPerfil($id, $nome, $email, $telefone)
{
    $query = "UPDATE pistonpit.cliente SET cli_nome = :nome, cli_email = :email, cli_telefone = :telefone WHERE id_cli = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function existsEmploEmail($email, $id)
{
    $query = "SELECT id_func FROM pistonpit.funcionario WHERE func_email = :email AND id_func <> :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
public function updateEmploAdmin($id, $nome, $email, $perfil, $ativo)
{
    $query = "UPDATE pistonpit.funcionario SET func_nome = :nome, func_email = :email, func_perfil = :perfil, func_ativo = :ativo WHERE id_func = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':perfil', $perfil);
    $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
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
    $query = "INSERT INTO pistonpit.funcionario (func_nome, func_email, func_senha, func_perfil, func_ativo)
              VALUES (:nome, :email, :senha, :perfil, 1)";
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $hash);
    $stmt->bindParam(':perfil', $perfil);
    return $stmt->execute();
}
public function updateCliSenha($id, $senha)
{
    $query = "UPDATE pistonpit.cliente SET cli_senha = :senha WHERE id_cli = :id";
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':senha', $hash);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function updateEmploNome($id, $nome)
{
    $query = "UPDATE pistonpit.funcionario SET func_nome = :nome WHERE id_func = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function updateEmploSenha($id, $senha)
{
    $query = "UPDATE pistonpit.funcionario SET func_senha = :senha WHERE id_func = :id";
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':senha', $hash);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function listCorridaMes($ini, $fim)
{
    $query = "SELECT c.*, cli.cli_nome, f.func_nome,
                     (SELECT COUNT(*) FROM pistonpit.participa p WHERE p.fk_id_corrida = c.id_corrida) AS total_part
              FROM pistonpit.corrida c
              LEFT JOIN pistonpit.cliente cli ON cli.id_cli = c.fk_id_cli
              LEFT JOIN pistonpit.funcionario f ON f.id_func = c.fk_id_func
              WHERE c.cor_data BETWEEN :ini AND :fim AND c.cor_status <> 'CANCELADA'
              ORDER BY c.cor_data, c.cor_horario";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function listBloqueioMes($ini, $fim)
{
    $query = "SELECT * FROM pistonpit.bloqueio WHERE bloq_data BETWEEN :ini AND :fim ORDER BY bloq_data";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function listPartCliente($idCli, $ini, $fim)
{
    $query = "SELECT p.fk_id_corrida
              FROM pistonpit.participa p
              INNER JOIN pistonpit.corrida c ON c.id_corrida = p.fk_id_corrida
              WHERE p.fk_id_cli = :id AND c.cor_data BETWEEN :ini AND :fim";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $idCli, PDO::PARAM_INT);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function listCorridaCliente($idCli)
{
    $query = "SELECT c.*, cli.cli_nome, f.func_nome,
                     ca.canc_just, ca.canc_data, ca.fk_id_cli AS canc_cli,
                     (SELECT COUNT(*) FROM pistonpit.participa p2 WHERE p2.fk_id_corrida = c.id_corrida) AS total_part
              FROM pistonpit.corrida c
              LEFT JOIN pistonpit.cliente cli ON cli.id_cli = c.fk_id_cli
              LEFT JOIN pistonpit.funcionario f ON f.id_func = c.fk_id_func
              LEFT JOIN pistonpit.cancelamento ca ON ca.fk_id_corrida = c.id_corrida
              WHERE c.fk_id_cli = :id
                 OR EXISTS (SELECT 1 FROM pistonpit.participa p
                            WHERE p.fk_id_corrida = c.id_corrida AND p.fk_id_cli = :idpart)
              ORDER BY c.cor_data DESC, c.cor_horario DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $idCli, PDO::PARAM_INT);
    $stmt->bindParam(':idpart', $idCli, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function readCorridaId($id)
{
    $query = "SELECT * FROM pistonpit.corrida WHERE id_corrida = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function existsCorridaHorario($data, $hora)
{
    $query = "SELECT id_corrida FROM pistonpit.corrida
              WHERE cor_data = :data AND cor_horario = :hora AND cor_status <> 'CANCELADA' LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':hora', $hora);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
public function countPartCorrida($idCorrida)
{
    $query = "SELECT COUNT(*) AS total FROM pistonpit.participa WHERE fk_id_corrida = :corrida";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':corrida', $idCorrida, PDO::PARAM_INT);
    $stmt->execute();
    $linha = $stmt->fetch(PDO::FETCH_ASSOC);
    return $linha ? (int) $linha['total'] : 0;
}
public function existsPart($idCli, $idCorrida)
{
    $query = "SELECT id_part FROM pistonpit.participa
              WHERE fk_id_cli = :cli AND fk_id_corrida = :corrida LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':cli', $idCli, PDO::PARAM_INT);
    $stmt->bindParam(':corrida', $idCorrida, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
public function listPartMes($ini, $fim)
{
    $query = "SELECT p.fk_id_corrida, p.part_data, cli.cli_nome
              FROM pistonpit.participa p
              INNER JOIN pistonpit.corrida c ON c.id_corrida = p.fk_id_corrida
              INNER JOIN pistonpit.cliente cli ON cli.id_cli = p.fk_id_cli
              WHERE c.cor_data BETWEEN :ini AND :fim
              ORDER BY cli.cli_nome";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function listPartCorrida($idCorrida)
{
    $query = "SELECT p.part_data, cli.cli_nome
              FROM pistonpit.participa p
              INNER JOIN pistonpit.cliente cli ON cli.id_cli = p.fk_id_cli
              WHERE p.fk_id_corrida = :corrida
              ORDER BY cli.cli_nome";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':corrida', $idCorrida, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function existsBloqueio($data)
{
    $query = "SELECT id_bloq FROM pistonpit.bloqueio WHERE bloq_data = :data LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':data', $data);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
public function createCorridaPrivada($idCli, $data, $hora, $tipo, $limite, $senha)
{
    try {
        $query = "INSERT INTO pistonpit.corrida
                    (cor_data, cor_horario, cor_tipo, cor_visibilidade, cor_senha, cor_limite_pilot, cor_status, fk_id_cli)
                  VALUES (:data, :hora, :tipo, 'PRIVADA', :senha, :limite, 'AGENDADA', :cli)";
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':senha', $hash);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':cli', $idCli, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    } catch (PDOException $exception) {
        $this->erroSql = $exception->getMessage();
        error_log("erro ao criar corrida privada: " . $exception->getMessage());
        return false;
    }
}
public function createCorridaPublica($idFunc, $data, $hora, $tipo, $limite, $desc)
{
    try {
        $query = "INSERT INTO pistonpit.corrida
                    (cor_data, cor_horario, cor_tipo, cor_visibilidade, cor_desc, cor_limite_pilot, cor_status, fk_id_func)
                  VALUES (:data, :hora, :tipo, 'PUBLICA', :desc, :limite, 'AGENDADA', :func)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindParam(':func', $idFunc, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $exception) {
        $this->erroSql = $exception->getMessage();
        error_log("erro ao criar corrida pública: " . $exception->getMessage());
        return false;
    }
}
public function updateCorridaRealizada($dataCorte, $horaCorte)
{
    $query = "UPDATE pistonpit.corrida SET cor_status = 'REALIZADA'
              WHERE cor_status = 'AGENDADA'
                AND (cor_data < :dataA OR (cor_data = :dataB AND cor_horario <= :hora))";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':dataA', $dataCorte);
    $stmt->bindParam(':dataB', $dataCorte);
    $stmt->bindParam(':hora', $horaCorte);
    return $stmt->execute();
}
public function createPart($idCli, $idCorrida)
{
    try {
        $query = "INSERT INTO pistonpit.participa (fk_id_cli, fk_id_corrida, part_data)
                  VALUES (:cli, :corrida, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cli', $idCli, PDO::PARAM_INT);
        $stmt->bindParam(':corrida', $idCorrida, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $exception) {
        $this->erroSql = $exception->getMessage();
        error_log("erro ao inscrever piloto: " . $exception->getMessage());
        return false;
    }
}
public function deletePart($idCli, $idCorrida)
{
    $query = "DELETE FROM pistonpit.participa WHERE fk_id_cli = :cli AND fk_id_corrida = :corrida";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':cli', $idCli, PDO::PARAM_INT);
    $stmt->bindParam(':corrida', $idCorrida, PDO::PARAM_INT);
    return $stmt->execute();
}
public function cancelCorrida($idCorrida, $just, $idCli, $idFunc)
{
    try {
        $this->conn->beginTransaction();
        $query = "UPDATE pistonpit.corrida SET cor_status = 'CANCELADA'
                  WHERE id_corrida = :id AND cor_status = 'AGENDADA'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idCorrida, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $this->conn->rollBack();
            return false;
        }
        $query = "INSERT INTO pistonpit.cancelamento (fk_id_corrida, canc_just, canc_data, fk_id_cli, fk_id_func)
                  VALUES (:corrida, :just, NOW(), :cli, :func)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':corrida', $idCorrida, PDO::PARAM_INT);
        $stmt->bindParam(':just', $just);
        $stmt->bindValue(':cli', $idCli, ($idCli === null) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':func', $idFunc, ($idFunc === null) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->execute();
        $this->conn->commit();
        return true;
    } catch (PDOException $exception) {
        $this->conn->rollBack();
        $this->erroSql = $exception->getMessage();
        error_log("erro ao cancelar corrida: " . $exception->getMessage());
        return false;
    }
}
public function createBloqueio($data, $desc, $idFunc)
{
    try {
        $query = "INSERT INTO pistonpit.bloqueio (bloq_data, bloq_desc, fk_id_func)
                  VALUES (:data, :desc, :func)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':func', $idFunc, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $exception) {
        $this->erroSql = $exception->getMessage();
        error_log("erro ao bloquear dia: " . $exception->getMessage());
        return false;
    }
}
public function deleteBloqueio($data)
{
    $query = "DELETE FROM pistonpit.bloqueio WHERE bloq_data = :data";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':data', $data);
    return $stmt->execute();
}
public function createReceita($desc, $valor, $data, $idFunc)
{
    try {
        $query = "INSERT INTO pistonpit.receita (rece_desc, rece_valor, rece_data, fk_id_func)
                  VALUES (:desc, :valor, :data, :func)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':func', $idFunc, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $exception) {
        $this->erroSql = $exception->getMessage();
        error_log("erro ao lancar receita: " . $exception->getMessage());
        return false;
    }
}
public function createDespesa($desc, $valor, $data, $idFunc)
{
    try {
        $query = "INSERT INTO pistonpit.despesa (desp_desc, desp_valor, desp_data, fk_id_func)
                  VALUES (:desc, :valor, :data, :func)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':func', $idFunc, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $exception) {
        $this->erroSql = $exception->getMessage();
        error_log("erro ao lancar despesa: " . $exception->getMessage());
        return false;
    }
}
public function listReceitaMes($ini, $fim)
{
    $query = "SELECT r.*, f.func_nome
              FROM pistonpit.receita r
              LEFT JOIN pistonpit.funcionario f ON f.id_func = r.fk_id_func
              WHERE r.rece_data BETWEEN :ini AND :fim
              ORDER BY r.rece_data DESC, r.id_rece DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function listDespesaMes($ini, $fim)
{
    $query = "SELECT d.*, f.func_nome
              FROM pistonpit.despesa d
              LEFT JOIN pistonpit.funcionario f ON f.id_func = d.fk_id_func
              WHERE d.desp_data BETWEEN :ini AND :fim
              ORDER BY d.desp_data DESC, d.id_desp DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function somaReceitaMes($ini, $fim)
{
    $query = "SELECT SUM(rece_valor) AS total FROM pistonpit.receita
              WHERE rece_data BETWEEN :ini AND :fim";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    $linha = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($linha && $linha['total'] != null) ? (float) $linha['total'] : 0;
}
public function somaDespesaMes($ini, $fim)
{
    $query = "SELECT SUM(desp_valor) AS total FROM pistonpit.despesa
              WHERE desp_data BETWEEN :ini AND :fim";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':ini', $ini);
    $stmt->bindParam(':fim', $fim);
    $stmt->execute();
    $linha = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($linha && $linha['total'] != null) ? (float) $linha['total'] : 0;
}
}
