<?php
    class DataBase{
        private $host = "localhost";
        private $db_name = "pistonpit";
        private $username = "root";
        private $password = "";
        private $port = 3306;  
        private $ssl = false; 
        public $conn;
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
                $dsn = "mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->db_name.";charset=utf8mb4";
                $opcoes = array();
                if($this->ssl){
                    $ca = $this->caminhoCA();
                    if($ca != ""){
                        $opcoes[PDO::MYSQL_ATTR_SSL_CA] = $ca;
                        $opcoes[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
                    } else {
                        error_log("aviso: certificado CA nao encontrado, TLS sem verificacao");
                        $opcoes[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                    }
                }
                $opcoes[PDO::ATTR_TIMEOUT] = 15;
                if(getenv('DB_DEBUG') === 'true'){
                    $opcoes[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
                }
                $this->conn = new PDO($dsn, $this->username, $this->password, $opcoes);
            }
            catch(PDOException $exception){
                error_log("erro de conexao: " . $exception->getMessage());
                die("Nao foi possivel conectar ao banco de dados.");
            }
            return $this->conn;
        }

        private function caminhoCA(){
            if(getenv('DB_SSL_CA')){ return getenv('DB_SSL_CA'); }
            $caminhos = array(
                "/etc/ssl/certs/ca-certificates.crt",
                "/etc/pki/tls/certs/ca-bundle.crt",
                "/etc/ssl/cert.pem"
            );
            foreach($caminhos as $caminho){
                if(file_exists($caminho)){ return $caminho; }
            }
            return "";
        }
}
