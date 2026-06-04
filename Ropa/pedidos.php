<?php
session_start();

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "root", "", "ropa");
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// ----------------------------
// MARCAR PEDIDO COMO ENTREGADO
// ----------------------------
if(isset($_GET['entregado'])){
    $id = intval($_GET['entregado']);
    $mysqli->query("UPDATE pedidos SET estado = 'Entregado' WHERE id = $id");
    header("Location: pedidos.php");
    exit();
}

// ----------------------------
// MARCAR PEDIDO COMO EN PROCESO
// ----------------------------
if(isset($_GET['en_proceso'])){
    $id = intval($_GET['en_proceso']);
    $mysqli->query("UPDATE pedidos SET estado = 'En Proceso' WHERE id = $id");
    header("Location: pedidos.php");
    exit();
}

// ----------------------------
// CONSULTA PEDIDOS
// ----------------------------
$result_pedidos = $mysqli->query("
    SELECT 
        p.id,
        p.numero_orden,
        u.nombre AS cliente,
        u.email,
        u.telefono,
        u.direccion,
        p.estado,
        p.created_at AS fecha,
        (
            SELECT SUM(pi.cantidad * pi.precio_unitario)
            FROM pedido_items pi
            WHERE pi.pedido_id = p.id
        ) AS total
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.created_at DESC
");

if (!$result_pedidos) {
    die("Error en la consulta: " . $mysqli->error);
}

// ----------------------------
// ESTADÍSTICAS
// ----------------------------
$stats_pendientes = $mysqli->query("SELECT COUNT(*) as total FROM pedidos WHERE estado='Pendiente'")->fetch_assoc()['total'];
$stats_proceso = $mysqli->query("SELECT COUNT(*) as total FROM pedidos WHERE estado='En Proceso'")->fetch_assoc()['total'];
$stats_entregados = $mysqli->query("SELECT COUNT(*) as total FROM pedidos WHERE estado='Entregado'")->fetch_assoc()['total'];
$stats_total_ventas = $mysqli->query("SELECT SUM(pi.cantidad * pi.precio_unitario) as total FROM pedido_items pi JOIN pedidos p ON pi.pedido_id = p.id WHERE p.estado='Entregado'")->fetch_assoc()['total'] ?? 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pedidos - SHOP HOLY</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body { 
    font-family: 'Roboto', sans-serif; 
    background: linear-gradient(135deg, #000000, #1a1a1a); 
    color: #fff; 
    padding: 20px;
    min-height: 100vh;
}

h1 { 
    text-align: center; 
    margin-bottom: 30px; 
    font-family: 'Bebas Neue', cursive;
    font-size: 48px;
    background: linear-gradient(135deg, #FFD700, #FFA500);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
    filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.3));
}

/* Estadísticas */
.stats-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.stat-card {
    background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
    border: 2px solid #FFD700;
    padding: 15px 25px;
    border-radius: 12px;
    text-align: center;
    min-width: 150px;
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
}

.stat-card .label {
    font-size: 14px;
    color: #999;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-card .value {
    font-size: 24px;
    font-weight: bold;
    background: linear-gradient(135deg, #FFD700, #FFA500);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Tabla */
.table-wrapper { 
    background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
    padding: 20px; 
    border-radius: 15px; 
    overflow-x: auto; 
    max-width: 1400px; 
    margin: 0 auto;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.8);
    border: 2px solid #FFD700;
}

table { 
    width: 100%; 
    border-collapse: collapse; 
}

th, td { 
    padding: 15px 10px; 
    text-align: center; 
    border-bottom: 1px solid rgba(255, 215, 0, 0.1); 
}

th { 
    background: linear-gradient(135deg, #FFD700, #FFA500); 
    color: #000; 
    font-weight: 700;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
}

tr:hover { 
    background: rgba(255, 215, 0, 0.05); 
    transition: 0.3s; 
}

/* Estados */
.estado { 
    padding: 6px 14px; 
    border-radius: 20px; 
    display: inline-block; 
    color: #000; 
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}

.estado.Pendiente { 
    background: linear-gradient(135deg, #FFA500, #FF8C00); 
}

.estado.EnProceso { 
    background: linear-gradient(135deg, #FFD700, #DAA520); 
}

.estado.Entregado { 
    background: linear-gradient(135deg, #00ff88, #00cc66); 
    color: #000;
}

/* Botones */
.btn-action { 
    text-decoration: none; 
    padding: 8px 15px; 
    border-radius: 8px; 
    font-size: 13px; 
    display: inline-block; 
    margin: 3px; 
    transition: all 0.3s;
    font-weight: 600;
    border: 2px solid transparent;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
}

.btn-proceso { 
    background: linear-gradient(135deg, #FFD700, #FFA500); 
    color: #000; 
    border-color: #FFD700;
}

.btn-entregado { 
    background: linear-gradient(135deg, #00ff88, #00cc66); 
    color: #000;
    border-color: #00ff88;
}

.btn-wa { 
    background: linear-gradient(135deg, #25D366, #128C7E); 
    color: #fff; 
    border-color: #25D366;
}

.btn-wa i {
    margin-right: 5px;
}

/* Mensaje sin pedidos */
.no-orders {
    text-align: center;
    padding: 60px 20px;
    color: #999;
    font-size: 18px;
}

.no-orders i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #FFD700;
    filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.5));
}

/* Responsive */
@media (max-width: 768px) {
    h1 { font-size: 32px; }
    
    .stats-container {
        flex-direction: column;
        align-items: center;
    }
    
    table { font-size: 12px; }
    
    th, td { padding: 8px 5px; }
    
    .btn-action { 
        padding: 6px 10px; 
        font-size: 11px; 
        display: block;
        margin: 3px auto;
    }
}
</style>
</head>
<body>

<h1>🛍️ SHOP HOLY - Gestión de Pedidos</h1>

<!-- Estadísticas -->
<div class="stats-container">
    <div class="stat-card">
        <div class="label">Pendientes</div>
        <div class="value"><?= $stats_pendientes ?></div>
    </div>
    <div class="stat-card">
        <div class="label">En Proceso</div>
        <div class="value"><?= $stats_proceso ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Entregados</div>
        <div class="value"><?= $stats_entregados ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Ventas</div>
        <div class="value">$<?= number_format($stats_total_ventas, 2) ?></div>
    </div>
</div>

<!-- Tabla de pedidos -->
<div class="table-wrapper">
<?php if($result_pedidos->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result_pedidos->fetch_assoc()): 
                // Preparar mensaje de WhatsApp
                $nombre = urlencode($row['cliente']);
                $telefono = preg_replace('/[^0-9]/', '', $row['telefono']);
                $mensaje = urlencode("✨ ¡Hola {$row['cliente']}! Tu pedido #{$row['id']} ha sido procesado con éxito. Pronto llegará a tus manos. Muchas gracias por comprar en SHOP HOLY. 🛍️ ");
                $link_wa = "https://wa.me/52{$telefono}?text={$mensaje}";
            ?>
            <tr>
                <td><strong>#<?= $row['id'] ?></strong></td>
                <td><?= htmlspecialchars($row['cliente']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['direccion']) ?></td>
                <td><strong>$<?= number_format($row['total'], 2) ?></strong></td>
                <td>
                    <span class="estado <?= str_replace(' ', '', $row['estado']) ?>">
                        <?= $row['estado'] ?>
                    </span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($row['fecha'])) ?></td>
                <td>
                    <?php if($row['estado'] == 'Pendiente'): ?>
                        <a href="pedidos.php?en_proceso=<?= $row['id'] ?>" class="btn-action btn-proceso">
                            <i class="fas fa-clock"></i> En Proceso
                        </a>
                    <?php endif; ?>
                    
                    <?php if($row['estado'] != 'Entregado'): ?>
                        <a href="pedidos.php?entregado=<?= $row['id'] ?>" class="btn-action btn-entregado">
                            <i class="fas fa-check"></i> Entregado
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= $link_wa ?>" target="_blank" class="btn-action btn-wa">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="no-orders">
        <i class="fas fa-inbox"></i>
        <p>No hay pedidos registrados aún</p>
    </div>
<?php endif; ?>
</div>

</body>
</html>