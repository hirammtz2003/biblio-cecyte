<?php
session_start();

// Si ya está logueado, redirigir al index
if(isset($_SESSION["client_email"])){
    header("Location: index.php");
    exit();
}

// Procesar el formulario si se envió
if(isset($_POST['email'])) {
    include_once "SQLClient.php";
    include_once "Client.php";
    
    $client = new Client();
    $client->setName($_POST['nombre']);
    $client->setLastName($_POST['apellidos']);
    $client->setEmail($_POST['email']);
    $client->setPassword($_POST['password']);
    $client->setAddress($_POST['calle']);
    $client->setColonia($_POST['colonia']);
    $client->setCity($_POST['ciudad']);
    $client->setState($_POST['estado']);
    $client->setCountry($_POST['pais']);
    $client->setPostalCode($_POST['codigoPostal']);
    $client->setPhone($_POST['telefono']);
    
    $sqlClient = new SQLClient();
    $result = $sqlClient->signUp($client);
    
    if($result) {
        $_SESSION['client_email'] = $_POST['email'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Error al registrar el usuario. Intente nuevamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Joyería Suárez</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400&family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos/estilos.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .register-container h2 {
            color: #d4af37;
            margin-bottom: 30px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-family: 'Montserrat', sans-serif;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: #d4af37;
            outline: none;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .register-btn {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Montserrat', sans-serif;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .register-btn:hover {
            background-color: #c19b2e;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-family: 'Montserrat', sans-serif;
        }
        
        .login-link a {
            color: #d4af37;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background-color: #fff9e6;
            border-left: 5px solid #d4af37;
            padding: 15px;
            margin-bottom: 25px;
            text-align: left;
            border-radius: 4px;
            color: #d9534f;
            font-family: 'Montserrat', sans-serif;
        }

        .login-link {
            margin-top: 20px;
            font-family: 'Playfair Display', sans-serif;
            text-align: center;
        }

        .login-link a {
            color: #d4af37;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="section-header">
        <h1>Joyería Suárez</h1>
    </header>
    
    <nav>
        <a href="index.php">INICIO</a>
    </nav>
    
        <div class="register-container">
            <h2>Registro de Cliente</h2>
            
            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="FrmAddClient.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>                
                <div class="form-group">
                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" required>
                </div>                
                <div class="form-row">
                    <div class="form-group">
                        <label for="colonia">Colonia:</label>
                        <input type="text" id="colonia" name="colonia" required>
                    </div>
                    <div class="form-group">
                        <label for="ciudad">Ciudad:</label>
                        <input type="text" id="ciudad" name="ciudad" required>
                    </div>
                </div>                
                <div class="form-row">
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <input type="text" id="estado" name="estado" required>
                    </div>
                    <div class="form-group">
                        <label for="pais">País:</label>
                        <input type="text" id="pais" name="pais" required>
                    </div>
                </div>
                <div class="form-row">                
                    <div class="form-group">
                        <label for="codigoPostal">Código postal:</label>
                        <input type="text" id="codigoPostal" name="codigoPostal" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" required>
                    </div>
                </div>
                <button type="submit" class="register-btn">Registrarse</button>
                <p class="login-link">¿Ya tienes una cuenta? <a href="FrmSignIn.php">Inicia sesión aquí</a></p>
            </form>
    </div>
    
    <footer class="footer">
        <p>&copy; 2024 Joyería Suárez. Todos los derechos reservados.</p>
    </footer>
</body>
</html>