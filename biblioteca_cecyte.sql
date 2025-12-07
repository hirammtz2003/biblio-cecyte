-- Adminer 5.4.1 MySQL 8.0.43 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `biblioteca_cecyte` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `biblioteca_cecyte`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `libro_favorito` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned NOT NULL,
  `libro_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libro_favorito_usuario_id_libro_id_unique` (`usuario_id`,`libro_id`),
  KEY `libro_favorito_libro_id_foreign` (`libro_id`),
  CONSTRAINT `libro_favorito_libro_id_foreign` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE,
  CONSTRAINT `libro_favorito_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `libro_favorito` (`id`, `usuario_id`, `libro_id`, `created_at`, `updated_at`) VALUES
(10,	2,	4,	'2025-12-01 19:24:15',	'2025-12-01 19:24:15'),
(11,	1,	4,	'2025-12-01 19:29:44',	'2025-12-01 19:29:44');

CREATE TABLE `libros` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio_publicacion` year NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrera` enum('Soporte y Mantenimiento de Equipo de Cómputo','Enfermería General','Ventas','Diseño Gráfico Digital') COLLATE utf8mb4_unicode_ci NOT NULL,
  `semestre` enum('1°','2°','3°','4°','5°','6°') COLLATE utf8mb4_unicode_ci NOT NULL,
  `materia` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruta_primera_pagina` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primera_pagina_generada` tinyint(1) NOT NULL DEFAULT '0',
  `hash_archivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamanio` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `descargable` tinyint(1) NOT NULL DEFAULT '0',
  `veces_descargado` int NOT NULL DEFAULT '0',
  `veces_visto` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libros_hash_archivo_unique` (`hash_archivo`),
  UNIQUE KEY `libros_isbn_unique` (`isbn`),
  KEY `libros_id_usuario_foreign` (`id_usuario`),
  KEY `libros_carrera_semestre_index` (`carrera`,`semestre`),
  KEY `libros_titulo_index` (`titulo`),
  KEY `libros_autor_index` (`autor`),
  KEY `libros_materia_index` (`materia`),
  CONSTRAINT `libros_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `libros` (`id`, `titulo`, `autor`, `anio_publicacion`, `descripcion`, `isbn`, `carrera`, `semestre`, `materia`, `nombre_archivo`, `ruta_archivo`, `ruta_primera_pagina`, `primera_pagina_generada`, `hash_archivo`, `tamanio`, `id_usuario`, `activo`, `descargable`, `veces_descargado`, `veces_visto`, `created_at`, `updated_at`) VALUES
(4,	'Cómo funciona TeamViewer basico',	'Hiram Isay Martínez Saucedo',	'2020',	NULL,	NULL,	'Soporte y Mantenimiento de Equipo de Cómputo',	'6°',	'Modulo 3',	'Cómo funciona TeamViewer basico.pdf',	'libros/soporte-y-mantenimiento-de-equipo-de-computo/6/a4bf64558bac90a0ab79a908bbad3a41654d6081d2eb39cd539adf3ca02bc35b.pdf',	NULL,	0,	'a4bf64558bac90a0ab79a908bbad3a41654d6081d2eb39cd539adf3ca02bc35b',	38009,	2,	1,	0,	0,	23,	'2025-11-07 01:43:18',	'2025-12-01 19:29:37'),
(6,	'Pasos para activar el Escritorio Remoto',	'Hiram Isay Martínez Saucedo',	'2020',	NULL,	NULL,	'Soporte y Mantenimiento de Equipo de Cómputo',	'3°',	'Modulo 2',	'Pasos para activar el Escritorio Remoto.pdf',	'libros/soporte-y-mantenimiento-de-equipo-de-computo/3/eead7064d6ef8cf565e6f44b2d4ad1fe065341c23bbb82ff0cf6dc72d54b9eca.pdf',	NULL,	0,	'eead7064d6ef8cf565e6f44b2d4ad1fe065341c23bbb82ff0cf6dc72d54b9eca',	240929,	2,	1,	1,	0,	2,	'2025-12-01 19:25:52',	'2025-12-01 19:31:42');

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'0001_01_01_000000_create_users_table',	1),
(2,	'0001_01_01_000001_create_cache_table',	1),
(3,	'0001_01_01_000002_create_jobs_table',	1),
(4,	'2025_10_18_225555_create_usuarios_table',	1),
(5,	'2025_10_26_191926_create_libros_table',	2),
(6,	'2025_11_03_021530_add_descargable_to_libros_table',	3),
(7,	'2025_11_07_235240_add_primera_pagina_to_libros_table',	4),
(8,	'2025_11_08_180516_create_libro_favorito_table',	5);

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carrera` enum('Soporte y Mantenimiento de Equipo de Cómputo','Enfermería General','Ventas','Diseño Gráfico Digital') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_control` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_usuario` enum('Administrador','Docente','Alumno') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Alumno',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_numero_control_unique` (`numero_control`),
  UNIQUE KEY `usuarios_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `carrera`, `numero_control`, `email`, `contrasena`, `tipo_usuario`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Hiram Isay',	'Martínez',	'Saucedo',	'Soporte y Mantenimiento de Equipo de Cómputo',	'18432070030108',	'hiram_saucedo@outlook.com',	'$2y$12$oUBMQ7RP27tOtd4UCioH3eEzW7BVgZdAg9m5NlLyMYTAETr0Jgwt.',	'Alumno',	NULL,	'2025-10-19 10:30:32',	'2025-11-21 05:44:24'),
(2,	'Gricelda',	'Saucedo',	'Cordero',	'Soporte y Mantenimiento de Equipo de Cómputo',	'1151',	'gricelda.saucedo@cecytezac.edu.mx',	'$2y$12$NXnAgGuWSA4vlReDwee6Aev30AAyUA5sctDqfbuA9FilXNMHTicgS',	'Docente',	NULL,	'2025-10-19 10:37:39',	'2025-11-03 09:50:15'),
(3,	'Francisco Javier',	'Ceniceros',	'Martínez',	'Soporte y Mantenimiento de Equipo de Cómputo',	'0000',	'francisco.ceniceros.ma@cecytezac.edu.mx',	'$2y$12$d1h1sOrV2qTBAkKJB90zbO8sfIClj2oQMfKt3CzlRmRf3Cgfd878i',	'Administrador',	NULL,	'2025-10-19 10:39:48',	'2025-11-03 09:49:10');

-- 2025-12-07 07:51:56 UTC
