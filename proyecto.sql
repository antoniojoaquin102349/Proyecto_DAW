-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-06-2025 a las 15:18:58
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
-- Base de datos: `proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recambios`
--

CREATE TABLE `recambios` (
  `referencia` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `precio` double NOT NULL,
  `vendido` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `stock` int(50) NOT NULL,
  `imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recambios`
--

INSERT INTO `recambios` (`referencia`, `categoria`, `nombre`, `precio`, `vendido`, `descripcion`, `stock`, `imagen`) VALUES
('12564675', 'Mecanica', 'Frenos de disco BREMBO', 500, 38, ' frenos de disco ', 448, 'imagenes/disco_de_freno.avif'),
('125646857486794', 'Mecanica', 'Frenos de disco CEISA', 500, 106, ' frenos de disco perforasfgh', 120, 'imagenes/disco_de_freno.avif'),
('1546241', 'ruedas', 'Neumático MICHELIN ', 180, 30, 'Neumático MICHELIN SÑASLKEDRJGFHAQUIOR4TG', 490, 'imagenes/ruedas.jpg'),
('15465465', 'ruedas', 'Neumático CST', 180, 200, 'Neumático CST SÑASLKEDRJGFHAQUIOR4TG', 205, 'imagenes/ruedas.jpg'),
('1958648', 'suspension', 'Amortiguadores KING', 560, 86, 'Amortiguadores de gas ', 379, 'imagenes/amortiguadores.jpg'),
('49846', 'electricidad', 'Barra LED NLpearl', 300, 43, 'barra led de varias medidas', 42, 'imagenes/barra_led.avif');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `telefono` int(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `CPostal` int(6) NOT NULL,
  `n_tarjeta` int(50) NOT NULL,
  `n_seguridad` int(10) NOT NULL,
  `fecha_cadu` date NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `primer_apellido`, `segundo_apellido`, `dni`, `telefono`, `email`, `direccion`, `CPostal`, `n_tarjeta`, `n_seguridad`, `fecha_cadu`, `contrasena`) VALUES
(1, 'Antonio Joaquín', 'Carmona', 'Ariza', '15452317C', 650817633, 'antoniojoaquincarmona@gmail.com', 'Muñoz Pérez 27', 14520, 165465, 12, '2025-05-29', '$2y$10$WYLoHwy/o3BR7gKziugcBuYvULPLhe0rH6zi8//.TzvQ/UClgohyO'),
(2, 'Cristina', 'Carmona', 'Ariza', '14542748A', 650817633, 'cristina@gmail.com', 'Muñoz Pérez 27', 14520, 2147483647, 12, '2025-05-16', '$2y$10$uP1.to5vsfTHOFM8iXN9zuHMSUPdUEJ7AZwS9y7IhvbL5ic/E8IGG'),
(3, 'Joaquín', 'Carmona', 'Ariza', '45782565B', 650817633, 'juakigac@gmail.com', 'Muñoz Pérez 27', 14520, 2147483647, 452, '2025-05-31', '$2y$10$Ei45siVbLnRkWIlbshAaiuy/7HJ2W8QpcraVfkvwYLmNXSrBic0.y'),
(4, 'Jesus', 'Carmona', 'Ariza', '58756371L', 650817633, 'jesus@gmail.com', 'Muñoz Pérez 27', 14520, 5746864, 254, '2025-05-31', '$2y$10$5Zj1EiKEbXyXYZUrttfjI.6Ikb6596aGM.FtwyX0ZP29yh.0NWza.'),
(15, 'alba', 'Carmona', 'Ariza', '15487514c', 650817633, 'alba@gmail.com', 'Muñoz Pérez 27', 14520, 15634654, 152, '2025-06-12', '$2y$10$lsnNhsEqXUZIiLDMR74Rc.sbFZ8nEkufNtb.jwNUdtm3hY650jI.K');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `recambios`
--
ALTER TABLE `recambios`
  ADD PRIMARY KEY (`referencia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
