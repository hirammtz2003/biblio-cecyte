<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
ob_start('mb_output_handler');

session_start();
if (!isset($_SESSION["client_email"])){
    header("Location: FrmSignIn.php");
    exit;
}

include_once "SQLClient.php";
include_once "Client.php";

$clientEmail = $_SESSION["client_email"];
$sqlClient = new SQLClient();
$clientData = $sqlClient->getClientByEmail($clientEmail);

// Estados posibles:
// - Ninguno: Mostrar datos normales
// - password_requested: Mostrar campo para contraseña
// - password_verified: Mostrar formulario de edición
$editState = 'none';
$passwordError = false;

// Procesar solicitud de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["edit_mode"])) {
        // Solicitud para entrar en modo edición
        $editState = 'password_requested';
    } 
    elseif (isset($_POST["verify_password"])) {
        // Verificación de contraseña
        $password = $_POST["password"] ?? '';
        $tempClient = new Client();
        $tempClient->setEmail($clientEmail);
        $tempClient->setPassword($password);
        if ($sqlClient->signIn($tempClient)) {
            $editState = 'password_verified';
        } else {
            $passwordError = true;
            $editState = 'password_requested';
        }
    }
    elseif (isset($_POST["save_changes"])) {
        // Guardar cambios después de edición
        $updatedData = [
            'name' => $_POST["name"] ?? '',
            'lastName' => $_POST["lastName"] ?? '',
            'email' => $_POST["email"] ?? '',
            'address' => $_POST["address"] ?? '',
            'colonia' => $_POST["colonia"] ?? '',
            'city' => $_POST["city"] ?? '',
            'state' => $_POST["state"] ?? '',
            'country' => $_POST["country"] ?? '',
            'postalCode' => $_POST["postalCode"] ?? '',
            'phone' => $_POST["phone"] ?? '',
            'password' => $_POST["new_password"] ?? '' // Añadir esto para la contraseña
        ];
        
        if ($sqlClient->updateClient($clientEmail, $updatedData)) {
            $clientData = $sqlClient->getClientByEmail($clientEmail);
            $editState = 'none';
        } else {
            $error = "Error al actualizar los datos. Por favor intenta nuevamente.";
            $editState = 'password_verified';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta - Joyería Suárez</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400&family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos/estilos.css">
    <style>
        .account-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .client-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .client-table th, .client-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .client-table th {
            background-color: #f8f8f8;
            width: 30%;
        }
        .logout-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #c9302c;
        }
        .edit-btn {
            background-color: #5bc0de;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .edit-btn:hover {
            background-color: #46b8da;
        }
        .save-btn {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .save-btn:hover {
            background-color: #4cae4c;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error-message {
            color: #d9534f;
            margin-bottom: 15px;
        }
        .password-prompt {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f8f8;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="section-header">
        <h1>Joyería Suárez</h1>
        <div class="user-area">
            <?php if(isset($_SESSION['client_email'])): ?>
                <?php
                    // Obtener datos del cliente
                    include_once "SQLClient.php";
                    $sqlClient = new SQLClient();
                    $client = $sqlClient->getClientByEmail($_SESSION['client_email']);
                ?>
                <?php if($client && $client->isAdmin()): ?>
                    <span class="admin-icon"></span>
                    <a href="admin.php" class="user-button">Administración</a>
                    <span style="margin: 0 5px;">|</span>
                <?php endif; ?>
                <span class="user-icon"></span>
                <a href="miCuenta.php" class="user-button">Mi cuenta</a>
            <?php else: ?>
                <span class="user-icon"></span>
                <a href="FrmSignIn.php" class="user-button">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </header>
    <nav>
        <a href="index.php">INICIO</a>
    </nav>
    
    <div class="container">
        <div class="account-container">
            <h2>Mi Cuenta</h2>
            
            <?php if(isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <!-- Mostrar datos del cliente SOLO cuando no esté en modo edición -->
            <?php if($clientData && $editState !== 'password_verified'): ?>
                <table class="client-table">
                    <tr>
                        <th>Nombre:</th>
                        <td><?php echo htmlspecialchars($clientData->getName()); ?></td>
                    </tr>
                    <tr>
                        <th>Apellidos:</th>
                        <td><?php echo htmlspecialchars($clientData->getLastName()); ?></td>
                    </tr>
                    <tr>
                        <th>Correo electrónico:</th>
                        <td><?php echo htmlspecialchars($clientData->getEmail()); ?></td>
                    </tr>
                    <tr>
                        <th>Calle:</th>
                        <td><?php echo htmlspecialchars($clientData->getAddress()); ?></td>
                    </tr>
                    <tr>
                        <th>Colonia:</th>
                        <td><?php echo htmlspecialchars($clientData->getColonia()); ?></td>
                    </tr>
                    <tr>
                        <th>Ciudad:</th>
                        <td><?php echo htmlspecialchars($clientData->getCity()); ?></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td><?php echo htmlspecialchars($clientData->getState()); ?></td>
                    </tr>
                    <tr>
                        <th>País:</th>
                        <td><?php echo htmlspecialchars($clientData->getCountry()); ?></td>
                    </tr>
                    <tr>
                        <th>Código postal:</th>
                        <td><?php echo htmlspecialchars($clientData->getPostalCode()); ?></td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td><?php echo htmlspecialchars($clientData->getPhone()); ?></td>
                    </tr>
                </table>
            <?php endif; ?>
            
            <!-- Formulario para solicitar edición -->
            <?php if($editState === 'none'): ?>
                <form method="post">
                    <input type="hidden" name="edit_mode" value="1">
                    <button type="submit" class="edit-btn">Editar mis datos</button>
                </form>
            <?php endif; ?>

            <!-- Solicitud de contraseña -->
            <?php if($editState === 'password_requested'): ?>
                <form method="post" class="password-prompt">
                    <h3>Verificación de seguridad</h3>
                    <p>Por favor ingresa tu contraseña para continuar:</p>
                    <?php if($passwordError): ?>
                        <p class="error-message">Contraseña incorrecta. Intenta nuevamente.</p>
                    <?php endif; ?>
                    <input type="password" name="password" required placeholder="Ingresa tu contraseña">
                    <input type="hidden" name="verify_password" value="1">
                    <button type="submit" class="save-btn">Verificar</button>
                    <button type="button" class="logout-btn" onclick="window.location.href='miCuenta.php'">Cancelar</button>
                </form>
            <?php endif; ?>

            <!-- Formulario de edición SOLO cuando esté verificado -->
            <?php if($editState === 'password_verified'): ?>
                <form method="post">
                    <table class="client-table">
                        <tr>
                            <th>Nombre:</th>
                            <td>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($clientData->getName()); ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th>Apellidos:</th>
                            <td>
                                <input type="text" name="lastName" value="<?php echo htmlspecialchars($clientData->getLastName()); ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th>Correo electrónico:</th>
                            <td>
                                <input type="text" name="email" value="<?php echo htmlspecialchars($clientData->getEmail()); ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th>Cambiar contraseña:</th>
                            <td>
                                <input type="password" name="new_password" placeholder="Dejar en blanco para no cambiar">
                            </td>
                        </tr>
                        <tr>
                            <th>Calle:</th>
                            <td>
                                <input type="text" name="address" value="<?php echo htmlspecialchars($clientData->getAddress()); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Colonia:</th>
                            <td>
                                <input type="text" name="colonia" value="<?php echo htmlspecialchars($clientData->getColonia()); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Ciudad:</th>
                            <td>
                                <input type="text" name="city" value="<?php echo htmlspecialchars($clientData->getCity()); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <input type="text" name="state" value="<?php echo htmlspecialchars($clientData->getState()); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>País:</th>
                            <td>
                                <input type="text" name="country" value="<?php echo htmlspecialchars($clientData->getCountry()); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Código postal:</th>
                            <td>
                                <input type="text" name="postalCode" value="<?php echo htmlspecialchars($clientData->getPostalCode()); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($clientData->getPhone()); ?>">
                            </td>
                        </tr>
                    </table>
                    
                    <input type="hidden" name="save_changes" value="1">
                    <button type="submit" class="save-btn">Guardar cambios</button>
                    <button type="button" class="logout-btn" onclick="window.location.href='miCuenta.php'">Cancelar</button>
                </form>
            <?php endif; ?>

            <form action="FrmLogout.php" method="post">
                <button type="submit" class="logout-btn">Cerrar Sesión</button>
            </form>                

        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2024 Joyería Suárez. Todos los derechos reservados.</p>
    </footer>
</body>
</html>