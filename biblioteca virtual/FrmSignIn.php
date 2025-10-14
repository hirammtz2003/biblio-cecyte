<?php
session_start();

if(isset($_SESSION['client_email'])) {
    header("Location: index.php");
    exit();
}

if(isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    include_once "SQLClient.php";
    include_once "Client.php";
    
    $client = new Client();
    $client->setEmail($email);
    $client->setPassword($password);
    
    $sqlClient = new SQLClient();
    $loginResult = $sqlClient->signIn($client);
    
    if ($loginResult === 1){
        $_SESSION['client_email'] = $email;
        header("Location: index.php");
        exit();
    } else {
        $error_message = "El correo electrónico o la contraseña son incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Joyería Suárez</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400&family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos/estilos.css">
    <script type="text/javascript">
    function validate(){
        var email = document.loginForm.email.value;
        var password = document.loginForm.password.value;
        if (email == "") {
            alert("Por favor, ingresa tu correo electrónico.");
            return false;
        }
        if (password == "") {
            alert("Por favor, ingresa tu contraseña.");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>
    <header class="section-header">
        <h1>Joyería Suárez</h1>
    </header>
    
    <nav>
        <a href="index.php">INICIO</a>
    </nav>
    
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            
            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <h3>Error al iniciar sesión</h3>
                    <p><?php echo $error_message; ?></p>
                    <a href="FrmSignIn.php">Intentar de nuevo</a>
                </div>
            <?php endif; ?>
            
            <form method="post" action="FrmSignIn.php" name="loginForm" onsubmit="return validate()">
                <div class="form-group">
                    <label>Correo electrónico:</label>
                    <input type="email" name="email" placeholder="tu@email.com" required>
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" placeholder="Tu contraseña" required>
                </div>
                <button type="submit">Ingresar</button>
                <p>¿No tienes cuenta? <a href="FrmAddClient.php">Regístrate aquí</a></p>
            </form>
        </div>
    
    <footer class="footer">
        <p>&copy; 2024 Joyería Suárez. Todos los derechos reservados.</p>
    </footer>
</body>
</html>