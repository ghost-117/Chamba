-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2026 a las 06:30:31
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ropa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `parametro` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `puesto` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `rol` varchar(50) DEFAULT 'empleado',
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad` varchar(20) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `numero_orden` varchar(50) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `envio` decimal(10,2) DEFAULT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `created_at` datetime DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `numero_orden`, `fecha`, `total`, `envio`, `nombre_cliente`, `estado`, `created_at`, `usuario_id`) VALUES
(1, 'SH202512124329', '2025-12-12 16:35:25', 569.00, 99.00, 'itzel', 'Pendiente', '2025-12-12 16:35:25', NULL),
(2, 'SH202512138011', '2025-12-12 18:23:44', 569.00, 99.00, 'itzel', 'Pendiente', '2025-12-12 18:23:44', NULL),
(3, 'SH202512139054', NULL, NULL, NULL, NULL, 'Entregado', '2025-12-13 11:36:32', 2),
(4, 'SH202512132827', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-13 11:36:36', 2),
(5, 'SH202512137846', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-13 11:36:36', 2),
(6, 'SH202512139034', NULL, NULL, NULL, NULL, 'En Proceso', '2025-12-13 11:36:36', 2),
(7, 'SH202512137210', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-13 11:36:37', 2),
(8, 'SH202512142729', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-14 14:00:23', 2),
(9, 'SH202512159854', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-15 16:29:53', 2),
(10, 'SH202512169411', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-16 08:26:55', 2),
(11, 'SH202512162066', NULL, NULL, NULL, NULL, 'Entregado', '2025-12-16 09:44:34', 3),
(12, 'SH202512163144', NULL, NULL, NULL, NULL, 'Pendiente', '2025-12-16 13:58:00', 3),
(13, 'SH202602056292', NULL, NULL, NULL, NULL, 'Pendiente', '2026-02-05 11:52:23', 3),
(14, 'SH202602059623', NULL, NULL, NULL, NULL, 'Entregado', '2026-02-05 11:56:26', 3),
(15, 'SH202605146263', NULL, NULL, NULL, NULL, 'Pendiente', '2026-05-14 12:56:19', 3),
(16, 'SH202605195840', NULL, NULL, NULL, NULL, 'Pendiente', '2026-05-19 08:20:34', 3),
(17, 'SH202605265765', NULL, NULL, NULL, NULL, 'Pendiente', '2026-05-26 08:09:12', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_items`
--

CREATE TABLE `pedido_items` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `talla` varchar(10) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido_items`
--

INSERT INTO `pedido_items` (`id`, `pedido_id`, `producto_id`, `talla`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 14, 'XXL', 1, 0.00, 470.00),
(2, 2, 14, 'S', 1, 470.00, 470.00),
(3, 3, 14, NULL, 1, 470.00, NULL),
(4, 8, 14, NULL, 1, 470.00, NULL),
(5, 9, 14, NULL, 1, 470.00, NULL),
(6, 10, 14, NULL, 1, 470.00, NULL),
(7, 11, 14, NULL, 1, 470.00, NULL),
(8, 12, 12, NULL, 1, 260.00, NULL),
(9, 13, 3, NULL, 1, 520.00, NULL),
(10, 14, 15, NULL, 1, 320.00, NULL),
(11, 15, 15, NULL, 1, 320.00, NULL),
(12, 16, 15, NULL, 1, 320.00, NULL),
(13, 16, 14, NULL, 1, 470.00, NULL),
(14, 16, 12, NULL, 1, 260.00, NULL),
(15, 17, 8, NULL, 1, 210.00, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock` int(11) DEFAULT 0,
  `tallas` varchar(100) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `imagen`, `disponible`, `created_at`, `updated_at`, `stock`, `tallas`, `categoria`, `marca`) VALUES
