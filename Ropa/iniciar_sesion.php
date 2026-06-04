<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "ropa");
if ($mysqli->connect_error) die("Error en la conexión: " . $mysqli->connect_error);

$errorMsg = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["usuario"]); 
    $password = trim($_POST["password"]);

    // Buscamos en tabla 'usuarios' por email, rol cliente y activo
    $sql = "SELECT id, nombre, apellido, email, password, rol 
            FROM usuarios 
            WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))
              AND rol = 'cliente'
              AND activo = 1
            LIMIT 1";

    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row["password"])) {
                $_SESSION["cliente_id"]      = $row["id"];
                $_SESSION["cliente_nombre"]  = $row["nombre"];
                $_SESSION["cliente_usuario"] = $row["email"];
                $_SESSION["cliente_nivel"]   = $row["rol"];

                $successMsg = "¡Bienvenido/a <b>" . htmlspecialchars($row["nombre"]) . "</b>!<br>
                               Tu sesión se inició correctamente.<br>
                               Preparando tu experiencia de compra...";
            } else {
                $errorMsg = "Contraseña incorrecta";
            }
        } else {
            $errorMsg = "Usuario no encontrado o inactivo";
        }

        $stmt->close();
    } else {
        $errorMsg = "Error al preparar la consulta.";
    }
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión - SHOP HOLY</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Montserrat:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/8f3b7d3f19.js" crossorigin="anonymous"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;}

body {
    font-family:'Montserrat', sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#0a0a0a;
    position:relative;
    overflow:hidden;
}

/* Video de fondo */
.video-bg {
    position:fixed;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
    z-index:0;
    filter:brightness(0.25) contrast(1.1);
}

/* Overlay con gradiente dorado */
.overlay {
    position:fixed;
    inset:0;
    background:linear-gradient(135deg, rgba(194,161,104,0.15), rgba(10,10,10,0.9));
    z-index:1;
    backdrop-filter:blur(8px);
}

