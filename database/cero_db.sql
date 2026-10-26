-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2026 a las 03:14:33
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
-- Base de datos: `cero_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id_cita` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_servicio` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `estado_cita` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id_cita`, `id_paciente`, `id_usuario`, `id_servicio`, `fecha_hora`, `estado_cita`) VALUES
(3, 9, NULL, 1, '2026-05-11 19:30:00', 'Rechazada'),
(5, 11, NULL, 3, '2026-05-28 20:11:00', 'Rechazada'),
(6, 12, NULL, 3, '2026-05-12 19:11:00', 'Rechazada'),
(10, 16, NULL, 3, '2026-05-27 00:37:00', 'Rechazada'),
(11, 17, NULL, 1, '2026-05-19 23:40:00', 'Completada'),
(12, 18, 35, 3, '2026-05-14 00:57:00', 'Rechazada'),
(13, 19, NULL, 3, '2026-05-13 08:34:00', 'Completada'),
(14, 20, NULL, 3, '2026-05-13 13:08:00', 'Completada'),
(15, 21, NULL, 3, '2026-05-27 05:06:00', 'Rechazada'),
(16, 18, NULL, 3, '2026-05-21 20:00:00', 'Completada'),
(17, 22, 43, 1, '2026-05-21 21:30:00', 'Completada'),
(18, 23, 44, 1, '2026-05-26 02:23:00', 'Completada'),
(19, 22, 43, 3, '2026-05-27 07:53:00', 'Completada'),
(20, 22, 43, 3, '2026-05-27 16:13:00', 'Completada'),
(21, 22, 43, 1, '2026-05-27 11:25:00', 'Completada'),
(22, 23, 44, 1, '2026-05-27 11:25:00', 'Completada'),
(23, 22, 43, 3, '2026-05-28 14:00:00', 'Completada'),
(24, 22, 43, 3, '2026-05-28 12:20:00', 'Pendiente'),
(25, 22, 43, 3, '2026-05-27 12:20:00', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evolucion_medica`
--

CREATE TABLE `evolucion_medica` (
  `id_evolucion` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_evaluacion` date NOT NULL,
  `diagnostico` text NOT NULL,
  `tratamiento` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evolucion_medica`
--

INSERT INTO `evolucion_medica` (`id_evolucion`, `id_paciente`, `id_usuario`, `fecha_evaluacion`, `diagnostico`, `tratamiento`) VALUES
(1, 4, 20, '2026-04-20', '', ''),
(2, 4, 20, '2026-04-20', 'rotura de ligamento cruzado ', 'readaptacion, fortalecimiento y control'),
(3, 4, 20, '2026-04-20', 'mejora significativa de la movilidad', 'estiramiento y  rehabilitación'),
(4, 4, 20, '2026-04-20', '', ''),
(5, 4, 4, '2026-05-11', 'mejoria ', 'movilidad articular'),
(6, 7, 4, '2026-05-11', 'adjadhahdjdhasjdhajhdj', 'movilidad tipo x'),
(7, 7, 4, '2026-05-12', 'adjadhahdjdhasjdhajhdj', 'movilidad tipo x'),
(8, 7, 4, '2026-05-12', 'huhuhuhuh', 'bbjbb'),
(10, 11, 30, '2026-05-12', 'adjadhahdjdhasjdhajhdj', 'wdadad'),
(11, 18, 4, '2026-05-13', 'Mejora de movilidad en el abdomen', 'hacer biceps'),
(12, 18, 26, '2026-05-13', 'ids', 'dqid'),
(13, 20, 4, '2026-05-18', 'xd', 'xd'),
(14, 20, 4, '2026-05-18', '', ''),
(15, 20, 4, '2026-05-18', 'huhuhuhuh', 'movilidad tipo x'),
(16, 23, 4, '2026-05-27', 'mejor de movilidad', 'movilidad tipo x'),
(17, 22, 4, '2026-05-27', 'Rotura del ligamento cruzado', 'Ejercicios de movilidad, estiramientos, todo sin impacto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kpi_progresos`
--

CREATE TABLE `kpi_progresos` (
  `id_kpi` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `fecha_medicion` date NOT NULL,
  `peso_kg` decimal(5,2) NOT NULL,
  `fuerza_maxima` decimal(5,2) DEFAULT NULL,
  `rango_movimiento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `kpi_progresos`
--

INSERT INTO `kpi_progresos` (`id_kpi`, `id_paciente`, `fecha_medicion`, `peso_kg`, `fuerza_maxima`, `rango_movimiento`) VALUES
(1, 7, '2026-05-11', 80.00, 100.00, '90'),
(2, 7, '2026-05-11', 85.00, 90.00, '0'),
(3, 4, '2026-05-12', 80.00, 120.00, '120'),
(4, 11, '2026-05-12', 999.99, 787.00, '0787'),
(5, 18, '2026-05-13', 999.99, 999.99, '32817'),
(6, 18, '2026-05-13', 999.99, 0.00, ''),
(7, 18, '2026-05-13', 80.00, 120.00, '90'),
(8, 20, '2026-05-13', 80.00, 100.00, '0-100'),
(9, 20, '2026-05-13', 82.00, 120.00, '100-120'),
(11, 18, '2026-05-18', 89.00, 98.00, '89'),
(12, 20, '2026-05-26', 10.00, 10.00, '10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id_paciente`, `cedula`, `nombre_completo`, `fecha_nacimiento`, `correo`, `password_hash`, `fecha_registro`) VALUES
(4, '3223342524', 'ricardo', '2007-02-24', 'alavarado@gmail.com', '$2y$10$3zJPF8zwSAKlHC1RTtuuNe8JoEyOiS6w0ADG98Dad.AdmQdlntXQW', '2026-04-20 19:28:41'),
(7, '10489533', 'tonny', '1975-03-27', 'tonny@gmail.com', '$2y$10$jDnRQfJksX1WvR4vXjx7VeWAlg1pm5UdRE5XdQBc0LP2h0qI0MKF.', '2026-04-20 21:26:01'),
(8, '9094675', 'Marisol', '1965-04-06', 'marisol@gmail.com', '$2y$10$viBR1oh08nd6fOJwcJDX4eVJ2G/OhxWZ29IoEs6WXbWBQRMeK6Ply', '2026-04-20 21:29:48'),
(9, '5646243545553', 'kelvin stanzione', '1990-01-01', NULL, NULL, '2026-05-11 21:51:08'),
(11, '21827172', 'prueba2 dasda', '1990-01-01', NULL, NULL, '2026-05-12 19:06:52'),
(12, '565565', 'diego herrera', '1990-01-01', NULL, NULL, '2026-05-12 19:08:02'),
(16, '1920911', 'h wihds', '1990-01-01', NULL, NULL, '2026-05-13 00:33:55'),
(17, '3123123', 'dsdds fssd', '1990-01-01', NULL, NULL, '2026-05-13 00:40:37'),
(18, '13990964', 'Fabricio marcial', '2006-12-21', 'fabrr@gmail.com', '$2y$10$0Hyle.bjZewEJCdSqE/SVegOLV9cGglPHjOCwbATQsEXTS0oE4JxS', '2026-05-13 00:51:40'),
(19, '2131233213', 'qdddqwd dsadsad', '1990-01-01', NULL, NULL, '2026-05-13 12:34:43'),
(20, '31444444', 'Magda Perozo', '1990-01-01', NULL, NULL, '2026-05-13 15:06:12'),
(21, '8989898', 'hh kk', '1990-01-01', NULL, NULL, '2026-05-20 19:53:17'),
(22, '31973411', 'Fabricio Matheus', '2006-12-04', 'fabriciocliente@gmail.com', '$2y$10$x0Q6f44hdTtnyqAfZ0BSL.Arzu3DwZRf7lebn4aqS08EdJdzFEvl2', '2026-05-21 21:25:05'),
(23, '2676216', 'calamardo', '2026-05-26', 'calamardo@gmail.com', '$2y$10$mK3N4lvcKXvDa4cU07G0YuNOyr5LyDApAKt4I5M22azcc976I98ba', '2026-05-27 02:16:24'),
(24, '110', 'fabricio', '2006-01-15', '110@gmail.com', '$2y$10$rdtlWnxFBSkAt26L6fY8OeucWHvydnxD7prNJLOgFntE36bFSU2YO', '2026-10-16 01:07:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes_entrenamiento`
--

CREATE TABLE `planes_entrenamiento` (
  `id_plan` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `objetivo_deportivo` varchar(255) NOT NULL,
  `descripcion_rutina` text NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado_plan` varchar(50) DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes_entrenamiento`
--

INSERT INTO `planes_entrenamiento` (`id_plan`, `id_paciente`, `id_usuario`, `objetivo_deportivo`, `descripcion_rutina`, `fecha_inicio`, `fecha_fin`, `estado_plan`) VALUES
(1, 7, 22, 'Fuerza abdominal', 'plan de entrenamiento bsado asdkasdlkd', '2026-05-12', '2026-06-12', 'Completado'),
(3, 11, 22, 'Fuerza abdominal', 'ikj', '2026-05-13', '2026-06-03', 'Activo'),
(4, 11, 11, 'Fuerza abdominal', 'dihwoasohdos', '2026-05-06', '2026-05-19', 'Activo'),
(6, 20, 22, 'Fuerza Explosiva', 'Plan de entrenamiento de sentadilla y trote', '2026-05-15', '2026-06-01', 'Completado'),
(7, 18, 22, 'jooj', 'guigikvu', '2026-05-19', '2026-05-27', 'Completado'),
(8, 20, 22, 'Fuerza abdominal', 'juhjhjkhjkhkj', '2026-05-27', '2026-05-31', 'Completado'),
(9, 20, 20, 'jooj', 'ewadada', '2026-05-18', '2026-05-31', 'Completado'),
(10, 18, 39, '9898', 'jgjjk', '2026-05-18', '2026-05-31', 'Completado'),
(11, 23, 20, 'Fuerza abdominal', 'Plan de entrenamiento basado en fuerza', '2026-05-26', '2026-07-26', 'Completado'),
(12, 7, 22, 'Fuerza Explosiva', 'Plan de entrenamiento para fuerza explosiva', '2026-05-26', '2026-07-26', 'Completado'),
(13, 18, 39, 'Fuerza abdominal', 'plan de', '2026-05-26', '2026-05-27', 'Completado'),
(14, 22, 49, 'Varicocele', 'Rutina dedidacada a la recuperacion', '2026-05-27', '2026-06-27', 'Completado'),
(15, 22, 20, 'Cardio', 'trote 20 minutos, subir escaleras', '2026-05-27', '2026-07-27', 'Completado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes_nutricionales`
--

CREATE TABLE `planes_nutricionales` (
  `id_plan_nutricional` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `requerimiento_calorico` decimal(6,2) NOT NULL,
  `distribucion_macros` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planes_nutricionales`
--

INSERT INTO `planes_nutricionales` (`id_plan_nutricional`, `id_paciente`, `id_usuario`, `requerimiento_calorico`, `distribucion_macros`, `fecha_registro`) VALUES
(2, 7, 4, 8787.00, 'Prot: 676 g; CHO: 6868 g; Grasas: 90 g', '2026-05-11 23:13:02'),
(3, 7, 4, 80.00, 'Prot: 77 g; CHO: 78 g; Grasas: 90 g', '2026-05-12 18:07:08'),
(4, 7, 4, 877.00, 'Prot: 89 g; CHO: 56 g; Grasas: 89 g', '2026-05-12 18:31:54'),
(6, 12, 4, 9999.99, 'Prot: 87 g; CHO: 989 g; Grasas: 90 g', '2026-05-12 20:21:20'),
(7, 12, 4, 9999.99, 'Prot: 8 g; CHO: 98 g; Grasas: 90 g', '2026-05-12 20:22:07'),
(8, 12, 4, 105.00, 'Prot: 89 g; CHO: 70 g; Grasas: 89 g', '2026-05-12 20:22:41'),
(9, 11, 4, 8978.00, 'Prot: 7887 g; CHO: 9 g; Grasas: 90 g', '2026-05-12 20:23:20'),
(10, 11, 4, 717.00, 'Prot: 89291 g; CHO: 2172 g; Grasas: 83928 g', '2026-05-12 20:29:22'),
(11, 11, 30, 9999.99, 'Prot: 32 g; CHO: 323 g; Grasas: 43 g', '2026-05-12 20:46:23'),
(12, 18, 4, 313.00, 'Prot: 321 g; CHO: 3213 g; Grasas: 31 g', '2026-05-13 07:23:16'),
(13, 20, 4, 1500.00, 'Prot: 600 g; CHO: 100 g; Grasas: 100 g', '2026-05-13 11:11:23'),
(14, 18, 4, 3.10, 'Prot: 98 g; CHO: 88 g; Grasas: 70 g', '2026-05-18 20:35:50'),
(15, 20, 4, 779.00, 'Prot: 87 g; CHO: 989 g; Grasas: 909 g', '2026-05-18 20:39:04'),
(16, 18, 4, 898.00, 'Prot: 88 g; CHO: 656 g; Grasas: 90 g', '2026-05-18 21:31:45'),
(17, 18, 4, 78.00, 'Prot: 99 g; CHO: 99 g; Grasas: 99 g', '2026-05-18 21:41:27'),
(18, 18, 4, 110.00, 'Prot: 57 g; CHO: 78 g; Grasas: 90 g', '2026-05-26 22:45:12'),
(19, 20, 48, 100.00, 'Prot: 100 g; CHO: 100 g; Grasas: 100 g', '2026-05-26 22:56:38'),
(20, 22, 4, 2200.00, 'Prot: 130 g; CHO: 260 g; Grasas: 70 g', '2026-05-27 10:09:39'),
(21, 22, 4, 1500.00, 'Prot: 500 g; CHO: 500 g; Grasas: 500 g', '2026-05-27 12:03:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_termica`
--

CREATE TABLE `recuperacion_termica` (
  `id_sesion` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `tipo_terapia` varchar(50) NOT NULL,
  `duracion_minutos` int(11) NOT NULL,
  `fecha_sesion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recuperacion_termica`
--

INSERT INTO `recuperacion_termica` (`id_sesion`, `id_paciente`, `tipo_terapia`, `duracion_minutos`, `fecha_sesion`) VALUES
(1, 4, 'Sauna', 30, '2026-05-11 21:44:02'),
(2, 4, 'Crioterapia', 30, '2026-05-11 21:44:15'),
(3, 7, 'Sauna', 15, '2026-05-12 03:19:18'),
(4, 7, 'Sauna', 15, '2026-05-12 19:26:02'),
(5, 7, 'Crioterapia', 15, '2026-05-12 19:26:06'),
(6, 7, 'Sauna', 15, '2026-05-12 22:05:34'),
(7, 12, 'Crioterapia', 15, '2026-05-13 00:20:18'),
(8, 12, 'Sauna', 15, '2026-05-13 00:20:24'),
(9, 11, 'Crioterapia', 15, '2026-05-13 00:48:33'),
(10, 18, 'Sauna', 15, '2026-05-13 12:42:19'),
(11, 18, 'Crioterapia', 15, '2026-05-13 14:14:29'),
(12, 18, 'Sauna', 15, '2026-05-13 14:48:01'),
(13, 20, 'Sauna', 15, '2026-05-13 15:20:31'),
(14, 18, 'Crioterapia', 15, '2026-05-13 15:28:51'),
(15, 18, 'Sauna', 15, '2026-05-19 00:38:15'),
(16, 18, 'Sauna', 15, '2026-05-19 01:33:20'),
(17, 18, 'Sauna', 15, '2026-05-19 01:43:01'),
(18, 20, 'Sauna', 20, '2026-05-27 02:47:55'),
(19, 22, 'Sauna', 10, '2026-05-27 14:05:30'),
(20, 20, 'Crioterapia', 10, '2026-05-27 14:42:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(3, 'Entrenador'),
(2, 'Especialista'),
(5, 'Paciente'),
(4, 'Recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `tipo_servicio` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `tipo_servicio`, `precio`) VALUES
(1, 'Fisioterapia', 'Rehabilitación', 20.00),
(2, 'Acondicionamiento de Elite', 'Entrenamiento', 40.00),
(3, 'Nutrición', 'Alimentación', 30.00),
(4, 'Crioterapia y Sauna', 'Recuperación', 30.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos`
--

CREATE TABLE `telefonos` (
  `id_telefono` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `tipo` varchar(20) DEFAULT 'Móvil'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `telefonos`
--

INSERT INTO `telefonos` (`id_telefono`, `id_paciente`, `numero`, `tipo`) VALUES
(2, 4, '0414456132', 'Móvil'),
(3, 7, '04143650477', 'Móvil'),
(4, 8, '04164563678', 'Móvil'),
(6, 9, '235235234325234', 'Móvil'),
(7, 9, '235235234325234', 'Móvil'),
(9, 11, '04245690780', 'Móvil'),
(10, 12, '2121212112', 'Móvil'),
(14, 16, '721901', 'Móvil'),
(15, 17, '332232322', 'Móvil'),
(16, 18, '0424-5585708', 'Móvil'),
(17, 19, '0422528618', 'Móvil'),
(18, 20, '04245556789', 'Móvil'),
(19, 21, '0909090', 'Móvil'),
(20, 18, '04245690780', 'Móvil'),
(21, 22, '04245690780', 'Móvil'),
(22, 23, '213231312', 'Fijo'),
(23, 24, '31241414141414', 'Móvil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `nombre_completo`, `correo`, `password_hash`) VALUES
(4, 1, 'kelvin stanzione', 'kelvinstanzione@gmail.com', '$2y$10$KRY8J6fGB2kFIA64Z89.g.kaNU4A3ODn6FB.RgIEnGYObxfQl2tWu'),
(5, 5, 'diego espinoza', 'diego@gmail.com', '$2y$10$2GV0skNbrv4G168/BW8m2.rYEf4NdqY5Lj.ali6Nu7YrsYQbfrxhO'),
(11, 3, 'prueba', 'nuevoprueba@gmail.com', '$2y$10$cw1Inp5VbRWepkfyC5k7te/9iMSJdRDi88yMHQX2FBAcrvV9vio8i'),
(12, 5, 'diego', 'pacienteprueba@gmail.com', '$2y$10$glE8Mt9RElklvU5BJMuue.GZGHH9j5xrFCD5QYlEtfB5MVuwCknjO'),
(13, 5, 'ricardo', 'alavarado@gmail.com', '$2y$10$3zJPF8zwSAKlHC1RTtuuNe8JoEyOiS6w0ADG98Dad.AdmQdlntXQW'),
(17, 5, 'tonny', 'tonny@gmail.com', '$2y$10$jDnRQfJksX1WvR4vXjx7VeWAlg1pm5UdRE5XdQBc0LP2h0qI0MKF.'),
(18, 5, 'Marisol', 'marisol@gmail.com', '$2y$10$viBR1oh08nd6fOJwcJDX4eVJ2G/OhxWZ29IoEs6WXbWBQRMeK6Ply'),
(20, 3, 'Diego', 'die13.aeg@gmail.com', '$2y$10$3xJIflNOQ5lWxZ88.c5CkeNtma1nbrIbQJS1tp/vljm3/GIfnTDHO'),
(22, 3, 'Fabricio Matheus', 'f@gmail.com', '$2y$10$xSEGsC/uieDufXnC.ty4welCgQeNe1nq/uNsjEA1yPp2Eg1PFqhO6'),
(23, 1, 'Fabricio Matheus', 'fab@gmail.com', '$2y$10$TxBI5922VycPpxaoH3y5TuHKmU6NadCPoXlOr.Fk7n6NmmwBX9kFS'),
(25, 1, 'ihdsda', 'naskdh@gmail.com', '$2y$10$vX9WkamL4RfoMpe8VTLzBeToyZJH.h/.A4eHB2YQhKA6fdDkivWyu'),
(26, 1, 'Fabricio Matheus', 'fabriciomatheus@gmail.com', '$2y$10$/c99RPb7MIQxiATDgfBxtuRlqS7JjoWXelxPo77u/oM14WOQrIsD2'),
(28, 5, 'Fabricio Matheus', 'fabri@gmail.com', '$2y$10$/Ddj7dkwdQC2e7FhXEYMlu1hCGLlM33ekdEx6Wc.MnvThrqx4Z//W'),
(30, 1, 'carlos matheus', 'ash@gmail.com', '$2y$10$AohaNzylucQl4zLszbzW8O.eC0WVe6CTcsvDtEJj0Gyhz.3TaDE7G'),
(35, 5, 'Fabricio marcial', 'fabrr@gmail.com', '$2y$10$0Hyle.bjZewEJCdSqE/SVegOLV9cGglPHjOCwbATQsEXTS0oE4JxS'),
(39, 3, 'juan gallardo', 'onepie@gmail.com', '$2y$10$jUTRh7dN0l24JM.OipuzHuMb4g0m/7W2w8yA53.gcAZRonTEWMwg2'),
(40, 2, 'fabiana', 'fabiana@gmail.com', '$2y$10$qF9Qq7L/ReYzZrmhqOnhQ.gKFi.d2rAlClnNkUjuYql5V5WqmtkNO'),
(41, 4, 'maria gonzales', 'maria@gmail.com', '$2y$10$cMfp7A6rNmJZSLwu2EMDi.a972B7KeUZYTVbB9esSZE730uscFsnG'),
(42, 4, 'Fabricio Recepcionista', 'fabriciorecepcionista@gmail.com', '$2y$10$0k2T0Gv.W9lgt6ZEvOXLw.aUTjhTAYiO4.cXo3TuuTszvVKGV36AW'),
(43, 5, 'Fabricio Matheus', 'fabriciocliente@gmail.com', '$2y$10$x0Q6f44hdTtnyqAfZ0BSL.Arzu3DwZRf7lebn4aqS08EdJdzFEvl2'),
(44, 5, 'calamardo', 'calamardo@gmail.com', '$2y$10$mK3N4lvcKXvDa4cU07G0YuNOyr5LyDApAKt4I5M22azcc976I98ba'),
(48, 2, 'Fabricio Especialista', 'fabricioespecialista@gmail.com', '$2y$10$ioupThDVXg5I/3RtH.73AeslEPel/DIWNMzVLjg20hz08y1c0tguK'),
(49, 3, 'Fabricio Entrenador', 'fabricioentrenador@gmail.com', '$2y$10$nn.7RzIjMtx14NMPEoKL4.BOtmDu/5vbbCqgpP6CAn59Ux01bz09C'),
(50, 5, 'fabricio', '110@gmail.com', '$2y$10$rdtlWnxFBSkAt26L6fY8OeucWHvydnxD7prNJLOgFntE36bFSU2YO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id_cita`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `evolucion_medica`
--
ALTER TABLE `evolucion_medica`
  ADD PRIMARY KEY (`id_evolucion`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `kpi_progresos`
--
ALTER TABLE `kpi_progresos`
  ADD PRIMARY KEY (`id_kpi`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `planes_entrenamiento`
--
ALTER TABLE `planes_entrenamiento`
  ADD PRIMARY KEY (`id_plan`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `planes_nutricionales`
--
ALTER TABLE `planes_nutricionales`
  ADD PRIMARY KEY (`id_plan_nutricional`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `recuperacion_termica`
--
ALTER TABLE `recuperacion_termica`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `telefonos`
--
ALTER TABLE `telefonos`
  ADD PRIMARY KEY (`id_telefono`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `evolucion_medica`
--
ALTER TABLE `evolucion_medica`
  MODIFY `id_evolucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `kpi_progresos`
--
ALTER TABLE `kpi_progresos`
  MODIFY `id_kpi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `planes_entrenamiento`
--
ALTER TABLE `planes_entrenamiento`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `planes_nutricionales`
--
ALTER TABLE `planes_nutricionales`
  MODIFY `id_plan_nutricional` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `recuperacion_termica`
--
ALTER TABLE `recuperacion_termica`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `telefonos`
--
ALTER TABLE `telefonos`
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `evolucion_medica`
--
ALTER TABLE `evolucion_medica`
  ADD CONSTRAINT `evolucion_medica_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `evolucion_medica_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `kpi_progresos`
--
ALTER TABLE `kpi_progresos`
  ADD CONSTRAINT `kpi_progresos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `planes_entrenamiento`
--
ALTER TABLE `planes_entrenamiento`
  ADD CONSTRAINT `planes_entrenamiento_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `planes_entrenamiento_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `planes_nutricionales`
--
ALTER TABLE `planes_nutricionales`
  ADD CONSTRAINT `planes_nutricionales_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `planes_nutricionales_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recuperacion_termica`
--
ALTER TABLE `recuperacion_termica`
  ADD CONSTRAINT `recuperacion_termica_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `telefonos`
--
ALTER TABLE `telefonos`
  ADD CONSTRAINT `telefonos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
