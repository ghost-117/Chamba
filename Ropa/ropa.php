<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "ropa");
if($conexion->connect_error) die("Error de conexión: " . $conexion->connect_error);

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

if (isset($_POST['agregar'])) {
    $producto_id = (int)$_POST['producto_id'];
    $talla = $_POST['talla'] ?? '';
    $cantidad = (int)$_POST['cantidad'];
    
    if ($producto_id > 0 && $cantidad > 0 && !empty($talla)) {
        $key = $producto_id . '_' . $talla;
        if (isset($_SESSION['carrito'][$key])) {
            $_SESSION['carrito'][$key]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$key] = ['producto_id' => $producto_id, 'talla' => $talla, 'cantidad' => $cantidad];
        }
        echo "<script>window.location.href='carrito.php';</script>";
        exit;
    }
}

$cantidad_carrito = 0;
foreach ($_SESSION['carrito'] as $item) $cantidad_carrito += $item['cantidad'];

$categoria_filtro = $_GET['categoria'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

$sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.disponible = 1";
if (!empty($categoria_filtro)) $sql .= " AND c.nombre = '" . $conexion->real_escape_string($categoria_filtro) . "'";
if (!empty($busqueda)) $sql .= " AND (p.nombre LIKE '%" . $conexion->real_escape_string($busqueda) . "%' OR p.descripcion LIKE '%" . $conexion->real_escape_string($busqueda) . "%')";
$sql .= " ORDER BY p.id DESC";
$resultado = $conexion->query($sql);

$categorias_query = $conexion->query("SELECT DISTINCT nombre FROM categorias ORDER BY nombre");
$categorias = [];
if ($categorias_query) while($cat = $categorias_query->fetch_assoc()) $categorias[] = $cat['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SHOP HOLY - Luxury Urban Fashion</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Montserrat:wght@300;400;500;600;700;800&family=Cormorant+Garamond:wght@300;400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --gold: #d4af37;
    --gold-light: #f4e4c1;
    --gold-dark: #c9a961;
    --black: #0a0a0a;
    --gray: #b8b5a8;
    --white: #f5f5f0;
}

body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    min-height: 100vh;
    color: var(--white);
}

.top-bar {
    background: rgba(212,175,55,0.1);
    padding: 12px 0;
    text-align: center;
    font-size: 13px;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(212,175,55,0.2);
    letter-spacing: 1px;
    font-weight: 300;
}

.top-bar i {
    color: var(--gold);
    margin-right: 8px;
}

.navbar {
    background: linear-gradient(135deg, rgba(15,15,15,0.98), rgba(20,20,20,0.95));
    padding: 22px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 30px rgba(0,0,0,0.5);
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(212,175,55,0.15);
}

.logo {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 900;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 4px;
    text-shadow: 0 0 30px rgba(212,175,55,0.3);
}

.nav-links {
    display: flex;
    gap: 35px;
    align-items: center;
}

.nav-links a {
    color: var(--gray);
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: 0.3s;
    position: relative;
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 1px;
    background: var(--gold);
    transition: 0.3s;
}

.nav-links a:hover {
    color: var(--gold);
}

.nav-links a:hover::after {
    width: 100%;
}

.cart-btn {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: var(--black);
    padding: 13px 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    transition: 0.3s;
    box-shadow: 0 4px 20px rgba(212,175,55,0.3);
    position: relative;
    letter-spacing: 1px;
}

.cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(212,175,55,0.5);
    background: linear-gradient(135deg, var(--gold-light), var(--gold));
}

.cart-btn::after {
    display: none;
}

.cart-count {
    background: var(--black);
    color: var(--gold);
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: bold;
}

.hero {
    text-align: center;
    padding: 100px 20px 80px;
    background: 
        radial-gradient(circle at 50% 50%, rgba(212,175,55,0.08), transparent 70%),
        linear-gradient(135deg, rgba(10,10,10,0.8), rgba(26,26,26,0.9));
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gold), transparent);
}

.hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(48px, 8vw, 72px);
    font-weight: 900;
    margin-bottom: 18px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 3px;
}

.hero p {
    font-family: 'Cormorant Garamond', serif;
    font-size: 20px;
    color: var(--gray);
    font-weight: 300;
    letter-spacing: 4px;
    text-transform: uppercase;
    font-style: italic;
}

.filters-section {
    max-width: 1200px;
    margin: -30px auto 70px;
    padding: 0 20px;
    position: relative;
    z-index: 10;
}

.filters-container {
    background: linear-gradient(135deg, rgba(20,20,25,0.95), rgba(15,15,15,0.98));
    border: 1px solid rgba(212,175,55,0.25);
    border-radius: 12px;
    padding: 35px;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    box-shadow: 0 15px 50px rgba(0,0,0,0.5);
    backdrop-filter: blur(20px);
}

.search-box {
    max-width: 600px;
    width: 100%;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 16px 55px 16px 22px;
    border: 1px solid rgba(212,175,55,0.25);
    border-radius: 8px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
    background: rgba(10,10,10,0.6);
    color: var(--white);
    font-family: 'Montserrat', sans-serif;
}

.search-box input::placeholder {
    color: rgba(184,181,168,0.5);
}

.search-box input:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
    background: rgba(15,15,15,0.8);
}

.search-box button {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    border: none;
    color: var(--black);
    width: 45px;
    height: 45px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 16px;
}

.search-box button:hover {
    transform: translateY(-50%) scale(1.05);
    box-shadow: 0 4px 15px rgba(212,175,55,0.4);
}

.category-select {
    display: none;
}

.products-section {
    max-width: 1400px;
    margin: 0 auto 100px;
    padding: 0 20px;
}

.section-header {
    text-align: center;
    margin-bottom: 60px;
}

.section-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 42px;
    font-weight: 700;
    color: var(--gold);
    margin-bottom: 15px;
    letter-spacing: 3px;
}

.section-header p {
    color: var(--gray);
    font-size: 15px;
    letter-spacing: 2px;
    font-weight: 300;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 40px;
}

.product-item {
    background: linear-gradient(135deg, rgba(20,20,25,0.6), rgba(15,15,15,0.8));
    border: 1px solid rgba(212,175,55,0.15);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    transition: 0.4s;
    position: relative;
    backdrop-filter: blur(10px);
}

.product-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 60px rgba(212,175,55,0.2);
    border-color: rgba(212,175,55,0.4);
}

.product-image {
    position: relative;
    overflow: hidden;
    height: 380px;
    background: #1a1a1a;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.6s;
    filter: brightness(0.9) contrast(1.1);
}

.product-item:hover .product-image img {
    transform: scale(1.08);
    filter: brightness(1) contrast(1.15);
}

.product-badge {
    position: absolute;
    top: 18px;
    right: 18px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--black);
    padding: 8px 18px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    box-shadow: 0 4px 15px rgba(212,175,55,0.4);
}

.favorite-btn {
    position: absolute;
    top: 18px;
    left: 18px;
    background: rgba(255,255,255,0.15);
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.3s;
    color: var(--white);
    font-size: 18px;
    backdrop-filter: blur(10px);
}

.favorite-btn:hover {
    background: rgba(212,175,55,0.2);
    color: var(--gold);
    transform: scale(1.1);
}

.favorite-btn.active {
    background: var(--gold);
    color: var(--black);
}

.product-info {
    padding: 28px;
}

.product-category {
    color: var(--gold);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 10px;
    font-weight: 600;
}

.product-name {
    font-family: 'Playfair Display', serif;
    color: var(--white);
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
}

.product-description {
    color: var(--gray);
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 18px;
    font-weight: 300;
}

.product-price {
    color: var(--gold);
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 22px;
    font-family: 'Playfair Display', serif;
}

