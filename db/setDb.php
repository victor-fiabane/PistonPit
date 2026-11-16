<?php
    class DataBase{
        // === ALTERADO (deploy no Render) ==============================
        // Antes os dados do banco ficavam fixos aqui embaixo.
        // Agora: se as variaveis de ambiente existirem (foi o que a
        // gente configurou no painel do Render), ele conecta no banco
        // online. Se nao existirem (seu XAMPP aqui no PC), continua
        // usando localhost / root / sem senha, igual era antes.
        // Assim o mesmo arquivo serve nos dois lugares, sem trocar nada.
        // ==============================================================
        private $host = "localhost";
        private $db_name = "pistonpit";
        private $username = "root";
        private $password = "";
        private $port = 3306;   // NOVO: o XAMPP usa 3306, o TiDB usa 4000
        private $ssl = false;   // NOVO: o banco online exige conexao segura
        public $conn;

        // NOVO: le as variaveis de ambiente, quando existirem
        public function __construct(){
            if(getenv('DB_HOST')){ $this->host = getenv('DB_HOST'); }
            if(getenv('DB_NAME')){ $this->db_name = getenv('DB_NAME'); }
            if(getenv('DB_USER')){ $this->username = getenv('DB_USER'); }
            if(getenv('DB_PASS') !== false){ $this->password = getenv('DB_PASS'); }
            if(getenv('DB_PORT')){ $this->port = (int) getenv('DB_PORT'); }
            if(getenv('DB_SSL') === 'true'){ $this->ssl = true; }
        }

        public function getConnection(){
            try{
                // ALTERADO: o endereco de conexao (DSN) agora leva tambem a
                // porta e o charset. Antes era so host e dbname.
                $dsn = "mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->db_name.";charset=utf8mb4";

                // NOVO: opcoes da conexao. O certificado so entra quando
                // esta rodando online; no XAMPP esse trecho e ignorado.
                $opcoes = array();
                if($this->ssl){
                    $opcoes[PDO::MYSQL_ATTR_SSL_CA] = "/etc/ssl/certs/ca-certificates.crt";
                }

                $this->conn = new PDO($dsn, $this->username, $this->password, $opcoes);
            }
            catch(PDOException $exception){
                echo"erro de conexão: ".$exception->getMessage();
            }
            return $this->conn;
        }
}
