<?php
session_start();

// Si ya tienes el id del usuario en sesión, úsalo:
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Conexión BD
$mysqli = new mysqli("localhost", "root", "", "ropa");
if ($mysqli->connect_error) die("Error BD: " . $mysqli->connect_error);

// 1) Obtener nombre del usuario
$nombre = "Usuario";
if ($usuario_id) {
    $resUser = $mysqli->query("SELECT nombre FROM usuarios WHERE id = $usuario_id LIMIT 1");
    if ($resUser && $resUser->num_rows > 0) {
        $rowUser = $resUser->fetch_assoc();
        $nombre = $rowUser['nombre'];
    }
} else {
    // Si no hay sesión, permitimos ?nombre= en la URL como fallback
    $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : "Usuario";
}

// 2) Contar pedidos del usuario
$pedidos = 0;
if ($usuario_id) {
    $resPed = $mysqli->query("SELECT COUNT(*) AS total FROM pedidos WHERE usuario_id = $usuario_id");
    if ($resPed && $resPed->num_rows > 0) {
        $rowPed  = $resPed->fetch_assoc();
        $pedidos = (int)$rowPed['total'];
    }
}

// 3) Calcular nivel
if ($pedidos >= 40)      $nivel = "Diamante";
elseif ($pedidos >= 20)  $nivel = "Oro";
elseif ($pedidos >= 10)  $nivel = "Plata";
else                     $nivel = "Bronce";

// Color por nivel (si tu PHP es < 8.0, te dejo más abajo versión sin match)
$colorNivel = match($nivel) {
    "Bronce"   => "#CD7F32",
    "Plata"    => "#C0C0C0",
    "Oro"      => "#FFD700",
    "Diamante" => "#B9F2FF",
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenida</title>
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #1a0000 0%, #000 100%);
            color: white;
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .contenedor {
            position: relative;
            background: rgba(0,0,0,0.65);
            padding: 45px;
            border-radius: 25px;
            border: 4px solid #FFD700;
            box-shadow: 0 0 30px rgba(255,215,0,0.6), inset 0 0 25px rgba(255,0,0,0.4);
            animation: fadeInUp 1.5s ease-in-out;
        }

        .contenedor::before {
            content: "✦";
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 28px;
            color: #FFD700;
            text-shadow: 0 0 10px #FF4500;
        }

        h2 {
            font-size: 36px;
            margin-bottom: 10px;
            color: #FFD700;
            font-family: 'Great Vibes', cursive;
            text-shadow: 0 0 15px #FFD700, 0 0 40px #FF4500;
            animation: glow 3s infinite alternate;
        }

        p {
            font-size: 18px;
            margin-bottom: 15px;
            color: #f5f5f5;
        }

        .nivel {
            font-weight: bold;
            font-size: 20px;
        }

        .contador {
            font-size: 70px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #FF0000;
            text-shadow: 0 0 30px #FF0000, 0 0 55px #FFD700;
            animation: pulse 1.3s infinite;
        }

        @keyframes pulse {
            0% {transform: scale(1);}
            50% {transform: scale(1.2);}
            100% {transform: scale(1);}
        }

        @keyframes glow {
            from { text-shadow: 0 0 10px #FFD700, 0 0 30px #FF0000; }
            to { text-shadow: 0 0 25px #FF0000, 0 0 50px #FFD700; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>
    <div class="contenedor">
        <h2>🥢 Bienvenido, <?php echo htmlspecialchars($nombre); ?> </h2>
        <p>Tu nivel actual: <span class="nivel" style="color: <?php echo $colorNivel; ?>;"><?php echo $nivel; ?></span></p>

        <?php
        $pedidosParaSiguiente = match($nivel) {
            "Bronce" => 10 - $pedidos,
            "Plata" => 20 - $pedidos,
            "Oro" => 40 - $pedidos,
            "Diamante" => 0,
        };
        if($pedidosParaSiguiente > 0){
            echo "<p>Faltan $pedidosParaSiguiente pedidos para subir al siguiente nivel.</p>";
        } else {
            echo "<p>¡Has alcanzado el nivel máximo!</p>";
        }
        ?>

        <p>Serás dirigid@ al menú en un momento.</p>
        <div class="contador" id="contador">10</div>
    </div>

    <script>
    console.log("Script cargado");

    particlesJS("particles-js", {
        "particles": {
            "number": { "value": 90, "density": {"enable": true, "value_area": 900} },
            "color": {"value": ["#FFD700", "#FF4500", "#B22222"]},
            "shape": {"type": "circle"},
            "opacity": {
                "value": 0.8, "random": true,
                "anim": { "enable": true, "speed": 2, "opacity_min": 0.2, "sync": false }
            },
            "size": {
                "value": 6, "random": true,
                "anim": { "enable": true, "speed": 3, "size_min": 1, "sync": false }
            },
            "line_linked": {"enable": false},
            "move": {"enable": true, "speed": 2, "direction": "top", "out_mode": "out"}
        },
        "interactivity": {
            "events": { "onhover": {"enable": true, "mode": "bubble"}, "onclick": {"enable": true, "mode": "push"} },
            "modes": { "bubble": {"distance":200,"size":10,"duration":1,"opacity":1}, "push":{"particles_nb":5} }
        },
        "retina_detect": true
    });

    let contador = 10;

    function iniciarContador() {
        console.log("Iniciando contador");
        const elemento = document.getElementById("contador");
        console.log("Elemento contador:", elemento);

        let interval = setInterval(() => {
            console.log("Valor contador:", contador);
            elemento.innerText = contador;

            if (contador === 0) {
                clearInterval(interval);
                console.log("Redirigiendo a menu.php");
                window.location.href = "ropa.php";
            }

            contador--;
        }, 1000);
    }

    window.onload = iniciarContador;
</script>
</body> </html>