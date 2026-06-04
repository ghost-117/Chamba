<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "ropa");
if($conexion->connect_error) die("Error: " . $conexion->connect_error);

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

if (isset($_GET['eliminar'])) {
    unset($_SESSION['carrito'][intval($_GET['eliminar'])]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}

if (isset($_POST['actualizar'])) {
    $key = intval($_POST['key']);
    $cantidad = max(1, min(99, intval($_POST['cantidad'])));
    if (isset($_SESSION['carrito'][$key])) {
        $_SESSION['carrito'][$key]['cantidad'] = $cantidad;
    }
    header("Location: carrito.php");
    exit;
}

if (isset($_POST['procesar_pago'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $tarjeta = trim($_POST['tarjeta'] ?? '');
    $expiracion = trim($_POST['expiracion'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');
    
    if ($nombre && $email && $telefono && $direccion && $tarjeta && $expiracion && $cvv) {
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $stmt = $conexion->prepare("SELECT precio FROM productos WHERE id = ?");
            $stmt->bind_param("i", $item['producto_id']);
            $stmt->execute();
            $prod = $stmt->get_result()->fetch_assoc();
            if ($prod) $total += $prod['precio'] * $item['cantidad'];
        }
        
        $envio = ($total >= 500) ? 0 : 99;
        $total_final = $total + $envio;
        $numero_orden = 'SH' . date('Ymd') . rand(1000, 9999);
        
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usuario_id = $result->fetch_assoc()['id'];
            $stmt = $conexion->prepare("UPDATE usuarios SET nombre=?, telefono=?, direccion=? WHERE id=?");
            $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $usuario_id);
            $stmt->execute();
        } else {
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email, telefono, direccion) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $nombre, $email, $telefono, $direccion);
            $stmt->execute();
            $usuario_id = $stmt->insert_id;
        }
        
        $stmt = $conexion->prepare("INSERT INTO pedidos (numero_orden, usuario_id, estado, created_at) VALUES (?, ?, 'Pendiente', NOW())");
        $stmt->bind_param("si", $numero_orden, $usuario_id);
        $stmt->execute();
        $pedido_id = $stmt->insert_id;
        
        foreach ($_SESSION['carrito'] as $item) {
            $stmt = $conexion->prepare("SELECT precio FROM productos WHERE id = ?");
            $stmt->bind_param("i", $item['producto_id']);
            $stmt->execute();
            $prod = $stmt->get_result()->fetch_assoc();
            if ($prod) {
                $stmt2 = $conexion->prepare("INSERT INTO pedido_items (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?,?,?,?)");
                $stmt2->bind_param("iiid", $pedido_id, $item['producto_id'], $item['cantidad'], $prod['precio']);
                $stmt2->execute();
            }
        }
        
        $_SESSION['orden_completada'] = [
            'numero' => $numero_orden,
            'fecha' => date('d/m/Y H:i:s'),
            'total' => $total_final,
            'envio' => $envio,
            'subtotal' => $total,
            'productos' => $_SESSION['carrito'],
            'cliente' => $nombre
        ];
        
        $_SESSION['carrito'] = [];
        header("Location: carrito.php?ticket=1");
        exit;
    }
}