/* Efecto de grid elegante */
.grid-overlay {
    position:fixed;
    inset:0;
    background-image:
        linear-gradient(rgba(212,175,55,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(212,175,55,0.03) 1px, transparent 1px);
    background-size:60px 60px;
    z-index:2;
    animation:gridMove 25s linear infinite;
}
@keyframes gridMove {
    0% {transform:translateY(0);}
    100% {transform:translateY(60px);}
}

/* Partículas doradas flotantes */
.particle {
    position:fixed;
    width:3px;
    height:3px;
    background:#d4af37;
    border-radius:50%;
    filter:blur(1px);
    box-shadow:0 0 15px rgba(212,175,55,0.6);
    animation:floatUp 20s linear infinite;
    z-index:3;
}
@keyframes floatUp {
    0% {transform:translateY(100vh) translateX(0); opacity:0;}
    10% {opacity:0.8;}
    90% {opacity:0.6;}
    100% {transform:translateY(-10vh) translateX(40px); opacity:0;}
}

/* Contenedor principal */
.login-container {
    position:relative;
    z-index:10;
    width:min(480px, 92%);
    background:linear-gradient(135deg, rgba(15,15,15,0.95), rgba(20,20,20,0.92));
    border:1px solid rgba(212,175,55,0.25);
    border-radius:12px;
    padding:55px 45px;
    backdrop-filter:blur(25px);
    box-shadow:
        0 20px 60px rgba(0,0,0,0.8),
        0 0 100px rgba(212,175,55,0.15),
        inset 0 1px 0 rgba(212,175,55,0.1);
    animation:slideIn 0.9s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes slideIn {
    from {opacity:0; transform:scale(0.92) translateY(-40px);}
    to {opacity:1; transform:scale(1) translateY(0);}
}

/* Logo */
.logo-section {
    text-align:center;
    margin-bottom:40px;
}

.brand-icon {
    width:90px;
    height:90px;
    margin:0 auto 25px;
    background:linear-gradient(135deg, #d4af37, #f4e4c1);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:40px;
    color:#0a0a0a;
    box-shadow:
        0 12px 35px rgba(212,175,55,0.4),
        0 0 60px rgba(212,175,55,0.2);
    animation:logoFloat 4s ease-in-out infinite;
    position:relative;
    overflow:hidden;
}
@keyframes logoFloat {
    0%, 100% {transform:translateY(0) rotate(0deg);}
    50% {transform:translateY(-8px) rotate(3deg);}
}

.brand-icon::before {
    content:'';
    position:absolute;
    inset:-50%;
    background:linear-gradient(45deg, transparent, rgba(255,255,255,0.4), transparent);
    animation:shine 3s linear infinite;
}
@keyframes shine {
    from {transform:translateX(-100%) rotate(45deg);}
    to {transform:translateX(100%) rotate(45deg);}
}

.brand-name {
    font-family:'Playfair Display', serif;
    font-size:46px;
    font-weight:900;
    letter-spacing:8px;
    background:linear-gradient(135deg, #d4af37, #f4e4c1, #c9a961);
    background-size:200%;
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
    animation:gradientShift 4s ease infinite;
    margin-bottom:8px;
}
@keyframes gradientShift {
    0%, 100% {background-position:0% 50%;}
    50% {background-position:100% 50%;}
}

.brand-tagline {
    font-family:'Cormorant Garamond', serif;
    font-size:13px;
    letter-spacing:4px;
    color:#b8b5a8;
    text-transform:uppercase;
    font-weight:300;
    font-style:italic;
}

/* Título */
.form-title {
    text-align:center;
    font-family:'Playfair Display', serif;
    font-size:30px;
    font-weight:600;
    color:#f5f5f0;
    letter-spacing:3px;
    margin-bottom:35px;
    text-transform:uppercase;
}

/* Inputs */
.input-wrapper {
    position:relative;
    margin-bottom:22px;
}

.input-icon {
    position:absolute;
    left:20px;
    top:50%;
    transform:translateY(-50%);
    color:#c9a961;
    font-size:17px;
    transition:all 0.3s;
}

.input-field {
    width:100%;
    padding:18px 22px 18px 56px;
    background:rgba(20,20,25,0.6);
    border:1px solid rgba(201,169,97,0.25);
    border-radius:8px;
    color:#f5f5f0;
    font-size:15px;
    font-family:'Montserrat', sans-serif;
    transition:all 0.3s;
    font-weight:400;
}

.input-field::placeholder {
    color:rgba(201,169,97,0.5);
    font-weight:300;
}

.input-field:focus {
    outline:none;
    border-color:#d4af37;
    background:rgba(25,25,30,0.8);
    box-shadow:0 0 25px rgba(212,175,55,0.15);
    transform:translateY(-2px);
}

.input-field:focus + .input-icon {
    color:#d4af37;
    transform:translateY(-50%) scale(1.1);
}

/* Botón submit */
.submit-btn {
    width:100%;
    padding:19px;
    background:linear-gradient(135deg, #d4af37, #c9a961);
    border:none;
    border-radius:8px;
    color:#0a0a0a;
    font-size:15px;
    font-weight:700;
    letter-spacing:3px;
    text-transform:uppercase;
    cursor:pointer;
    margin-top:28px;
    box-shadow:0 10px 30px rgba(212,175,55,0.3);
    transition:all 0.4s cubic-bezier(0.34,1.56,0.64,1);
    position:relative;
    overflow:hidden;
    font-family:'Montserrat', sans-serif;
}

.submit-btn::before {
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(135deg, rgba(255,255,255,0.25), transparent);
    transform:translateX(-100%);
    transition:transform 0.6s;
}

.submit-btn:hover::before {
    transform:translateX(100%);
}

.submit-btn:hover {
    transform:translateY(-3px);
    box-shadow:0 15px 40px rgba(212,175,55,0.5);
    background:linear-gradient(135deg, #f4e4c1, #d4af37);
}

.submit-btn:active {
    transform:translateY(0);
}

/* Link registro */
.register-link {
    text-align:center;
    margin-top:28px;
    font-size:14px;
    color:#b8b5a8;
}

.register-link a {
    color:#d4af37;
    text-decoration:none;
    font-weight:600;
    border-bottom:1px solid transparent;
    transition:all 0.3s;
    padding-bottom:2px;
}

.register-link a:hover {
    color:#f4e4c1;
    border-bottom-color:#d4af37;
}

/* Botón volver */
.back-btn {
    position:fixed;
    top:35px;
    left:35px;
    padding:15px 30px;
    background:rgba(20,20,25,0.85);
    border:1px solid rgba(201,169,97,0.3);
    border-radius:8px;
    color:#c9a961;
    text-decoration:none;
    font-weight:600;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:10px;
    backdrop-filter:blur(15px);
    box-shadow:0 6px 20px rgba(0,0,0,0.4);
    transition:all 0.3s;
    z-index:100;
    font-family:'Montserrat', sans-serif;
}

.back-btn:hover {
    background:rgba(201,169,97,0.15);
    border-color:#d4af37;
    transform:translateX(-5px);
    box-shadow:0 8px 25px rgba(212,175,55,0.25);
    color:#d4af37;
}

/* Mensajes */
.error-msg {
    background:rgba(200,60,60,0.12);
    border:1px solid rgba(200,60,60,0.3);
    color:#ff8a8a;
    padding:16px;
    border-radius:8px;
    text-align:center;
    margin-bottom:22px;
    font-weight:500;
    font-size:14px;
    animation:shake 0.5s;
}
@keyframes shake {
    0%, 100% {transform:translateX(0);}
    25% {transform:translateX(-8px);}
    75% {transform:translateX(8px);}
}

.success-msg {
    background:rgba(100,180,100,0.12);
    border:1px solid rgba(100,180,100,0.3);
    color:#a8e6a8;
    padding:16px;
    border-radius:8px;
    text-align:center;
    margin-bottom:22px;
    font-weight:500;
    font-size:14px;
    animation:successPop 0.6s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes successPop {
    0% {transform:scale(0.85); opacity:0;}
    100% {transform:scale(1); opacity:1;}
}

/* Contador de redirección */
.redirect-counter {
    text-align:center;
    margin-top:30px;
    padding:30px;
    background:rgba(201,169,97,0.08);
    border:1px solid rgba(201,169,97,0.2);
    border-radius:8px;
}

.counter-number {
    font-family:'Playfair Display', serif;
    font-size:68px;
    font-weight:700;
    background:linear-gradient(135deg, #d4af37, #f4e4c1);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
    animation:pulse 1s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% {transform:scale(1);}
    50% {transform:scale(1.08);}
}

.counter-text {
    color:#b8b5a8;
    font-size:14px;
    margin-top:12px;
    letter-spacing:2px;
    font-weight:300;
}

.menu-btn {
    display:inline-block;
    margin-top:22px;
    padding:15px 35px;
    background:linear-gradient(135deg, #d4af37, #c9a961);
    color:#0a0a0a;
    text-decoration:none;
    border-radius:8px;
    font-weight:700;
    font-size:14px;
    letter-spacing:2px;
    text-transform:uppercase;
    box-shadow:0 8px 25px rgba(212,175,55,0.3);
    transition:all 0.3s;
    font-family:'Montserrat', sans-serif;
}

.menu-btn:hover {
    transform:translateY(-3px);
    box-shadow:0 12px 35px rgba(212,175,55,0.5);
    background:linear-gradient(135deg, #f4e4c1, #d4af37);
}

/* Responsive */
@media(max-width:600px) {
    .login-container {
        padding:45px 32px;
    }
    
    .brand-name {
        font-size:38px;
        letter-spacing:6px;
    }
    
    .brand-icon {
        width:75px;
        height:75px;
        font-size:32px;
    }
    
    .back-btn {
        top:20px;
        left:20px;
        padding:12px 22px;
        font-size:13px;
    }
    
    .form-title {
        font-size:26px;
    }
}
</style>
</head>
<body>

<!-- Video de fondo (puedes cambiar el src por un video de moda) -->
<video class="video-bg" autoplay muted loop playsinline>
    <source src="https://assets.mixkit.co/videos/preview/mixkit-people-walking-in-a-shopping-mall-4561-large.mp4" type="video/mp4">
</video>

<div class="overlay"></div>
<div class="grid-overlay"></div>

<!-- Partículas -->
<script>
for(let i = 0; i < 25; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + 'vw';
    particle.style.animationDelay = Math.random() * 6 + 's';
    particle.style.animationDuration = (15 + Math.random() * 10) + 's';
    document.body.appendChild(particle);
}
</script>

<a href="index.php" class="back-btn">
    <i class="fas fa-arrow-left"></i> Volver
</a>

<div class="login-container">
    <?php if($errorMsg): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-triangle"></i> <?= $errorMsg ?>
        </div>
    <?php endif; ?>

    <?php if($successMsg): ?>
        <div class="success-msg">
            <i class="fas fa-check-circle"></i> <?= $successMsg ?>
        </div>
        
        <div class="redirect-counter">
            <div class="counter-number" id="countdown">10</div>
            <div class="counter-text">Redirigiendo automáticamente...</div>
            <a href="ropa.php" class="menu-btn">
                <i class="fas fa-shopping-bag"></i> Ir a comprar ahora
            </a>
        </div>
        
        <script>
    let count = 10;
    const countdownElement = document.getElementById('countdown');

    const interval = setInterval(() => {
        count--;
        countdownElement.textContent = count;

        if (count <= 0) {
            clearInterval(interval);
            window.location.href = 'ropa.php';
        }
    }, 1000);
</script>

    <?php else: ?>
    
    <div class="logo-section">
        <div class="brand-icon">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <h1 class="brand-name">SHOP HOLY</h1>
    </div>
    
    <h2 class="form-title">Iniciar Sesión</h2>
    
    <form method="POST">
        <div class="input-wrapper">
            <input type="email" name="usuario" class="input-field" placeholder="Correo electrónico" required>
            <i class="fas fa-envelope input-icon"></i>
        </div>
        
        <div class="input-wrapper">
            <input type="password" name="password" class="input-field" placeholder="Contraseña" required>
            <i class="fas fa-lock input-icon"></i>
        </div>
        
        <button type="submit" class="submit-btn">
            <i class="fas fa-sign-in-alt"></i> Entrar
        </button>
        
        <div class="register-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </form>
    
    <?php endif; ?>
</div>

</body>
</html>