.sizes-container {
    margin-bottom: 22px;
}

.sizes-label {
    color: var(--white);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 12px;
    display: block;
    letter-spacing: 1px;
}

.sizes-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.size-option {
    display: none;
}

.size-label {
    padding: 12px;
    border: 1px solid rgba(212,175,55,0.25);
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray);
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    background: rgba(10,10,10,0.4);
}

.size-label:hover {
    border-color: rgba(212,175,55,0.5);
    background: rgba(212,175,55,0.05);
}

.size-option:checked + .size-label {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: var(--black);
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(212,175,55,0.3);
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 22px;
}

.quantity-label {
    color: var(--white);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}

.qty-btn {
    background: rgba(212,175,55,0.1);
    border: 1px solid rgba(212,175,55,0.25);
    width: 42px;
    height: 42px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    font-weight: 700;
    color: var(--gold);
    transition: 0.3s;
}

.qty-btn:hover {
    background: var(--gold);
    color: var(--black);
    border-color: transparent;
}

.qty-input {
    width: 65px;
    text-align: center;
    border: 1px solid rgba(212,175,55,0.25);
    border-radius: 6px;
    padding: 10px;
    font-size: 16px;
    font-weight: 700;
    color: var(--white);
    height: 42px;
    background: rgba(10,10,10,0.4);
}

.add-to-cart-btn {
    width: 100%;
    padding: 17px;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: var(--black);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 4px 20px rgba(212,175,55,0.3);
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-family: 'Montserrat', sans-serif;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(212,175,55,0.5);
    background: linear-gradient(135deg, var(--gold-light), var(--gold));
}

.empty-state {
    text-align: center;
    padding: 120px 20px;
    color: var(--gray);
    grid-column: 1 / -1;
}

.empty-state i {
    font-size: 90px;
    color: rgba(212,175,55,0.3);
    margin-bottom: 25px;
}

.empty-state h2 {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: var(--white);
    margin-bottom: 12px;
}

.empty-state p {
    font-size: 16px;
    font-weight: 300;
}

.scroll-top {
    position: fixed;
    bottom: 35px;
    right: 35px;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    color: var(--black);
    font-size: 22px;
    cursor: pointer;
    box-shadow: 0 6px 25px rgba(212,175,55,0.4);
    transition: 0.3s;
    z-index: 999;
    border: 1px solid rgba(212,175,55,0.3);
}

.scroll-top:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 8px 35px rgba(212,175,55,0.6);
}

.scroll-top.show {
    display: flex;
}

.footer {
    background: linear-gradient(135deg, rgba(15,15,15,0.98), rgba(10,10,10,1));
    border-top: 1px solid rgba(212,175,55,0.2);
    padding: 50px 20px;
    text-align: center;
}

.footer-content {
    max-width: 800px;
    margin: 0 auto;
}

.footer h3 {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: var(--gold);
    margin-bottom: 15px;
    letter-spacing: 3px;
}

.footer p {
    color: var(--gray);
    font-size: 13px;
    letter-spacing: 1px;
    font-weight: 300;
}

