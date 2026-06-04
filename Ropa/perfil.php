<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "ropa");
if($conexion->connect_error) die("Error de conexión: " . $conexion->connect_error);

// Simulación de usuario (puedes adaptar esto a tu sistema de login)
if(!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1; // ID ficticio
    $_SESSION['usuario_nombre'] = 'Usuario Demo';
    $_SESSION['usuario_email'] = 'demo@shopholy.com';
}

$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_email = $_SESSION['usuario_email'];

// Obtener historial de pedidos (simulado)
$pedidos = [
    ['id' => 1001, 'fecha' => '2024-12-01', 'total' => 1250.00, 'estado' => 'Entregado'],
    ['id' => 1002, 'fecha' => '2024-12-05', 'total' => 850.50, 'estado' => 'En tránsito'],
    ['id' => 1003, 'fecha' => '2024-12-08', 'total' => 2100.00, 'estado' => 'Procesando']
];

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi Perfil - SHOP HOLY</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    color: #fff;
}

.navbar {
    background: rgba(255,255,255,0.95);
    padding: 20px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.logo {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 900;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 2px;
}

.nav-links a {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    margin-left: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: 0.3s;
}

.nav-links a:hover {
    color: #667eea;
}

.container {
    max-width: 1200px;
    margin: 60px auto;
    padding: 0 20px;
}

.profile-header {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 30px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #fff;
    font-weight: 700;
}

.profile-info h1 {
    color: #2c3e50;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 10px;
}

.profile-info p {
    color: #666;
    font-size: 16px;
}

.profile-info .email {
    color: #667eea;
    font-weight: 600;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.sidebar {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    height: fit-content;
}

.menu-item {
    padding: 15px 20px;
    margin-bottom: 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    gap: 15px;
    color: #2c3e50;
    font-weight: 600;
}

.menu-item i {
    width: 20px;
    text-align: center;
}

.menu-item:hover, .menu-item.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
}

.main-content {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.section-title {
    color: #2c3e50;
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
}

.order-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    border-left: 5px solid #667eea;
    transition: 0.3s;
}

.order-card:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.order-id {
    color: #2c3e50;
    font-weight: 700;
    font-size: 18px;
}

.order-status {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.status-entregado {
    background: #d4edda;
    color: #155724;
}

.status-transito {
    background: #fff3cd;
    color: #856404;
}

.status-procesando {
    background: #cce5ff;
    color: #004085;
}

.order-details {
    display: flex;
    justify-content: space-between;
    color: #666;
    font-size: 14px;
}

.order-total {
    color: #e74c3c;
    font-weight: 800;
    font-size: 20px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    padding: 12px 30px;
    border-radius: 50px;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(102,126,234,0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102,126,234,0.5);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    color: #fff;
}

.stat-number {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

@media (max-width: 968px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo">SHOP HOLY</div>
    <div class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
        <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
        <a href="perfil.php"><i class="fas fa-user"></i> Perfil</a>
    </div>
</nav>

<div class="container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($usuario_nombre, 0, 1)); ?>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($usuario_nombre); ?></h1>
            <p class="email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario_email); ?></p>
            <p><i class="fas fa-calendar"></i> Miembro desde Diciembre 2024</p>
        </div>
    </div>

    <div class="content-grid">
        <div class="sidebar">
            <div class="menu-item active">
                <i class="fas fa-shopping-bag"></i>
                Mis Pedidos
            </div>
            <div class="menu-item">
                <i class="fas fa-heart"></i>
                Favoritos
            </div>
            <div class="menu-item">
                <i class="fas fa-map-marker-alt"></i>
                Direcciones
            </div>
            <div class="menu-item">
                <i class="fas fa-credit-card"></i>
                Métodos de Pago
            </div>
            <div class="menu-item">
                <i class="fas fa-cog"></i>
                Configuración
            </div>
            <div class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </div>
        </div>

        <div class="main-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($pedidos); ?></div>
                    <div class="stat-label">Pedidos Totales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">$4,200</div>
                    <div class="stat-label">Total Gastado</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Favoritos</div>
                </div>
            </div>

            <h2 class="section-title">Historial de Pedidos</h2>

            <?php foreach($pedidos as $pedido): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">Pedido #<?php echo $pedido['id']; ?></span>
                        </div>
                        <span class="order-status status-<?php echo strtolower(str_replace(' ', '', $pedido['estado'])); ?>">
                            <?php echo $pedido['estado']; ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <div>
                            <i class="fas fa-calendar"></i> 
                            <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?>
                        </div>
                        <div class="order-total">
                            $<?php echo number_format($pedido['total'], 2); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="margin-top: 30px; text-align: center;">
                <button class="btn-primary">Ver Todos los Pedidos</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>