(1, 1, 'Camisa Oversize Blanca', 'Camisa unisex estilo oversize en tela suave, ideal para outfits callejeros.', 299.00, 'uploads/1765570943_adidas.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 20:22:23', 12, '[\"M\",\"XL\"]', 'Camisetas', 'Adidas'),
(2, 1, 'Sudadera Negra ', 'Sudadera negra con diseño minimalista, cómoda y ligera.', 450.00, 'uploads/1765572693_sudadera_negra.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 20:51:33', 0, NULL, NULL, NULL),
(3, 2, 'Pantalón Cargo Arena', 'Pantalón cargo color arena con múltiples bolsas y ajuste en tobillos.', 520.00, 'uploads/1765572773_pantalonn.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 20:52:53', 0, NULL, NULL, NULL),
(4, 2, 'Jeans Azul', 'Jeans clásico azul, corte bage, tela stretch.', 399.00, 'uploads/1765572872_jeans.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 20:54:32', 0, NULL, NULL, NULL),
(5, 3, 'Gorra Negra Street', 'Gorra negra estilo urbano, diseño bordado frontal.', 180.00, 'uploads/1765574395_GORRA.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:19:55', 0, NULL, NULL, NULL),
(6, 1, 'Playera Gráfica Anime', 'Playera de algodón con estampado inspirado en estética anime.', 250.00, 'uploads/1765574444_anime.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:20:44', 0, NULL, NULL, NULL),
(7, 1, 'Top Deportivo Rosa', 'Top ligero y transpirable para entrenamiento o uso casual.', 220.00, 'uploads/1765574461_top.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:21:01', 0, NULL, NULL, NULL),
(8, 2, 'Shorts Deportivos Unisex', 'Short deportivo cómodo con tela fresca.', 210.00, 'uploads/1765574545_shorts.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:22:25', 0, NULL, NULL, NULL),
(9, 3, 'Mochila Urbana Negra', 'Mochila resistente con bolsillos múltiples y estilo urbano.', 550.00, 'uploads/1765574568_mochila.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:22:48', 0, NULL, NULL, NULL),
(10, 1, 'Chamarra Rompevientos Azul', 'Rompevientos ligero resistente al agua, ideal para clima fresco.', 480.00, 'uploads/1765574586_rompevientos.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:23:06', 0, NULL, NULL, NULL),
(11, 2, 'Joggers Grises', 'Joggers unisex color gris, cómodos y combinables.', 330.00, 'uploads/1765574663_joggers .jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:24:23', 0, NULL, NULL, NULL),
(12, 1, 'Blusa Casual Beige', 'Blusa suave con corte relajado y color beige clásico.', 260.00, 'uploads/1765574685_camisa.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:24:45', 0, NULL, NULL, NULL),
(13, 3, 'Cinturón Negro Clásico', 'Cinturón de piel sintética color negro, hebilla metálica.', 150.00, 'uploads/1765574706_cinturon.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:25:06', 0, NULL, NULL, NULL),
(14, 1, 'Hoodie Rosa Pastel', 'Hoodie unisex color rosa pastel con bolsas frontales.', 470.00, 'uploads/1765574761_hoddie.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:26:01', 0, NULL, NULL, NULL),
(15, 2, 'Falda Plisada Negra', 'Falda juvenil plisada color negro, tela ligera y cómoda.', 320.00, 'uploads/1765574781_falda.jpg', 1, '2025-12-12 19:59:08', '2025-12-12 21:26:21', 0, NULL, NULL, NULL),
(17, 8, 'computadora mak', 'una computadora mak nueva ', 10000.00, 'uploads/1772806564_1765572693_sudadera_negra.jpg', 1, '2026-03-06 14:16:04', '2026-03-06 14:16:04', 0, NULL, NULL, NULL),
(19, 5, 'huii', 'hotel', 1000.00, 'uploads/1780532258_hotel(1).jpg', 1, '2026-06-04 00:17:38', '2026-06-04 00:17:38', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `genero` enum('M','F','Otro') DEFAULT 'Otro',
  `rol` enum('cliente','admin','empleado') DEFAULT 'cliente',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `puesto`, `descripcion`, `email`, `password`, `imagen`, `telefono`, `direccion`, `fecha_nacimiento`, `fecha_ingreso`, `genero`, `rol`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'itzel', 'Judith', NULL, NULL, 'Itz@gmail.com', '$2y$10$iLSGPaihviJ3e9YbJ//ZDeXeT.4/fb5dAJn7RdYJebelr.GAK0eDO', NULL, '12345678', 'dfghjk', '2011-11-11', NULL, '', 'cliente', 1, '2025-12-12 01:20:46', '2025-12-12 01:20:46'),
(2, 'itzel', NULL, NULL, NULL, 'ivonne77@gmail.com', '', NULL, '1234567890', 'sdfgbhnm,', NULL, NULL, 'Otro', 'cliente', 1, '2025-12-13 17:36:31', '2025-12-16 14:26:55'),
(3, 'itzel', NULL, NULL, NULL, 'itzel@gmail', '', NULL, '2411066507', 'la escondida', NULL, NULL, 'Otro', 'cliente', 1, '2025-12-16 15:44:34', '2026-05-26 14:09:12'),
(4, 'yandel', 'sanchez', NULL, NULL, 'yandel267@gmail.com', '$2y$10$Nh8y1Nxl5FfjrwO3fvxQBuXlTheMvd0gtH839QLag5oOD6mnQ.Ily', NULL, '2471319895', 'apizaco tlaxcala', '2008-03-21', NULL, '', 'cliente', 1, '2026-03-03 23:24:57', '2026-03-03 23:24:57'),
(5, 'eduardo', 'tzompantzi', NULL, NULL, 'eduardo@gmail.com', '$2y$10$bBp3EmiLmbKwT2uMwAUA8.I59B3F.CAxoIsXCy4Vgb4VyrJULG.ZS', NULL, '2461494094', 'calle hidalgo', '1995-05-21', NULL, '', 'cliente', 1, '2026-05-19 14:33:03', '2026-05-19 14:33:03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedido_items`
--
ALTER TABLE `pedido_items`
  ADD CONSTRAINT `pedido_items_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
