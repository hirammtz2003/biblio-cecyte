<?php
include_once "Client.php";
include_once "MysqlConnector.php";

class SQLClient {
    private $mysql;

    function __construct(){
        $this->mysql = new MysqlConnector();
    }

    public function signIn($client){
        $findIt = 0;
        $md5password = MD5($client->getPassword());
        $this->mysql->Connect();
        
        // Consulta modificada para usar la tabla clientes
        $sqlQuery = "SELECT * FROM cliente WHERE correoElectronico='".$client->getEmail()."' AND contrasena='".$md5password."'";
        //echo $sqlQuery;
        $response = $this->mysql->ExecuteQuery($sqlQuery);
        
        if(mysqli_num_rows($response) > 0) {
            $findIt = 1;
        }
        $this->mysql->CloseConnection();
        return $findIt;
    }

    public function signUp($newClient){
        $this->mysql->Connect();
        $sqlQuery = "INSERT INTO cliente (nombre, apellidos, correoElectronico, contrasena, calle, colonia, ciudad, estado, pais, codigoPostal, telefono) 
                    VALUES ('".$newClient->getName()."', '".$newClient->getLastName()."', '".$newClient->getEmail()."', 
                    MD5('".$newClient->getPassword()."'), '".$newClient->getAddress()."', '".$newClient->getColonia()."', 
                    '".$newClient->getCity()."', '".$newClient->getState()."', '".$newClient->getCountry()."', 
                    '".$newClient->getPostalCode()."', '".$newClient->getPhone()."')";
        $response = $this->mysql->ExecuteQuery($sqlQuery);
        $this->mysql->CloseConnection();
        return $response;
    }

    public function getClientByEmail($email) {
        $this->mysql->Connect();
        $this->mysql->connection->set_charset("utf8mb4"); // Asegurar charset
        
        $query = "SELECT * FROM cliente WHERE correoElectronico = ?";
        $stmt = $this->mysql->connection->prepare($query);
        
        if(!$stmt) {
            die("Error en la preparación de la consulta: " . $this->mysql->connection->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Convertir los datos a UTF-8 si es necesario
            $row = array_map(function($value) {
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }, $row);
            
            $client = new Client();
            $client->setId($row['idCliente']);
            $client->setName($row['nombre']);
            $client->setLastName($row['apellidos']);
            $client->setEmail($row['correoElectronico']);
            $client->setAddress($row['calle']);
            $client->setColonia($row['colonia']);
            $client->setCity($row['ciudad']);
            $client->setState($row['estado']);
            $client->setCountry($row['pais']);
            $client->setPostalCode($row['codigoPostal']);
            $client->setPhone($row['telefono']);
            $client->setTipo($row['tipo']);
            
            $stmt->close();
            $this->mysql->CloseConnection();
            return $client;
        }
        
        $stmt->close();
        $this->mysql->CloseConnection();
        return null;
    }

    public function updateClient($email, $data) {
        $this->mysql->Connect();
        
        // Construir la consulta SQL base
        $sql = "UPDATE cliente SET 
                nombre = ?, 
                apellidos = ?, 
                correoElectronico = ?, 
                calle = ?, 
                colonia = ?, 
                ciudad = ?, 
                estado = ?, 
                pais = ?, 
                codigoPostal = ?,
                telefono = ?";
        
        // Si hay nueva contraseña, añadirla a la consulta
        if (!empty($data['password'])) {
            $sql .= ", contrasena = MD5(?)";
        }
        
        $sql .= " WHERE correoElectronico = ?";
        
        $stmt = $this->mysql->connection->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $this->mysql->connection->error);
        }
        
        // Preparar los tipos y parámetros
        $types = "ssssssssss"; // 10 parámetros iniciales
        $params = [
            $data['name'],
            $data['lastName'],
            $data['email'],
            $data['address'],
            $data['colonia'],
            $data['city'],
            $data['state'],
            $data['country'],
            $data['postalCode'],
            $data['phone']
        ];
        
        // Si hay contraseña, añadirla
        if (!empty($data['password'])) {
            $types .= "s";
            $params[] = $data['password'];
        }
        
        // Añadir el email para el WHERE
        $types .= "s";
        $params[] = $email;
        
        // Necesitamos pasar los parámetros por referencia
        $bindParams = array($types);
        foreach ($params as &$param) {
            $bindParams[] = &$param;
        }
        
        // Llamar a bind_param con los parámetros por referencia
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
        
        $result = $stmt->execute();
        $stmt->close();
        $this->mysql->CloseConnection();
        
        return $result;
    }
}
?>