$subtotal = 0;
$productos_carrito = [];
foreach ($_SESSION['carrito'] as $key => $item) {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $item['producto_id']);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    if ($producto) {
        $producto['talla'] = $item['talla'];
        $producto['cantidad'] = $item['cantidad'];
        $producto['key'] = $key;
        $producto['subtotal'] = $producto['precio'] * $item['cantidad'];
        $subtotal += $producto['subtotal'];
        $productos_carrito[] = $producto;
    }
}
$envio = ($subtotal >= 500) ? 0 : 99;
$total = $subtotal + $envio;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carrito - Shop Holy</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    <style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Montserrat',sans-serif;
  background:#0a0a0a;
  min-height:100vh;
  color:#fff;
  padding-bottom:50px;
  position:relative;
}
body::before{
  content:'';
  position:fixed;
  inset:0;
  background:radial-gradient(circle at 20% 50%, rgba(212,175,55,0.08) 0%, transparent 50%),
             radial-gradient(circle at 80% 80%, rgba(255,215,0,0.06) 0%, transparent 50%);
  pointer-events:none;
  z-index:0;
}
.navbar{
  background:linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
  padding:20px 50px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 4px 30px rgba(212,175,55,0.3);
  margin-bottom:40px;
  border-bottom:2px solid rgba(212,175,55,0.3);
  position:relative;
  z-index:10;
}
.logo{
  font-family:'Playfair Display',serif;
  font-size:32px;
  font-weight:900;
  background:linear-gradient(135deg,#d4af37,#ffd700,#f4e5c3);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  letter-spacing:3px;
  text-shadow:0 0 20px rgba(212,175,55,0.5);
}
.back-btn{
  color:#d4af37;
  text-decoration:none;
  font-weight:600;
  display:flex;
  align-items:center;
  gap:10px;
  transition:0.3s;
  padding:10px 20px;
  border:2px solid rgba(212,175,55,0.3);
  border-radius:8px;
}
.back-btn:hover{
  background:rgba(212,175,55,0.1);
  border-color:#d4af37;
  transform:translateX(-5px);
}
.container{
  max-width:1200px;
  margin:0 auto;
  padding:0 20px;
  position:relative;
  z-index:1;
}
.page-title{
  font-family:'Playfair Display',serif;
  font-size:48px;
  text-align:center;
  margin-bottom:50px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  text-shadow:0 0 30px rgba(212,175,55,0.3);
}
.cart-layout{
  display:grid;
  grid-template-columns:1fr 400px;
  gap:30px;
}
.cart-items{
  display:flex;
  flex-direction:column;
  gap:20px;
}
.cart-item{
  background:linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
  border:1px solid rgba(212,175,55,0.2);
  border-radius:15px;
  padding:25px;
  display:grid;
  grid-template-columns:120px 1fr auto;
  gap:25px;
  align-items:center;
  box-shadow:0 10px 40px rgba(0,0,0,0.5);
  transition:0.3s;
}
.cart-item:hover{
  border-color:rgba(212,175,55,0.5);
  box-shadow:0 15px 50px rgba(212,175,55,0.2);
}
.item-image{
  width:120px;
  height:120px;
  border-radius:12px;
  overflow:hidden;
  border:2px solid rgba(212,175,55,0.3);
}
.item-image img{
  width:100%;
  height:100%;
  object-fit:cover;
}
.item-details h3{
  font-size:20px;
  margin-bottom:8px;
  color:#ffd700;
}
.item-meta{
  color:#999;
  font-size:14px;
  margin-bottom:15px;
}
.item-meta span{
  display:inline-block;
  margin-right:15px;
}
.quantity-controls{
  display:flex;
  align-items:center;
  gap:10px;
}
.qty-btn{
  background:rgba(212,175,55,0.1);
  border:1px solid rgba(212,175,55,0.3);
  width:35px;
  height:35px;
  border-radius:8px;
  cursor:pointer;
  font-size:18px;
  font-weight:700;
  color:#d4af37;
  transition:0.3s;
}
.qty-btn:hover{
  background:#d4af37;
  color:#000;
}
.qty-display{
  width:50px;
  text-align:center;
  font-weight:700;
  font-size:16px;
  border:none;
  background:transparent;
  color:#fff;
}
.item-actions{
  display:flex;
  flex-direction:column;
  align-items:flex-end;
  gap:15px;
}
.item-price{
  font-size:24px;
  font-weight:800;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.remove-btn{
  background:none;
  border:none;
  color:#c0392b;
  cursor:pointer;
  font-size:20px;
  transition:0.3s;
  text-decoration:none;
}
.remove-btn:hover{
  color:#e74c3c;
  transform:scale(1.2);
}
.cart-summary{
  background:linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
  border:1px solid rgba(212,175,55,0.3);
  border-radius:15px;
  padding:30px;
  height:fit-content;
  position:sticky;
  top:20px;
  box-shadow:0 10px 40px rgba(0,0,0,0.5);
}
.cart-summary h2{
  font-family:'Playfair Display',serif;
  font-size:28px;
  margin-bottom:25px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.summary-row{
  display:flex;
  justify-content:space-between;
  margin-bottom:15px;
  font-size:16px;
  color:#ccc;
}
.summary-row.total{
  border-top:2px solid rgba(212,175,55,0.3);
  padding-top:15px;
  margin-top:15px;
  font-size:20px;
  font-weight:800;
  color:#ffd700;
}
.free-shipping{
  background:linear-gradient(135deg,#d4af37,#f4e5c3);
  color:#000;
  padding:12px;
  border-radius:10px;
  text-align:center;
  margin:20px 0;
  font-weight:600;
  font-size:14px;
}
.shipping-info{
  background:rgba(212,175,55,0.1);
  border:1px solid rgba(212,175,55,0.3);
  padding:12px;
  border-radius:10px;
  text-align:center;
  margin:20px 0;
  font-size:14px;
  color:#d4af37;
}
.checkout-btn{
  width:100%;
  padding:18px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  color:#000;
  border:none;
  border-radius:12px;
  font-size:16px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:1px;
  cursor:pointer;
  transition:0.3s;
  box-shadow:0 4px 20px rgba(212,175,55,0.4);
}
.checkout-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 8px 30px rgba(212,175,55,0.6);
}
.empty-cart{
  grid-column:1/-1;
  text-align:center;
  padding:100px 20px;
}
.empty-cart i{
  font-size:100px;
  opacity:0.3;
  margin-bottom:30px;
  color:#d4af37;
}
.empty-cart h2{
  font-size:36px;
  margin-bottom:15px;
  color:#ffd700;
}
.empty-cart p{
  font-size:18px;
  margin-bottom:30px;
  color:#999;
}
.continue-shopping{
  display:inline-block;
  padding:16px 40px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  color:#000;
  text-decoration:none;
  border-radius:50px;
  font-weight:700;
  transition:0.3s;
}
.continue-shopping:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 30px rgba(212,175,55,0.5);
}
.modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.95);
  backdrop-filter:blur(15px);
  z-index:1000;
  align-items:center;
  justify-content:center;
  padding:20px;
  overflow-y:auto;
}
.modal.show{
  display:flex;
}
.modal-content{
  background:linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
  border:2px solid rgba(212,175,55,0.3);
  border-radius:20px;
  padding:40px;
  max-width:500px;
  width:100%;
  box-shadow:0 20px 60px rgba(0,0,0,0.8);
  position:relative;
  max-height:90vh;
  overflow-y:auto;
}
.close-modal{
  position:absolute;
  top:20px;
  right:20px;
  background:rgba(212,175,55,0.2);
  border:1px solid rgba(212,175,55,0.3);
  color:#d4af37;
  width:40px;
  height:40px;
  border-radius:50%;
  cursor:pointer;
  font-size:20px;
  transition:0.3s;
}
.close-modal:hover{
  background:rgba(212,175,55,0.3);
  transform:rotate(90deg);
}
.modal-title{
  font-family:'Playfair Display',serif;
  font-size:32px;
  text-align:center;
  margin-bottom:30px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.form-section-title{
  font-size:18px;
  font-weight:700;
  margin:30px 0 20px 0;
  padding-bottom:10px;
  border-bottom:2px solid rgba(212,175,55,0.3);
  color:#ffd700;
}
.credit-card{
  background:linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
  border-radius:18px;
  padding:35px 30px;
  margin-bottom:30px;
  box-shadow:0 15px 50px rgba(0,0,0,0.7),
             inset 0 1px 0 rgba(255,255,255,0.1),
             inset 0 -1px 0 rgba(0,0,0,0.5);
  position:relative;
  overflow:hidden;
  border:1px solid rgba(212,175,55,0.2);
  transform-style:preserve-3d;
  transition:transform 0.3s;
}
.credit-card:hover{
  transform:rotateY(5deg) rotateX(2deg);
}
.credit-card::before{
  content:'';
  position:absolute;
  top:0;
  left:-100%;
  width:50%;
  height:100%;
  background:linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
  animation:shine 3s infinite;
}
@keyframes shine{
  0%{left:-100%}
  100%{left:200%}
}
.credit-card::after{
  content:'';
  position:absolute;
  top:-50%;
  right:-50%;
  width:200%;
  height:200%;
  background:radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 60%);
}
.card-chip{
  width:55px;
  height:45px;
  background:linear-gradient(135deg, #d4af37 0%, #ffd700 50%, #f4e5c3 100%);
  border-radius:8px;
  margin-bottom:25px;
  position:relative;
  z-index:1;
  box-shadow:0 4px 15px rgba(212,175,55,0.4),
             inset 0 1px 0 rgba(255,255,255,0.3);
}
.card-chip::after{
  content:'';
  position:absolute;
  inset:6px;
  background:repeating-linear-gradient(
    45deg,
    rgba(0,0,0,0.1) 0px,
    rgba(0,0,0,0.1) 2px,
    transparent 2px,
    transparent 4px
  );
  border-radius:5px;
  box-shadow:inset 0 2px 5px rgba(0,0,0,0.3);
}
.card-number{
  font-size:24px;
  letter-spacing:4px;
  margin-bottom:25px;
  font-weight:600;
  position:relative;
  z-index:1;
  color:#fff;
  text-shadow:0 2px 10px rgba(0,0,0,0.5);
  font-family:'Courier New', monospace;
}
.card-details{
  display:flex;
  justify-content:space-between;
  position:relative;
  z-index:1;
}
.card-holder,.card-expiry{
  font-size:10px;
  opacity:0.7;
  margin-bottom:5px;
  text-transform:uppercase;
  letter-spacing:1px;
  color:#d4af37;
}
.card-holder-name,.card-expiry-date{
  font-size:16px;
  font-weight:600;
  color:#fff;
  text-shadow:0 2px 8px rgba(0,0,0,0.5);
}
.form-group{
  margin-bottom:20px;
}
.form-group label{
  display:block;
  margin-bottom:8px;
  font-size:14px;
  font-weight:600;
  color:#d4af37;
  text-transform:uppercase;
  letter-spacing:1px;
}
.form-group input,.form-group textarea{
  width:100%;
  padding:15px;
  border:1px solid rgba(212,175,55,0.3);
  border-radius:10px;
  background:rgba(212,175,55,0.05);
  color:#fff;
  font-size:16px;
  transition:0.3s;
  font-family:'Montserrat',sans-serif;
}
.form-group textarea{
  resize:vertical;
  min-height:80px;
}
.form-group input:focus,.form-group textarea:focus{
  outline:none;
  border-color:#d4af37;
  background:rgba(212,175,55,0.1);
  box-shadow:0 0 20px rgba(212,175,55,0.2);
}
.form-group input::placeholder,.form-group textarea::placeholder{
  color:rgba(255,255,255,0.3);
}
.form-row{
  display:grid;
  grid-template-columns:2fr 1fr;
  gap:15px;
}
.pay-btn{
  width:100%;
  padding:18px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  color:#000;
  border:none;
  border-radius:12px;
  font-size:16px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:1px;
  cursor:pointer;
  transition:0.3s;
  box-shadow:0 4px 20px rgba(212,175,55,0.4);
  margin-top:10px;
}
.pay-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 8px 30px rgba(212,175,55,0.6);
}
.ticket-modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.95);
  backdrop-filter:blur(15px);
  z-index:2000;
  align-items:center;
  justify-content:center;
  padding:20px;
}
.ticket-modal.show{
  display:flex;
}
.ticket{
  background:#fff;
  max-width:400px;
  width:100%;
  padding:40px 30px;
  box-shadow:0 20px 80px rgba(0,0,0,0.8);
  color:#000;
  font-family:'Courier New',monospace;
}
.ticket-header{
  text-align:center;
  border-bottom:2px dashed #000;
  padding-bottom:20px;
  margin-bottom:20px;
}
.ticket-logo{
  font-family:'Playfair Display',serif;
  font-size:36px;
  font-weight:900;
  background:linear-gradient(135deg,#d4af37,#000);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  margin-bottom:10px;
}
.ticket-info{
  font-size:12px;
  margin-bottom:5px;
}
.ticket-body{
  margin-bottom:20px;
}
.ticket-item{
  display:flex;
  justify-content:space-between;
  margin-bottom:10px;
  font-size:13px;
}
.ticket-item-name{
  flex:1;
}
.ticket-item-qty{
  width:30px;
  text-align:center;
}
.ticket-item-price{
  width:80px;
  text-align:right;
}
.ticket-divider{
  border-top:1px dashed #000;
  margin:15px 0;
}
.ticket-total{
  display:flex;
  justify-content:space-between;
  font-size:16px;
  font-weight:700;
  margin-top:15px;
}
.ticket-footer{
  text-align:center;
  border-top:2px dashed #000;
  padding-top:20px;
  margin-top:20px;
  font-size:12px;
}
.ticket-close-btn{
  width:100%;
  padding:15px;
  background:linear-gradient(135deg,#d4af37,#ffd700);
  color:#000;
  border:none;
  border-radius:10px;
  font-size:14px;
  font-weight:700;
  text-transform:uppercase;
  cursor:pointer;
  margin-top:20px;
  font-family:'Montserrat',sans-serif;
}
@media(max-width:768px){
  .cart-layout{grid-template-columns:1fr}
  .cart-item{grid-template-columns:80px 1fr;gap:15px}
  .item-actions{grid-column:1/-1;flex-direction:row;justify-content:space-between;align-items:center}
  .cart-summary{position:static}
  .form-row{grid-template-columns:1fr}
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo">SHOP HOLY</div>
    <a href="ropa.php" class="back-btn"><i class="fas fa-arrow-left"></i> Seguir Comprando</a>
</nav>

<div class="container">
    <h1 class="page-title">Mi Carrito</h1>
    
    <div class="cart-layout">
        <?php if (count($productos_carrito) > 0): ?>
            <div class="cart-items">
                <?php foreach ($productos_carrito as $producto): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        </div>
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <div class="item-meta">
                                <span><strong>Talla:</strong> <?php echo htmlspecialchars($producto['talla']); ?></span>
                                <span><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 2); ?></span>
                            </div>
                            <form method="POST" class="qty-form">
                                <input type="hidden" name="key" value="<?php echo $producto['key']; ?>">
                                <div class="quantity-controls">
                                    <button type="button" class="qty-btn" onclick="changeQty(this,-1)">-</button>
                                    <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" class="qty-display" readonly>
                                    <button type="button" class="qty-btn" onclick="changeQty(this,1)">+</button>
                                    <button type="submit" name="actualizar" style="display:none" class="update-btn"></button>
                                </div>
                            </form>
                        </div>
                        <div class="item-actions">
                            <div class="item-price">$<?php echo number_format($producto['subtotal'], 2); ?></div>
                            <a href="?eliminar=<?php echo $producto['key']; ?>" class="remove-btn" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <h2>Resumen</h2>
                <div class="summary-row"><span>Subtotal:</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
                <div class="summary-row"><span>Envío:</span><span><?php echo ($envio==0)?'GRATIS':'$'.number_format($envio,2); ?></span></div>
                <?php if($envio==0): ?>
                    <div class="free-shipping"><i class="fas fa-truck"></i> ¡Envío gratis!</div>
                <?php else: ?>
                    <div class="shipping-info">Agrega $<?php echo number_format(500-$subtotal,2); ?> más para envío gratis</div>
                <?php endif; ?>
                <div class="summary-row total"><span>Total:</span><span>$<?php echo number_format($total, 2); ?></span></div>
                <button class="checkout-btn" onclick="openModal()"><i class="fas fa-lock"></i> Procesar Pago</button>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-bag"></i>
                <h2>Tu carrito está vacío</h2>
                <p>¡Agrega productos increíbles!</p>
                <a href="ropa.php" class="continue-shopping"><i class="fas fa-arrow-left"></i> Ir a la Tienda</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="paymentModal">
    <div class="modal-content">
        <button class="close-modal" onclick="closeModal()"><i class="fas fa-times"></i></button>
        <h2 class="modal-title">Finalizar Compra</h2>
        
        <form method="POST" id="paymentForm">
            <div class="form-section-title"><i class="fas fa-user"></i> Información de Contacto</div>
            <div class="form-group">
                <label>Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" placeholder="Juan Pérez García" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico *</label>
                <input type="email" id="email" name="email" placeholder="juan@ejemplo.com" required>
            </div>
            <div class="form-group">
                <label>Teléfono *</label>
                <input type="tel" id="telefono" name="telefono" placeholder="5512345678" minlength="10" maxlength="10" required>
            </div>
            <div class="form-group">
                <label>Dirección Completa *</label>
                <textarea id="direccion" name="direccion" placeholder="Calle, Número, Colonia, CP, Ciudad" required></textarea>
            </div>
            
            <div class="form-section-title"><i class="fas fa-credit-card"></i> Método de Pago</div>
            <div class="credit-card">
                <div class="card-chip"></div>
                <div class="card-number" id="cardNumber">•••• •••• •••• ••••</div>
                <div class="card-details">
                    <div>
                        <div class="card-holder">Titular</div>
                        <div class="card-holder-name" id="cardHolder">NOMBRE APELLIDO</div>
                    </div>
                    <div>
                        <div class="card-expiry">Válido hasta</div>
                        <div class="card-expiry-date" id="cardExpiry">MM/AA</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Número de Tarjeta *</label>
                <input type="text" id="tarjeta" name="tarjeta" placeholder="1234 5678 9012 3456" maxlength="19" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Expiración *</label>
                    <input type="text" id="expiracion" name="expiracion" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div class="form-group">
                    <label>CVV *</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" minlength="3" maxlength="3" required>
                </div>
            </div>
            
            <button type="submit" name="procesar_pago" class="pay-btn">
                <i class="fas fa-check-circle"></i> Confirmar - $<?php echo number_format($total, 2); ?>
            </button>
        </form>
    </div>
</div>

<?php if(isset($_SESSION['orden_completada']) && isset($_GET['ticket'])): $orden=$_SESSION['orden_completada']; ?>
<div class="ticket-modal show">
    <div class="ticket">
        <div class="ticket-header">
            <div class="ticket-logo">SHOP HOLY</div>
            <div class="ticket-info">Premium Fashion Store</div>
            <div class="ticket-info">Tel: (55) 1234-5678</div>
        </div>
        <div class="ticket-body">
            <div class="ticket-info"><strong>Cliente:</strong> <?php echo htmlspecialchars($orden['cliente']); ?></div>
            <div class="ticket-info"><strong>Orden:</strong> <?php echo $orden['numero']; ?></div>
            <div class="ticket-info"><strong>Fecha:</strong> <?php echo $orden['fecha']; ?></div>
            <div class="ticket-divider"></div>
            <?php foreach($orden['productos'] as $item):
                $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->bind_param("i", $item['producto_id']);
                $stmt->execute();
                $prod = $stmt->get_result()->fetch_assoc();
                if($prod): ?>
                <div class="ticket-item">
                    <span class="ticket-item-name"><?php echo htmlspecialchars($prod['nombre']); ?> (<?php echo htmlspecialchars($item['talla']); ?>)</span>
                    <span class="ticket-item-qty"><?php echo $item['cantidad']; ?>x</span>
                    <span class="ticket-item-price">$<?php echo number_format($prod['precio']*$item['cantidad'],2); ?></span>
                </div>
            <?php endif; endforeach; ?>
            <div class="ticket-divider"></div>
            <div class="ticket-item">
                <span class="ticket-item-name">Subtotal</span>
                <span class="ticket-item-qty"></span>
                <span class="ticket-item-price">$<?php echo number_format($orden['subtotal'],2); ?></span>
            </div>
            <div class="ticket-item">
                <span class="ticket-item-name">Envío</span>
                <span class="ticket-item-qty"></span>
                <span class="ticket-item-price"><?php echo ($orden['envio']==0)?'GRATIS':'$'.number_format($orden['envio'],2); ?></span>
            </div>
            <div class="ticket-total">
                <span>TOTAL</span>
                <span>$<?php echo number_format($orden['total'],2); ?></span>
            </div>
        </div>
        <div class="ticket-footer">
            <div><strong>¡GRACIAS POR TU COMPRA!</strong></div>
            <div style="margin:10px 0">Conserva tu ticket</div>
        </div>
        <button class="ticket-close-btn" onclick="window.location.href='ropa.php'">
            <i class="fas fa-home"></i> Volver a la Tienda
        </button>
    </div>
</div>
<?php unset($_SESSION['orden_completada']); endif; ?>

<script>
function changeQty(btn,delta){
    const form=btn.closest('.qty-form');
    const input=form.querySelector('.qty-display');
    let val=parseInt(input.value)+delta;
    if(val<1)val=1;
    if(val>99)val=99;
    input.value=val;
    form.querySelector('.update-btn').click();
}

function openModal(){
    document.getElementById('paymentModal').classList.add('show');
    document.body.style.overflow='hidden';
}

function closeModal(){
    document.getElementById('paymentModal').classList.remove('show');
    document.body.style.overflow='auto';
}

document.addEventListener('DOMContentLoaded',function(){
    const form=document.getElementById('paymentForm');
    if(form){
        form.addEventListener('submit',function(e){
            const tel=document.getElementById('telefono').value;
            const tarj=document.getElementById('tarjeta').value.replace(/\s/g,'');
            const cvv=document.getElementById('cvv').value;
            const exp=document.getElementById('expiracion').value;
            
            if(tel.length!==10){alert('Teléfono: 10 dígitos');e.preventDefault();return false;}
            if(tarj.length<15||tarj.length>16){alert('Tarjeta: 15-16 dígitos');e.preventDefault();return false;}
            if(cvv.length!==3){alert('CVV: 3 dígitos');e.preventDefault();return false;}
            if(!/^\d{2}\/\d{2}$/.test(exp)){alert('Expiración: MM/AA');e.preventDefault();return false;}
            return true;
        });
    }
    
    const tarjeta=document.getElementById('tarjeta');
    if(tarjeta){
        tarjeta.addEventListener('input',function(){
            let val=this.value.replace(/\s/g,'').replace(/\D/g,'');
            let fmt=val.match(/.{1,4}/g);
            this.value=fmt?fmt.join(' '):val;
            document.getElementById('cardNumber').textContent=this.value||'•••• •••• •••• ••••';
        });
    }
    
    const expiracion=document.getElementById('expiracion');
    if(expiracion){
        expiracion.addEventListener('input',function(){
            let val=this.value.replace(/\D/g,'');
            if(val.length>=2)val=val.slice(0,2)+'/'+val.slice(2,4);
            this.value=val;
            document.getElementById('cardExpiry').textContent=val||'MM/AA';
        });
    }
    
    const nombre=document.getElementById('nombre');
    if(nombre){
        nombre.addEventListener('input',function(){
            document.getElementById('cardHolder').textContent=this.value.toUpperCase()||'NOMBRE APELLIDO';
        });
    }
    
    const telefono=document.getElementById('telefono');
    if(telefono){
        telefono.addEventListener('input',function(){
            this.value=this.value.replace(/\D/g,'');
        });
    }
    
    const cvv=document.getElementById('cvv');
    if(cvv){
        cvv.addEventListener('input',function(){
            this.value=this.value.replace(/\D/g,'');
        });
    }
});
</script>

</body>
</html>