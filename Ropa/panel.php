<?php
session_start();
if(!isset($_SESSION["admin_id"])) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Admin - SHOP HOLY</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --gold-primary: #d4af37;
    --gold-light: #ffd700;
    --gold-lighter: #f4e5c3;
    --dark: #0a0a0a;
    --dark-light: #1a1a1a;
    --dark-lighter: #2d2d2d;
    --success: #10b981;
    --danger: #e74c3c;
}

body {
    font-family: 'Montserrat', sans-serif;
    background: var(--dark);
    min-height: 100vh;
    color: #fff;
    position: relative;
    overflow-x: hidden;
}

/* Fondo animado */
.animated-bg {
    position: fixed;
    inset: 0;
    background: 
        radial-gradient(circle at 20% 30%, rgba(212,175,55,0.1), transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(255,215,0,0.08), transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(212,175,55,0.05), transparent 60%);
    animation: bgPulse 15s ease-in-out infinite alternate;
    z-index: 0;
}

@keyframes bgPulse {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
}

.grid-overlay {
    position: fixed;
    inset: 0;
    background-image: 
        linear-gradient(rgba(212,175,55,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(212,175,55,0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
    z-index: 1;
}

@keyframes gridMove {
    0% { transform: translate(0, 0); }
    100% { transform: translate(50px, 50px); }
}

/* Contenedor principal */
.container {
    position: relative;
    z-index: 10;
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 20px 80px;
}

/* Header */
.header {
    background: linear-gradient(135deg, var(--dark-light) 0%, #0d0d0d 100%);
    border: 2px solid rgba(212,175,55,0.3);
    border-radius: 25px;
    padding: 30px 40px;
    margin-bottom: 50px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5),
                0 0 100px rgba(212,175,55,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    animation: slideDown 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.logo-box {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #000;
    box-shadow: 0 5px 20px rgba(212,175,55,0.4);
    animation: logoFloat 3s ease-in-out infinite;
}

@keyframes logoFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-5px) rotate(5deg); }
}

.header-info h1 {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 900;
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-light), var(--gold-lighter));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 5px;
}

.header-info p {
    color: #999;
    font-size: 14px;
    font-weight: 600;
}

.user-welcome {
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
    color: #000;
    padding: 12px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(212,175,55,0.3);
}

.logout-btn {
    padding: 12px 25px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: #fff;
    border: none;
    border-radius: 50px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(231,76,60,0.3);
    font-size: 14px;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(231,76,60,0.5);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
    animation: fadeInUp 0.8s ease-out 0.2s backwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    background: linear-gradient(135deg, var(--dark-light) 0%, #0f0f0f 100%);
    border: 1px solid rgba(212,175,55,0.2);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    transition: 0.3s;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--gold-primary), var(--gold-light));
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(212,175,55,0.3);
    border-color: rgba(212,175,55,0.5);
}

.stat-value {
    font-size: 42px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px;
}

.stat-label {
    color: #999;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Módulos Grid */
.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    animation: fadeInUp 0.8s ease-out 0.4s backwards;
}

.module-card {
    background: linear-gradient(135deg, var(--dark-light) 0%, #0f0f0f 100%);
    border: 1px solid rgba(212,175,55,0.2);
    border-radius: 25px;
    padding: 35px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    transition: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.module-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, transparent, rgba(212,175,55,0.05));
    opacity: 0;
    transition: 0.3s;
}

.module-card:hover::before {
    opacity: 1;
}

.module-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 60px rgba(212,175,55,0.3);
    border-color: rgba(212,175,55,0.5);
}

.module-icon {
    width: 70px;
    height: 70px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.5);
    transition: 0.3s;
}

.module-card:hover .module-icon {
    transform: rotateY(180deg) scale(1.1);
}

.module-ropa .module-icon {
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
    color: #000;
}

.module-pedidos .module-icon {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: #fff;
}

.module-content h2 {
    color: var(--gold-light);
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 12px;
    transition: 0.3s;
}

.module-card:hover .module-content h2 {
    color: var(--gold-lighter);
}

.module-content p {
    color: #999;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 25px;
}

.module-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-light));
    color: #000;
    border-radius: 50px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    transition: 0.3s;
    box-shadow: 0 4px 15px rgba(212,175,55,0.3);
}

.module-link:hover {
    transform: translateX(5px);
    box-shadow: 0 6px 20px rgba(212,175,55,0.5);
}

.status-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 8px 16px;
    background: rgba(16,185,129,0.1);
    color: var(--success);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    border: 2px solid var(--success);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 968px) {
    .header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .header-left {
        flex-direction: column;
    }
    
    .modules-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 580px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<div class="animated-bg"></div>
<div class="grid-overlay"></div>

<div class="container">
    <header class="header">
        <div class="header-left">
            <div class="logo-box">
                <i class="fas fa-store"></i>
            </div>
            <div class="header-info">
                <h1>Panel de Administración</h1>
                <p>SHOP HOLY - Premium Fashion</p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div class="user-welcome">
                <i class="fas fa-user-shield"></i>
                Bienvenida, Itzel
            </div>
            <a href="admin.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </a>
        </div>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">2</div>
            <div class="stat-label">Módulos Activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">24/7</div>
            <div class="stat-label">Sistema Online</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">100%</div>
            <div class="stat-label">Operacional</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">✨</div>
            <div class="stat-label">Todo en Orden</div>
        </div>
    </div>

    <div class="modules-grid">
        <!-- Módulo Gestión de Ropa -->
        <div class="module-card module-ropa" onclick="window.location.href='admin_ropa.php'">
            <span class="status-badge">Activo</span>
            <div class="module-icon">
                <i class="fas fa-tshirt"></i>
            </div>
            <div class="module-content">
                <h2>Gestión de Productos</h2>
                <p>Administra el catálogo completo de ropa: agregar, editar, eliminar productos, gestionar tallas, precios y disponibilidad.</p>
                <a href="admin_ropa.php" class="module-link" onclick="event.stopPropagation()">
                    <span>Gestionar Productos</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Módulo Pedidos -->
        <div class="module-card module-pedidos" onclick="window.location.href='pedidos.php'">
            <span class="status-badge">Activo</span>
            <div class="module-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="module-content">
                <h2>Pedidos</h2>
                <p>Controla y gestiona todos los pedidos realizados: estados, seguimiento, confirmaciones y historial completo de órdenes.</p>
                <a href="pedidos.php" class="module-link" onclick="event.stopPropagation()">
                    <span>Ver Pedidos</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Efecto parallax suave en el fondo
document.addEventListener('mousemove', (e) => {
    const bg = document.querySelector('.animated-bg');
    const x = (e.clientX / window.innerWidth - 0.5) * 20;
    const y = (e.clientY / window.innerHeight - 0.5) * 20;
    bg.style.transform = `translate(${x}px, ${y}px)`;
});

// Animación de ripple al hacer clic en las tarjetas
document.querySelectorAll('.module-card').forEach(card => {
    card.addEventListener('click', function(e) {
        const ripple = document.createElement('div');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            border-radius: 50%;
            background: rgba(212,175,55,0.3);
            top: ${y}px;
            left: ${x}px;
            pointer-events: none;
            transform: scale(0);
            animation: rippleEffect 0.6s ease-out;
        `;
        
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// Agregar animación de ripple
const style = document.createElement('style');
style.textContent = `
    @keyframes rippleEffect {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>