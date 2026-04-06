-- Tabla `pagos` con clave primaria e AUTO_INCREMENT (corrige dumps incompletos).
-- Si ya existe `pagos` sin PK, ejecute antes: DROP TABLE IF EXISTS `pagos`;
-- o repare manualmente con ALTER (según su entorno).

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `metodo` enum('tarjeta_credito','tarjeta_debito','paypal','transferencia','efectivo','cripto') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','completado','fallido','reembolsado','cancelado') NOT NULL DEFAULT 'pendiente',
  `descripcion` text DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pagos_estado` (`estado`),
  KEY `idx_pagos_metodo` (`metodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