@media (max-width: 768px) {
    .navbar {
        padding: 18px 20px;
        flex-wrap: wrap;
    }
    
    .nav-links {
        order: 3;
        width: 100%;
        margin-top: 20px;
        justify-content: center;
        gap: 20px;
    }
    
    .filters-container {
        flex-direction: column;
        padding: 25px;
    }
    
    .search-box {
        min-width: 100%;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .sizes-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .hero {
        padding: 70px 20px 60px;
    }

    .section-header h2 {
        font-size: 32px;
    }
}
</style>
</head>
<body>

<div class="top-bar">
    <i class="fas fa-shipping-fast"></i> Envío gratis en compras mayores a $500 | <i class="fas fa-undo"></i> Devoluciones gratis en 30 días
</div>

<nav class="navbar">
    <div class="logo">SHOP HOLY</div>
    <div class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
        <a href="login_admin.php"><i class="fas fa-cog"></i> Admin</a>
        <a href="perfil.php"><i class="fas fa-user"></i> Perfil</a>
        <a href="carrito.php" class="cart-btn">
            <i class="fas fa-shopping-bag"></i>
            Carrito
            <?php if($cantidad_carrito > 0): ?>
                <span class="cart-count"><?php echo $cantidad_carrito; ?></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<section class="hero">
    <h1>Luxury Urban Fashion</h1>
    <p>Donde el estilo se encuentra con la elegancia</p>
</section>

<section class="filters-section">
    <div class="filters-container">
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="busqueda" placeholder="Buscar productos exclusivos..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</section>

<section class="products-section">
    <div class="section-header">
        <h2>Nuestra Colección</h2>
        <p>PIEZAS SELECCIONADAS CON EXCELENCIA</p>
    </div>
    
    <div class="products-grid">
        <?php if($resultado && $resultado->num_rows > 0): ?>
            <?php while($producto = $resultado->fetch_assoc()): ?>
                <div class="product-item">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($producto['imagen'] ?? 'images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <button class="favorite-btn" onclick="toggleFav(this)">
                            <i class="far fa-heart"></i>
                        </button>
                        <?php if($producto['nuevo'] ?? false): ?>
                            <div class="product-badge">Nuevo</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'General'); ?></div>
                        <h3 class="product-name"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <div class="product-price">$<?php echo number_format($producto['precio'], 2); ?></div>
                        
                        <form method="POST" action="" onsubmit="return validarFormulario(this)">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            
                            <div class="sizes-container">
                                <label class="sizes-label">Selecciona tu talla</label>
                                <div class="sizes-grid">
                                    <?php foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $talla): ?>
                                        <input type="radio" name="talla" value="<?php echo $talla; ?>" id="size_<?php echo $producto['id']; ?>_<?php echo $talla; ?>" class="size-option" required>
                                        <label for="size_<?php echo $producto['id']; ?>_<?php echo $talla; ?>" class="size-label"><?php echo $talla; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="quantity-selector">
                                <span class="quantity-label">Cantidad</span>
                                <div class="quantity-controls">
                                    <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                                    <input type="number" name="cantidad" value="1" min="1" max="99" class="qty-input" readonly>
                                    <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                                </div>
                            </div>
                            
                            <button type="submit" name="agregar" class="add-to-cart-btn">
                                <i class="fas fa-shopping-bag"></i> Añadir al Carrito
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-gem"></i>
                <h2>No se encontraron productos</h2>
                <p>Intenta con otra búsqueda o categoría</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer class="footer">
    <div class="footer-content">
        <h3>SHOP HOLY</h3>
        <p>© 2025 Shop Holy. Luxury Urban Fashion. Todos los derechos reservados.</p>
    </div>
</footer>

<div class="scroll-top" id="scrollTop" onclick="window.scrollTo({top:0, behavior:'smooth'})">
    <i class="fas fa-arrow-up"></i>
</div>

<script>
function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('.qty-input');
    let val = parseInt(input.value) + delta;
    if(val < 1) val = 1;
    if(val > 99) val = 99;
    input.value = val;
}

function toggleFav(btn) {
    btn.classList.toggle('active');
    const icon = btn.querySelector('i');
    icon.classList.toggle('far');
    icon.classList.toggle('fas');
}

function validarFormulario(form) {
    const tallaSeleccionada = form.querySelector('input[name="talla"]:checked');
    if (!tallaSeleccionada) {
        alert('Por favor selecciona una talla');
        return false;
    }
    return true;
}

window.addEventListener('scroll', () => {
    const scrollTop = document.getElementById('scrollTop');
    scrollTop.classList.toggle('show', window.scrollY > 300);
});
</script>

</body>
</html>
<?php $conexion->close(); ?>