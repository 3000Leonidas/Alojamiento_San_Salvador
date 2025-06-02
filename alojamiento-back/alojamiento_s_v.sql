-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-06-2025 a las 21:52:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `alojamiento_s_v`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_admin`
--

CREATE TABLE `acceso_admin` (
  `id` bigint(20) NOT NULL,
  `id_admin` bigint(20) DEFAULT NULL,
  `fecha_concesion_acceso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acceso_huesped`
--

CREATE TABLE `acceso_huesped` (
  `id` bigint(20) NOT NULL,
  `fecha_concesion_acceso` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` bigint(20) DEFAULT NULL,
  `id_huesped` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caracteristicas_habitaciones`
--

CREATE TABLE `caracteristicas_habitaciones` (
  `id` bigint(20) NOT NULL,
  `id_habitacion` bigint(20) DEFAULT NULL,
  `nombre_caracteristica` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuentos_especiales`
--

CREATE TABLE `descuentos_especiales` (
  `id` bigint(20) NOT NULL,
  `fecha_fin` date NOT NULL,
  `nombre_descuento` varchar(255) NOT NULL,
  `porcentaje_descuento` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `id` bigint(20) NOT NULL,
  `precio_por_noche` decimal(10,2) NOT NULL,
  `tipo_habitacion` varchar(255) NOT NULL,
  `esta_disponible` tinyint(1) DEFAULT 1,
  `numero_habitacion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`id`, `precio_por_noche`, `tipo_habitacion`, `esta_disponible`, `numero_habitacion`) VALUES
(1, 140.00, 'Familiar', 1, '202'),
(2, 190.00, 'Familiar', 1, '211'),
(3, 200.00, 'Matrimonial', 1, '200'),
(4, 200.00, 'Matrimonial', 1, '201'),
(5, 170.00, 'Amigos', 0, '203'),
(6, 300.00, 'Eventos', 1, '204');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `huespedes`
--

CREATE TABLE `huespedes` (
  `id` bigint(20) NOT NULL,
  `fecha_entrada` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `fecha_salida` date DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `numero_telefono` varchar(255) DEFAULT NULL,
  `ci` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `huespedes`
--

INSERT INTO `huespedes` (`id`, `fecha_entrada`, `email`, `apellido`, `fecha_salida`, `nombre`, `numero_telefono`, `ci`) VALUES
(1, '2025-12-31', 'leconaandre51@gmail.com', 'Lecoña saravia', '2025-12-31', 'steven andré', '76710783', '11065750'),
(2, '2025-11-28', 'RAMIRO@GMAIL.COM', 'MARCA', '2025-11-30', 'RAMIRO', '12345678', '12345678'),
(3, '2025-09-27', 'CRISTOFER@GMAIL.COM', 'ANGEL ', '2025-09-28', 'CRITOFER', '78897674', '14240344');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_habitaciones`
--

CREATE TABLE `imagenes_habitaciones` (
  `id` bigint(20) NOT NULL,
  `url_imagen` varchar(255) NOT NULL,
  `id_habitacion` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes_habitaciones`
--

INSERT INTO `imagenes_habitaciones` (`id`, `url_imagen`, `id_habitacion`) VALUES
(1, 'uploads/img_68376f46ac01d_Matrimonial1.jpg', 1),
(2, 'uploads/img_6837b0b1e487f_familiar1.jpg', 1),
(7, 'uploads/img_6838ac0c05e1c_Familiar2.jpg', 2),
(8, 'uploads/img_683b4cb518c8b_Matrimonial1.jpg', 3),
(9, 'uploads/img_683b4d4700618_Matrimonial2.jpg', 4),
(10, 'uploads/img_683b4d8eaeba3_Familiar2.jpg', 5),
(11, 'uploads/img_683b4db54d57c_img_683862b0f0622_img_6837b0b1e487f_familiar1.jpg', 6),
(12, 'uploads/img_683b8b2965bdd_Matrimonial1.jpg', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) NOT NULL,
  `url_codigo_qr` varchar(255) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_reserva` bigint(20) DEFAULT NULL,
  `metodo_pago` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` bigint(20) NOT NULL,
  `id_huesped` bigint(20) DEFAULT NULL,
  `fecha_entrada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `tipo_reserva` varchar(255) DEFAULT NULL,
  `id_habitacion` bigint(20) DEFAULT NULL,
  `fecha_reserva` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `id_huesped`, `fecha_entrada`, `fecha_salida`, `tipo_reserva`, `id_habitacion`, `fecha_reserva`) VALUES
(2, 1, '2025-12-31', '2025-12-31', 'normal', 1, '2025-05-31'),
(3, 1, '2025-12-31', '2025-12-31', 'normal', 1, '2025-05-31'),
(4, 2, '2025-11-28', '2025-11-30', 'grupo', 1, '2025-05-31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` bigint(20) NOT NULL,
  `nombre_servicio` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_huespedes`
--

CREATE TABLE `servicios_huespedes` (
  `id` bigint(20) NOT NULL,
  `id_servicio` bigint(20) DEFAULT NULL,
  `fecha_servicio` date DEFAULT curdate(),
  `id_huesped` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `hash_contrasena` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre_usuario` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `hash_contrasena`, `rol`, `creado_en`, `nombre_usuario`) VALUES
(1, '$2y$10$f3IvQ9TWYcVN6Hhg7CQChemOCDOMKQAgjcgKhPpULDR8FWAaNFa8i', 'admin', '2025-05-26 01:27:47', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acceso_admin`
--
ALTER TABLE `acceso_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indices de la tabla `acceso_huesped`
--
ALTER TABLE `acceso_huesped`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_huesped` (`id_huesped`);

--
-- Indices de la tabla `caracteristicas_habitaciones`
--
ALTER TABLE `caracteristicas_habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_habitacion` (`id_habitacion`);

--
-- Indices de la tabla `descuentos_especiales`
--
ALTER TABLE `descuentos_especiales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_descuento` (`nombre_descuento`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_habitacion` (`numero_habitacion`);

--
-- Indices de la tabla `huespedes`
--
ALTER TABLE `huespedes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ci` (`ci`);

--
-- Indices de la tabla `imagenes_habitaciones`
--
ALTER TABLE `imagenes_habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_habitacion` (`id_habitacion`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_huesped` (`id_huesped`),
  ADD KEY `id_habitacion` (`id_habitacion`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_servicio` (`nombre_servicio`);

--
-- Indices de la tabla `servicios_huespedes`
--
ALTER TABLE `servicios_huespedes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `id_huesped` (`id_huesped`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acceso_admin`
--
ALTER TABLE `acceso_admin`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `acceso_huesped`
--
ALTER TABLE `acceso_huesped`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caracteristicas_habitaciones`
--
ALTER TABLE `caracteristicas_habitaciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `descuentos_especiales`
--
ALTER TABLE `descuentos_especiales`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `huespedes`
--
ALTER TABLE `huespedes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `imagenes_habitaciones`
--
ALTER TABLE `imagenes_habitaciones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios_huespedes`
--
ALTER TABLE `servicios_huespedes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acceso_admin`
--
ALTER TABLE `acceso_admin`
  ADD CONSTRAINT `acceso_admin_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `acceso_huesped`
--
ALTER TABLE `acceso_huesped`
  ADD CONSTRAINT `acceso_huesped_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acceso_huesped_ibfk_2` FOREIGN KEY (`id_huesped`) REFERENCES `huespedes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `caracteristicas_habitaciones`
--
ALTER TABLE `caracteristicas_habitaciones`
  ADD CONSTRAINT `caracteristicas_habitaciones_ibfk_1` FOREIGN KEY (`id_habitacion`) REFERENCES `habitaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `imagenes_habitaciones`
--
ALTER TABLE `imagenes_habitaciones`
  ADD CONSTRAINT `imagenes_habitaciones_ibfk_1` FOREIGN KEY (`id_habitacion`) REFERENCES `habitaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reservas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_huesped`) REFERENCES `huespedes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_habitacion`) REFERENCES `habitaciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicios_huespedes`
--
ALTER TABLE `servicios_huespedes`
  ADD CONSTRAINT `servicios_huespedes_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `servicios_huespedes_ibfk_2` FOREIGN KEY (`id_huesped`) REFERENCES `huespedes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
