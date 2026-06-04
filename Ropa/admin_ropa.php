<?php
// --- CONFIGURACIÓN DE BASE DE DATOS ---
$mysqli = new mysqli("localhost", "root", "", "ropa");
if ($mysqli->connect_error) die("Error de conexión: " . $mysqli->connect_error);

// --- CREAR CARPETA uploads SI NO EXISTE ---
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// --- AGREGAR PRODUCTO ---
if (isset($_POST['agregar'])) {
    $categoria_id = $_POST['categoria_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $disponible = $_POST['disponible'];

    // Imagen
    $imgRuta = "";
    if (!empty($_FILES['imagen']['name'])) {
        $fileName = time() . '_' . basename($_FILES['imagen']['name']);
        $destino = $uploadDir . $fileName;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
        $imgRuta = $destino;
    }

    $mysqli->query("INSERT INTO productos (categoria_id, nombre, descripcion, precio, imagen, disponible) VALUES ('$categoria_id', '$nombre', '$descripcion', '$precio', '$imgRuta', '$disponible')");
}

// --- ELIMINAR PRODUCTO ---
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $mysqli->query("DELETE FROM productos WHERE id = $id");
    header("Location: admin_ropa.php");
    exit;
}

// --- EDITAR PRODUCTO ---
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $categoria_id = $_POST['categoria_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $disponible = $_POST['disponible'];

    // Imagen
    $imgRuta = $_POST['imagen_actual'];
    if (!empty($_FILES['imagen']['name'])) {
        $fileName = time() . '_' . basename($_FILES['imagen']['name']);
        $destino = $uploadDir . $fileName;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
        $imgRuta = $destino;
    }

    $mysqli->query("UPDATE productos SET categoria_id='$categoria_id', nombre='$nombre', descripcion='$descripcion', precio='$precio', disponible='$disponible', imagen='$imgRuta' WHERE id=$id");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Shop Holy</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@300;400;700&family=Archivo+Black&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/8f3b7d3f19.js" crossorigin="anonymous"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Roboto', sans-serif;
    background: #0a0a0a;
    color: #fff;
    min-height: 100vh;
    padding: 40px 20px;
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at 20% 50%, rgba(212,175,55,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,215,0,0.06) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.admin-header {
    text-align: center;
    margin-bottom: 60px;
    padding: 30px;
    background: linear-gradient(135deg, rgba(212,175,55,0.1), rgba(255,215,0,0.05));
    border-radius: 20px;
    border: 2px solid rgba(212,175,55,0.3);
    box-shadow: 0 0 40px rgba(212,175,55,0.2);
    position: relative;
    z-index: 1;
}

.admin-header h1 {
    font-family: 'Playfair Display', sans-serif;
    font-size: clamp(40px, 8vw, 70px);
    letter-spacing: 6px;
    background: linear-gradient(45deg, #d4af37, #ffd700, #f4e5c3, #d4af37);
    background-size: 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradientFlow 3s ease infinite;
    margin-bottom: 10px;
}

@keyframes gradientFlow {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.admin-header p {
    font-size: 18px;
    color: #999;
    letter-spacing: 3px;
    text-transform: uppercase;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.section {
    background: linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
    border: 2px solid rgba(212,175,55,0.3);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

.section h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(32px, 5vw, 48px);
    background: linear-gradient(135deg, #d4af37, #ffd700);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 30px;
    letter-spacing: 3px;
    text-align: center;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    color: #d4af37;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 10px;
}

input[type="text"],
input[type="number"],
textarea,
select,
input[type="file"] {
    background: rgba(212,175,55,0.05);
    border: 2px solid rgba(212,175,55,0.3);
    border-radius: 10px;
    padding: 15px;
    color: #fff;
    font-size: 16px;
    font-family: 'Roboto', sans-serif;
    transition: all 0.3s ease;
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus,
select:focus {
    outline: none;
    border-color: #d4af37;
    box-shadow: 0 0 20px rgba(212,175,55,0.3);
    background: rgba(212,175,55,0.1);
}

textarea {
    resize: vertical;
    min-height: 100px;
}

input[type="file"] {
    padding: 10px;
    cursor: pointer;
}

.btn {
    padding: 16px 40px;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-agregar {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: #fff;
    box-shadow: 0 0 30px rgba(76,175,80,0.4);
    margin-top: 20px;
    width: 100%;
}

.btn-agregar:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 40px rgba(76,175,80,0.6);
}

.btn-editar {
    background: linear-gradient(135deg, #d4af37, #ffd700);
    color: #000;
    box-shadow: 0 0 20px rgba(212,175,55,0.3);
    padding: 10px 20px;
    font-size: 14px;
}

.btn-editar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(212,175,55,0.5);
}

.btn-eliminar {
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: #fff;
    box-shadow: 0 0 20px rgba(192,57,43,0.3);
    padding: 10px 20px;
    font-size: 14px;
}

.btn-eliminar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(192,57,43,0.5);
}

.table-container {
    overflow-x: auto;
    border-radius: 15px;
    background: rgba(0, 0, 0, 0.3);
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

th, td {
    padding: 18px;
    text-align: left;
    border-bottom: 1px solid rgba(212,175,55,0.2);
}

th {
    background: linear-gradient(135deg, #d4af37, #ffd700);
    color: #000;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    letter-spacing: 2px;
    text-transform: uppercase;
}

tr {
    transition: all 0.3s ease;
}

tr:hover {
    background: rgba(212,175,55,0.1);
}

td img {
    border-radius: 10px;
    border: 2px solid rgba(212,175,55,0.3);
    transition: transform 0.3s ease;
}

td img:hover {
    transform: scale(1.5);
    border-color: #d4af37;
}

.estado-disponible {
    color: #4CAF50;
    font-weight: 700;
}

.estado-no-disponible {
    color: #e74c3c;
    font-weight: 700;
}

.acciones {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.edit-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 3px solid #d4af37;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.preview-image {
    margin: 20px 0;
    text-align: center;
}

.preview-image label {
    color: #d4af37;
    display: block;
    margin-bottom: 15px;
}

.preview-image img {
    border-radius: 15px;
    border: 3px solid rgba(212,175,55,0.5);
    box-shadow: 0 10px 30px rgba(212,175,55,0.3);
}

.btn-guardar {
    background: linear-gradient(135deg, #d4af37, #ffd700);
    color: #000;
    box-shadow: 0 0 30px rgba(212,175,55,0.4);
    margin-top: 20px;
    width: 100%;
}

.btn-guardar:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 40px rgba(212,175,55,0.6);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    font-size: 60px;
    margin-bottom: 20px;
    color: #d4af37;
}

@media (max-width: 768px) {
    .section {
        padding: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .acciones {
        flex-direction: column;
    }
    
    .btn-editar, .btn-eliminar {
        width: 100%;
    }
}
</style>
</head>
<body>

<div class="admin-header">
    <h1>SHOP HOLY</h1>
    <p>Panel de Administración</p>
</div>

<div class="container">
    <!-- AGREGAR PRODUCTO -->
    <div class="section">
        <h2><i class="fas fa-plus-circle"></i> Agregar Producto</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="categoria_id">Categoría ID</label>
                    <input type="text" id="categoria_id" name="categoria_id" placeholder="Ej: 1" required>
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: Hoodie Urban" required>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio ($)</label>
                    <input type="number" id="precio" name="precio" placeholder="Ej: 599" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="disponible">Estado</label>
                    <select id="disponible" name="disponible">
                        <option value="1">Disponible</option>
                        <option value="0">No disponible</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" placeholder="Describe el producto..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="imagen"><i class="fas fa-image"></i> Imagen del Producto</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
            </div>
            
            <button class="btn btn-agregar" name="agregar">
                <i class="fas fa-plus"></i> Agregar Producto
            </button>
        </form>
    </div>

    <!-- LISTADO DE PRODUCTOS -->
    <div class="section">
        <h2><i class="fas fa-box-open"></i> Productos Registrados</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $mysqli->query("SELECT * FROM productos ORDER BY id DESC");
                    if ($res->num_rows > 0):
                        while ($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td>$<?= number_format($row['precio'], 2) ?></td>
                            <td>
                                <?php if ($row['imagen']): ?>
                                    <img src="<?= htmlspecialchars($row['imagen']) ?>" width="80" alt="<?= htmlspecialchars($row['nombre']) ?>">
                                <?php else: ?>
                                    <span style="color: #888;">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?= $row['disponible'] ? 'estado-disponible' : 'estado-no-disponible' ?>">
                                    <?= $row['disponible'] ? '✓ Disponible' : '✗ No disponible' ?>
                                </span>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a class="btn btn-editar" href="?edit=<?= $row['id'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a class="btn btn-eliminar" href="?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>No hay productos registrados</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- EDITAR PRODUCTO -->
    <?php if (isset($_GET['edit'])):
        $id = intval($_GET['edit']);
        $edit = $mysqli->query("SELECT * FROM productos WHERE id=$id")->fetch_assoc();
        if ($edit): ?>
        
        <div class="section edit-section">
            <h2><i class="fas fa-edit"></i> Editar Producto</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($edit['imagen']) ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_categoria_id">Categoría ID</label>
                        <input type="text" id="edit_categoria_id" name="categoria_id" value="<?= htmlspecialchars($edit['categoria_id']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_nombre">Nombre del Producto</label>
                        <input type="text" id="edit_nombre" name="nombre" value="<?= htmlspecialchars($edit['nombre']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_precio">Precio ($)</label>
                        <input type="number" id="edit_precio" name="precio" value="<?= $edit['precio'] ?>" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_disponible">Estado</label>
                        <select id="edit_disponible" name="disponible">
                            <option value="1" <?= $edit['disponible'] ? 'selected' : '' ?>>Disponible</option>
                            <option value="0" <?= !$edit['disponible'] ? 'selected' : '' ?>>No disponible</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_descripcion">Descripción</label>
                    <textarea id="edit_descripcion" name="descripcion"><?= htmlspecialchars($edit['descripcion']) ?></textarea>
                </div>

                <?php if ($edit['imagen']): ?>
                    <div class="preview-image">
                        <label>Imagen Actual:</label><br>
                        <img src="<?= htmlspecialchars($edit['imagen']) ?>" width="150" alt="Imagen actual">
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="edit_imagen"><i class="fas fa-image"></i> Nueva Imagen (opcional)</label>
                    <input type="file" id="edit_imagen" name="imagen" accept="image/*">
                </div>

                <button class="btn btn-guardar" name="editar">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>

    <?php endif;
    endif; ?>
</div>

</body>
</html>