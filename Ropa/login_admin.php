<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ropa");
if ($conn->connect_error) die("Error: " . $conn->connect_error);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];
    
    // Validación actualizada: usuario "itzel" y contraseña "1234"
    if ($usuario === 'itzel' && $password === '1234') {
        $_SESSION["admin_id"] = 1;
        $_SESSION["admin_usuario"] = 'itzel';
        $_SESSION["admin_nombre"] = 'Itzel';
        header("Location: panel.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - SHOP HOLY</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Montserrat', sans-serif;
    background: #0a0a0a;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at 20% 50%, rgba(212,175,55,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,215,0,0.08) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.login-container {
    background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
    border: 2px solid rgba(212,175,55,0.3);
    border-radius: 25px;
    padding: 50px 40px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8),
                0 0 100px rgba(212,175,55,0.1);
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.login-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #d4af37, #ffd700, #d4af37);
}

.login-container::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(212,175,55,0.05) 0%, transparent 70%);
    pointer-events: none;
}

.logo-section {
    text-align: center;
    margin-bottom: 40px;
    position: relative;
    z-index: 1;
}

.logo {
    font-family: 'Playfair Display', serif;
    font-size: 42px;
    font-weight: 900;
    background: linear-gradient(135deg, #d4af37, #ffd700, #f4e5c3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 3px;
    margin-bottom: 10px;
    text-shadow: 0 0 30px rgba(212,175,55,0.3);
}

.logo-subtitle {
    color: #999;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 3px;
    font-weight: 600;
}

.admin-badge {
    background: linear-gradient(135deg, #d4af37, #ffd700);
    color: #000;
    padding: 10px 25px;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(212,175,55,0.4);
    position: relative;
    z-index: 1;
}

.form-group {
    margin-bottom: 25px;
    position: relative;
    z-index: 1;
}

.form-label {
    display: block;
    color: #d4af37;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.input-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #d4af37;
    font-size: 18px;
}

.form-input {
    width: 100%;
    padding: 16px 20px 16px 50px;
    border: 1px solid rgba(212,175,55,0.3);
    border-radius: 15px;
    font-size: 15px;
    font-family: 'Montserrat', sans-serif;
    transition: 0.3s;
    outline: none;
    color: #fff;
    font-weight: 500;
    background: rgba(212,175,55,0.05);
}

.form-input:focus {
    border-color: #d4af37;
    box-shadow: 0 0 20px rgba(212,175,55,0.2);
    background: rgba(212,175,55,0.1);
}

.form-input::placeholder {
    color: rgba(255,255,255,0.3);
}

.btn-login {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #d4af37, #ffd700);
    color: #000;
    border: none;
    border-radius: 15px;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 6px 20px rgba(212,175,55,0.4);
    margin-top: 10px;
    position: relative;
    z-index: 1;
}

.btn-login:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(212,175,55,0.6);
}

.btn-login:active {
    transform: translateY(-1px);
}

.error-message {
    background: rgba(231,76,60,0.1);
    color: #e74c3c;
    padding: 15px;
    border-radius: 10px;
    margin-top: 20px;
    font-size: 14px;
    font-weight: 600;
    border-left: 4px solid #e74c3c;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 1;
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 30px 0;
    color: #666;
    font-size: 13px;
    position: relative;
    z-index: 1;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid rgba(212,175,55,0.2);
}

.divider span {
    padding: 0 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.back-link {
    text-align: center;
    margin-top: 25px;
    position: relative;
    z-index: 1;
}

.back-link a {
    color: #d4af37;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}

.back-link a:hover {
    gap: 12px;
    color: #ffd700;
}

.security-note {
    background: rgba(212,175,55,0.05);
    padding: 15px;
    border-radius: 10px;
    margin-top: 25px;
    text-align: center;
    color: #999;
    font-size: 12px;
    border: 1px solid rgba(212,175,55,0.2);
    position: relative;
    z-index: 1;
}

.security-note i {
    color: #d4af37;
    margin-right: 5px;
}

@media (max-width: 500px) {
    .login-container {
        margin: 20px;
        padding: 40px 30px;
    }
    
    .logo {
        font-size: 36px;
    }
}
</style>
</head>
<body>

<div class="login-container">
    <div class="logo-section">
        <div class="logo">SHOP HOLY</div>
    </div>

    <div style="text-align: center;">
        <div class="admin-badge">
            <i class="fas fa-shield-alt"></i>
            Panel de Administración
        </div>
    </div>

    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label">Usuario</label>
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="usuario" class="form-input" placeholder="Ingresa tu usuario" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="form-input" placeholder="Ingresa tu contraseña" required>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>

        <!-- Descomentar para q muestre el error -->
        <!-- <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            Usuario o contraseña incorrectos.
        </div> -->
    </form>

    <div class="divider">
        <span>Acceso Seguro</span>
    </div>

    <div class="security-note">
        <i class="fas fa-lock"></i>
        Esta es una zona restringida. Solo personal autorizado.
    </div>

    <div class="back-link">
        <a href="index.php">
            <i class="fas fa-arrow-left"></i>
            Volver a la tienda
        </a>
    </div>
</div>

</body>
</html>