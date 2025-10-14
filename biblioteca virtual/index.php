<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joyería Suárez</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400&family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos/estilos.css">
    <script>
        let index = 0;
        function showSlide(step) {
            let slides = document.querySelectorAll(".carousel img");
            slides[index].classList.remove("active");
            index = (index + step + slides.length) % slides.length;
            slides[index].classList.add("active");
        }
        function autoSlide() {
            showSlide(1);
            setTimeout(autoSlide, 5000);
        }
        window.onload = function() {
            document.querySelectorAll(".carousel img")[0].classList.add("active");
            setTimeout(autoSlide, 5000);
        }
    </script>
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
        <a href="historia.php">Historia</a>
        <a href="tiendas.php">Tiendas</a>
        <a href="productos.php">Productos</a>
    </nav>
    <div class="container">
        <h2>Bienvenidos a Joyería Suárez</h2>
        <p>Descubra la elegancia y tradición de nuestras joyas, relojes y colecciones exclusivas.</p>
        <?php if(isset($_SESSION['client_email'])): ?>
            <p>Bienvenido de vuelta, <?php echo htmlspecialchars($_SESSION['client_email']); ?></p>
        <?php endif; ?>
        <div class="carousel">
            <img src="SJ_Imagenes/Colecciones/pe13078-or.jpg" alt="Colección 1">
            <img src="SJ_Imagenes/Joyas/GE11001-00AG.jpg" alt="Joya 1">
            <img src="SJ_Imagenes/Relojeria/relojes280x280_0000_ulysse-nardin1.jpg" alt="Reloj 1">
            <img src="SJ_Imagenes/SolitariosYAlianzas/al-9825-obdc-st.jpg" alt="Solitario 1">
            <img src="SJ_Imagenes/Colecciones/PU15014-ORSAV.jpg" alt="Colección 2">
            <img src="SJ_Imagenes/Joyas/so14007-ag-st.jpg" alt="Joya 2">
            <img src="SJ_Imagenes/Relojeria/relojes425x425_0001_patek-philippe.jpg" alt="Reloj 2">
            <img src="SJ_Imagenes/SolitariosYAlianzas/al12014-obd110b.png" alt="Solitario 2">
            <button class="prev" onclick="showSlide(-1)">&#10094;</button>
            <button class="next" onclick="showSlide(1)">&#10095;</button>
        </div>
        <div class="testimonials">
            <h3>Testimonios de Clientes</h3>
            <p><strong>María López:</strong> "Las joyas de Suárez son simplemente impresionantes. Elegancia y calidad en cada detalle."</p>
            <p><strong>Carlos Herrera:</strong> "Compré un reloj aquí y la experiencia fue excelente. Atención al cliente impecable."</p>
            <p><strong>Lucía Fernández:</strong> "Mi anillo de compromiso de Suárez es una joya espectacular. ¡Totalmente recomendado!"</p>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; 2024 Joyería Suárez. Todos los derechos reservados.</p>
    </footer>
</body>
</html>