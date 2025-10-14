<?php
class MysqlConnector {
    private $server;
    private $connUser;
    private $connPassword;
    private $connDb;
    var $connection;

    function __construct(){
        $this->server = "basedatos";
        $this->connUser = "root";
        $this->connPassword = "root";
        $this->connDb = "joyeriaSuarez"; 
    }

    public function Connect(){
        // Establecer conexión con opciones UTF-8
        $this->connection = new mysqli($this->server, $this->connUser, $this->connPassword, $this->connDb);
        
        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }
        
        // Configuración completa de charset
        $this->connection->set_charset("utf8mb4");
        $this->connection->query("SET NAMES 'utf8mb4'");
        $this->connection->query("SET CHARACTER SET utf8mb4");
        $this->connection->query("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");
    }

    public function ExecuteQuery($query){
        $result = $this->connection->query($query);
        if(!$result){
            die("Error en la consulta: " . $this->connection->error);
        }
        return $result;
    }

    public function CloseConnection(){
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
?>