<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "ropa");
if ($mysqli->connect_error) die("Error en la conexión: " . $mysqli->connect_error);

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitizar entradas
    $nombre     = trim($_POST["nombre"]);
    $apellido   = trim($_POST["apellido"]);
    $telefono   = trim($_POST["telefono"]);
    $direccion  = trim($_POST["direccion"]);
    $correo     = trim($_POST["correo"]);
    $password_plain = $_POST["password"];
    $fecha_nac  = $_POST["fecha_nac"];
    $genero     = $_POST["genero"];

    // Validaciones
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Correo no válido";
    } elseif (strlen($password_plain) < 6) {
        $errorMsg = "La contraseña debe tener al menos 6 caracteres";
    } else {
        // Revisar si ya existe ese correo para que no se repita
        $check = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        if ($check) {
            $check->bind_param("s", $correo);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $errorMsg = "Este correo ya está registrado. Intenta iniciar sesión.";
            }
            $check->close();
        }

        // Si no hay error, registrar
        if ($errorMsg === "") {

            $password = password_hash($password_plain, PASSWORD_BCRYPT);

            // Insert seguro
            $sql = "INSERT INTO usuarios 
                        (nombre, apellido, email, password, telefono, direccion, fecha_nacimiento, genero, rol, activo, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'cliente', 1, NOW())";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssssssss",
                    $nombre,
                    $apellido,
                    $correo,
                    $password,
                    $telefono,
                    $direccion,
                    $fecha_nac,
                    $genero
                );

                if ($stmt->execute()) {

                    // Guardar sesión del usuario recién creado
                    $_SESSION["cliente_id"] = $stmt->insert_id;
                    $_SESSION["cliente_nombre"] = $nombre;
                    $_SESSION["cliente_usuario"] = $correo;
                    $_SESSION["cliente_nivel"] = "cliente";

                    header("Location: bienvenida.php?nombre=" . urlencode($nombre));
                    exit();

                } else {
                    $errorMsg = "Error al registrar: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errorMsg = "Error al preparar el registro.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro - SHOP HOLY</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Montserrat:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/8f3b7d3f19.js" crossorigin="anonymous"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;}

body {
    font-family:'Montserrat', sans-serif;
    min-height:100vh;
    background:#0a0a0a;
    position:relative;
    overflow-x:hidden;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:60px 20px;
}

/* Fondo animado con gradiente dorado */
.bg-gradient {
    position:fixed;
    inset:0;
    background:
        radial-gradient(circle at 20% 20%, rgba(212,175,55,0.12), transparent 40%),
        radial-gradient(circle at 80% 80%, rgba(201,169,97,0.10), transparent 40%),
        radial-gradient(circle at 50% 50%, rgba(194,161,104,0.08), transparent 50%);
    animation:gradientRotate 20s ease infinite;
    z-index:0;
}
@keyframes gradientRotate {
    0%, 100% {transform:rotate(0deg) scale(1);}
    50% {transform:rotate(180deg) scale(1.08);}
}

/* Líneas decorativas elegantes */
.neon-lines {
    position:fixed;
    inset:0;
    z-index:1;
    overflow:hidden;
    pointer-events:none;
}

.line {
    position:absolute;
    background:linear-gradient(90deg, transparent, rgba(212,175,55,0.15), transparent);
    height:1px;
    width:100%;
    animation:lineMove 12s linear infinite;
}

.line:nth-child(1) {top:20%; animation-delay:0s;}
.line:nth-child(2) {top:50%; animation-delay:4s;}
.line:nth-child(3) {top:80%; animation-delay:8s;}

@keyframes lineMove {
    0% {transform:translateX(-100%); opacity:0;}
    50% {opacity:0.6;}
    100% {transform:translateX(100%); opacity:0;}
}

/* Partículas doradas */
.particle {
    position:fixed;
    width:2px;
    height:2px;
    background:#d4af37;
    border-radius:50%;
    box-shadow:0 0 10px rgba(212,175,55,0.6);
    animation:particleFloat 18s linear infinite;
    z-index:2;
}
@keyframes particleFloat {
    0% {transform:translateY(100vh) rotate(0deg); opacity:0;}
    10% {opacity:0.8;}
    90% {opacity:0.6;}
    100% {transform:translateY(-10vh) rotate(360deg); opacity:0;}
}

/* Container */
.register-container {
    position:relative;
    z-index:10;
    width:min(650px, 95%);
    background:linear-gradient(135deg, rgba(15,15,15,0.96), rgba(20,20,20,0.94));
    border:1px solid rgba(212,175,55,0.25);
    border-radius:12px;
    padding:50px 45px;
    backdrop-filter:blur(30px);
    box-shadow:
        0 25px 70px rgba(0,0,0,0.9),
        0 0 120px rgba(212,175,55,0.12),
        inset 0 1px 0 rgba(212,175,55,0.15);
    animation:containerPop 0.9s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes containerPop {
    0% {opacity:0; transform:scale(0.85) translateY(-80px);}
    100% {opacity:1; transform:scale(1) translateY(0);}
}

/* Header */
.header {
    text-align:center;
    margin-bottom:40px;
}

.brand-icon {
    width:85px;
    height:85px;
    margin:0 auto 22px;
    background:linear-gradient(135deg, #d4af37, #f4e4c1);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:38px;
    color:#0a0a0a;
    box-shadow:
        0 12px 35px rgba(212,175,55,0.4),
        0 0 60px rgba(212,175,55,0.2);
    animation:iconPulse 4s ease-in-out infinite;
    position:relative;
    overflow:hidden;
}
@keyframes iconPulse {
    0%, 100% {transform:scale(1);}
    50% {transform:scale(1.04);}
}

.brand-icon::before {
    content:'';
    position:absolute;
    top:-50%;
    left:-50%;
    width:200%;
    height:200%;
    background:linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
    animation:iconShine 4s linear infinite;
}
@keyframes iconShine {
    0% {transform:rotate(0deg);}
    100% {transform:rotate(360deg);}
}

.brand-title {
    font-family:'Playfair Display', serif;
    font-size:44px;
    font-weight:900;
    letter-spacing:8px;
    background:linear-gradient(135deg, #d4af37, #f4e4c1, #c9a961);
    background-size:200%;
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
    animation:titleGradient 4s ease infinite;
    margin-bottom:8px;
}
@keyframes titleGradient {
    0%, 100% {background-position:0% 50%;}
    50% {background-position:100% 50%;}
}

.subtitle {
    font-family:'Cormorant Garamond', serif;
    font-size:14px;
    color:#b8b5a8;
    letter-spacing:3px;
    text-transform:uppercase;
    font-weight:300;
    font-style:italic;
}

/* Form grid */
.form-grid {
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:20px;
    margin-bottom:18px;
}

.form-group {
    position:relative;
}

.form-group.full-width {
    grid-column:1 / -1;
}

.form-icon {
    position:absolute;
    left:20px;
    top:50%;
    transform:translateY(-50%);
    color:#c9a961;
    font-size:16px;
    transition:all 0.3s;
    z-index:1;
}

.form-input, .form-select {
    width:100%;
    padding:16px 22px 16px 52px;
    background:rgba(20,20,25,0.7);
    border:1px solid rgba(201,169,97,0.25);
    border-radius:8px;
    color:#f5f5f0;
    font-size:14px;
    font-family:'Montserrat', sans-serif;
    transition:all 0.3s;
    font-weight:400;
}

.form-input::placeholder {
    color:rgba(201,169,97,0.5);
    font-weight:300;
}

.form-select {
    cursor:pointer;
}

.form-select option {
    background:#1a1a1e;
    color:#f5f5f0;
    padding:12px;
}

.form-input:focus, .form-select:focus {
    outline:none;
    border-color:#d4af37;
    background:rgba(25,25,30,0.85);
    box-shadow:0 0 25px rgba(212,175,55,0.15);
    transform:translateY(-2px);
}

.form-input:focus + .form-icon,
.form-select:focus + .form-icon {
    color:#d4af37;
    transform:translateY(-50%) scale(1.1);
}

/* Submit button */
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
    box-shadow:0 12px 35px rgba(212,175,55,0.3);
    transition:all 0.4s cubic-bezier(0.34,1.56,0.64,1);
    position:relative;
    overflow:hidden;
    font-family:'Montserrat', sans-serif;
}

.submit-btn::before {
    content:'';
    position:absolute;
    top:50%;
    left:50%;
    width:0;
    height:0;
    border-radius:50%;
    background:rgba(255,255,255,0.25);
    transform:translate(-50%, -50%);
    transition:width 0.6s, height 0.6s;
}

.submit-btn:hover::before {
    width:350px;
    height:350px;
}

.submit-btn:hover {
    transform:translateY(-4px);
    box-shadow:0 16px 45px rgba(212,175,55,0.5);
    background:linear-gradient(135deg, #f4e4c1, #d4af37);
}

.submit-btn:active {
    transform:translateY(-1px);
}

/* Links */
.links {
    display:flex;
    justify-content:center;
    gap:18px;
    margin-top:28px;
    flex-wrap:wrap;
}

.link-btn {
    color:#c9a961;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
    padding:12px 24px;
    border:1px solid rgba(201,169,97,0.3);
    border-radius:8px;
    background:rgba(20,20,25,0.6);
    transition:all 0.3s;
    font-family:'Montserrat', sans-serif;
}

.link-btn:hover {
    color:#d4af37;
    border-color:#c9a961;
    background:rgba(25,25,30,0.8);
    transform:translateY(-2px);
    box-shadow:0 6px 22px rgba(212,175,55,0.2);
}

/* Back button */
.back-btn {
    position:fixed;
    top:35px;
    left:35px;
    padding:15px 30px;
    background:rgba(20,20,25,0.9);
    border:1px solid rgba(201,169,97,0.3);
    border-radius:8px;
    color:#c9a961;
    text-decoration:none;
    font-weight:600;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:10px;
    backdrop-filter:blur(20px);
    box-shadow:0 6px 25px rgba(0,0,0,0.5);
    transition:all 0.3s;
    z-index:100;
    animation:slideInLeft 0.9s ease;
    font-family:'Montserrat', sans-serif;
}
@keyframes slideInLeft {
    from {opacity:0; transform:translateX(-50px);}
    to {opacity:1; transform:translateX(0);}
}

.back-btn:hover {
    background:rgba(201,169,97,0.15);
    border-color:#d4af37;
    transform:translateX(-5px);
    box-shadow:0 8px 28px rgba(212,175,55,0.3);
    color:#d4af37;
}

/* Error message */
.error-msg {
    background:rgba(200,70,70,0.12);
    border:1px solid rgba(200,70,70,0.35);
    color:#ff9a9a;
    padding:16px;
    border-radius:8px;
    text-align:center;
    margin-bottom:28px;
    font-weight:500;
    font-size:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:12px;
    animation:errorShake 0.5s;
}
@keyframes errorShake {
    0%, 100% {transform:translateX(0);}
    25% {transform:translateX(-8px);}
    75% {transform:translateX(8px);}
}

/* Responsive */
@media(max-width:700px) {
    .form-grid {
        grid-template-columns:1fr;
    }
    
    .register-container {
        padding:40px 28px;
    }
    
    .brand-title {
        font-size:38px;
        letter-spacing:6px;
    }
    
    .brand-icon {
        width:72px;
        height:72px;
        font-size:32px;
    }
    
    .back-btn {
        top:20px;
        left:20px;
        padding:12px 22px;
        font-size:13px;
    }
    
    .links {
        flex-direction:column;
        align-items:center;
    }
    
    .link-btn {
        width:100%;
        text-align:center;
    }
}

@media(max-width:400px) {
    body {
        padding:40px 10px;
    }
    
    .register-container {
        padding:35px 22px;
    }
    
    .brand-title {
        font-size:32px;
    }
}
</style>
</head>
<body>

<div class="bg-gradient"></div>

<div class="neon-lines">
    <div class="line"></div>
    <div class="line"></div>
    <div class="line"></div>
</div>

<!-- Partículas -->
<script>
for(let i = 0; i < 35; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + 'vw';
    particle.style.animationDelay = Math.random() * 6 + 's';
    particle.style.animationDuration = (14 + Math.random() * 10) + 's';
    document.body.appendChild(particle);
}
</script>

<a href="index.php" class="back-btn">
    <i class="fas fa-arrow-left"></i> Volver
</a>

<div class="register-container">
    <?php if($errorMsg): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i>
            <?= $errorMsg ?>
        </div>
    <?php endif; ?>
    
    <div class="header">
        <div class="brand-icon">
            <i class="fas fa-tshirt"></i>
        </div>
        <h1 class="brand-title">SHOP HOLY</h1>
        <p class="subtitle">Crea tu cuenta</p>
    </div>
    
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <input type="text" name="nombre" class="form-input" placeholder="Nombre" required>
                <i class="fas fa-user form-icon"></i>
            </div>
            
            <div class="form-group">
                <input type="text" name="apellido" class="form-input" placeholder="Apellido" required>
                <i class="fas fa-user-tag form-icon"></i>
            </div>
            
            <div class="form-group full-width">
                <input type="email" name="correo" class="form-input" placeholder="Correo electrónico" required>
                <i class="fas fa-envelope form-icon"></i>
            </div>
            
            <div class="form-group full-width">
                <input type="password" name="password" class="form-input" placeholder="Contraseña (mínimo 6 caracteres)" required>
                <i class="fas fa-lock form-icon"></i>
            </div>
            
            <div class="form-group">
                <input type="tel" name="telefono" class="form-input" placeholder="Teléfono" required>
                <i class="fas fa-phone form-icon"></i>
            </div>
            
            <div class="form-group">
                <input type="date" name="fecha_nac" class="form-input" required>
                <i class="fas fa-calendar form-icon"></i>
            </div>
            
            <div class="form-group full-width">
                <input type="text" name="direccion" class="form-input" placeholder="Dirección de envío" required>
                <i class="fas fa-map-marker-alt form-icon"></i>
            </div>
            
            <div class="form-group full-width">
                <select name="genero" class="form-select" required>
                    <option value="">Selecciona tu género</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
                <i class="fas fa-venus-mars form-icon"></i>
            </div>
        </div>
        
        <button type="submit" class="submit-btn">
            <i class="fas fa-user-plus"></i> Crear Cuenta
        </button>
        
        <div class="links">
            <a href="iniciar_sesion.php" class="link-btn">
                <i class="fas fa-sign-in-alt"></i> Ya tengo cuenta
            </a>
        </div>
    </form>
</div>

</body>
</html>