-- =============================================================================
-- MIGRACIÓN v2 — Proyecto CERO (PHP / MariaDB 10.4)
-- Archivo  : database/migration_v2.sql
-- Fecha    : 2026-10-26
-- Instrucciones: mysql -u root cero_db < database/migration_v2.sql
--
-- Este script es idempotente en la mayoría de sus secciones.
-- Las columnas nuevas (ALTER TABLE ... ADD COLUMN) producirán un error
-- "Duplicate column name" si ya existen; en MariaDB 10.4 ese es el
-- comportamiento esperado cuando se ejecuta el script por segunda vez.
-- Simplemente ignora esos errores o usa la opción -f (force) de mysql:
--   mysql -u root -f cero_db < database/migration_v2.sql
-- =============================================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- =============================================================================
-- SECCIÓN 1. RENOMBRAR ROL "Paciente" → "Cliente" (id = 5)
-- =============================================================================
UPDATE `roles`
SET    `nombre` = 'Cliente'
WHERE  `id` = 5
  AND  `nombre` = 'Paciente';   -- guarda idempotencia: solo actualiza si aún dice "Paciente"

-- =============================================================================
-- SECCIÓN 2. COLUMNAS NUEVAS EN `citas`
--    2a. nota_interna — nota interna del personal
--    2b. correo       — correo del solicitante en citas públicas
--
--    MariaDB 10.4 no soporta IF NOT EXISTS en ALTER TABLE;
--    el error "Duplicate column" es inofensivo en ejecuciones posteriores.
-- =============================================================================
ALTER TABLE `citas`
    ADD COLUMN `nota_interna` TEXT DEFAULT NULL
        COMMENT 'Nota interna del personal; no visible al cliente';

ALTER TABLE `citas`
    ADD COLUMN `correo` VARCHAR(150) DEFAULT NULL
        COMMENT 'Correo del solicitante para citas públicas (sin cuenta)';

-- =============================================================================
-- SECCIÓN 3. COLUMNA `moneda` EN `servicios`
-- =============================================================================
ALTER TABLE `servicios`
    ADD COLUMN `moneda` VARCHAR(3) NOT NULL DEFAULT 'USD'
        COMMENT 'Moneda del precio: USD, VES, EUR, etc.';

-- =============================================================================
-- SECCIÓN 4. TABLA `tipo_cambio_bcv`
--    Caché de la tasa oficial BCV (Bs. / USD)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `tipo_cambio_bcv` (
    `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `fecha`        DATE             NOT NULL              COMMENT 'Fecha de la tasa',
    `tasa`         DECIMAL(18, 4)   NOT NULL              COMMENT 'Bs. por 1 USD',
    `fuente`       VARCHAR(100)     NOT NULL DEFAULT 'BCV' COMMENT 'Fuente de la tasa',
    `creado_en`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tc_fecha` (`fecha`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Caché diaria de la tasa BCV (Bs./USD)';

-- =============================================================================
-- SECCIÓN 5. MOTOR CONTABLE — Plan de Cuentas (4 niveles)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `plan_cuentas` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `codigo`        VARCHAR(20)      NOT NULL              COMMENT 'Ej: 1.1.01.001',
    `nombre`        VARCHAR(200)     NOT NULL,
    `tipo`          ENUM('ACTIVO','PASIVO','PATRIMONIO','INGRESO','GASTO','COSTO')
                                     NOT NULL,
    `naturaleza`    ENUM('DEUDORA','ACREEDORA') NOT NULL,
    `nivel`         TINYINT UNSIGNED NOT NULL DEFAULT 1    COMMENT '1=grupo,2=subgrupo,3=cuenta,4=subcuenta',
    `padre_id`      INT UNSIGNED     DEFAULT NULL,
    `acepta_movim`  TINYINT(1)       NOT NULL DEFAULT 0    COMMENT '1 = puede recibir asientos directos',
    `activa`        TINYINT(1)       NOT NULL DEFAULT 1,
    `creado_en`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pc_codigo` (`codigo`),
    KEY `idx_pc_padre` (`padre_id`),
    CONSTRAINT `fk_pc_padre`
        FOREIGN KEY (`padre_id`) REFERENCES `plan_cuentas` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Plan de cuentas contable de 4 niveles';

-- =============================================================================
-- SECCIÓN 6. MOTOR CONTABLE — Períodos Contables
-- =============================================================================
CREATE TABLE IF NOT EXISTS `periodos_contables` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nombre`        VARCHAR(100)    NOT NULL              COMMENT 'Ej: Octubre 2026',
    `fecha_inicio`  DATE            NOT NULL,
    `fecha_fin`     DATE            NOT NULL,
    `estado`        ENUM('ABIERTO','CERRADO') NOT NULL DEFAULT 'ABIERTO',
    `creado_en`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cerrado_en`    DATETIME        DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_per_estado` (`estado`),
    CONSTRAINT `chk_per_fechas` CHECK (`fecha_fin` >= `fecha_inicio`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Períodos contables (meses o ejercicios fiscales)';

-- =============================================================================
-- SECCIÓN 7. MOTOR CONTABLE — Asientos Contables (cabecera)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `asientos_contables` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `periodo_id`    INT UNSIGNED    NOT NULL,
    `numero`        INT UNSIGNED    NOT NULL              COMMENT 'Correlativo dentro del período',
    `fecha`         DATE            NOT NULL,
    `descripcion`   VARCHAR(500)    NOT NULL,
    `referencia`    VARCHAR(100)    DEFAULT NULL          COMMENT 'N° factura, recibo, etc.',
    `tipo`          ENUM('MANUAL','AUTO') NOT NULL DEFAULT 'MANUAL',
    `estado`        ENUM('BORRADOR','APROBADO','ANULADO') NOT NULL DEFAULT 'BORRADOR',
    `usuario_id`    INT UNSIGNED    DEFAULT NULL,
    `creado_en`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `aprobado_en`   DATETIME        DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_asiento_num` (`periodo_id`, `numero`),
    KEY `idx_asi_fecha`   (`fecha`),
    KEY `idx_asi_estado`  (`estado`),
    CONSTRAINT `fk_asi_periodo`
        FOREIGN KEY (`periodo_id`) REFERENCES `periodos_contables` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Cabecera de asientos contables';

-- =============================================================================
-- SECCIÓN 8. MOTOR CONTABLE — Renglones de Asiento (débitos / créditos)
-- =============================================================================
CREATE TABLE IF NOT EXISTS `renglones_asiento` (
    `id`            INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `asiento_id`    INT UNSIGNED      NOT NULL,
    `cuenta_id`     INT UNSIGNED      NOT NULL,
    `descripcion`   VARCHAR(300)      DEFAULT NULL,
    `debe`          DECIMAL(18, 2)    NOT NULL DEFAULT 0.00,
    `haber`         DECIMAL(18, 2)    NOT NULL DEFAULT 0.00,
    `moneda`        VARCHAR(3)        NOT NULL DEFAULT 'USD',
    `tasa_bcv`      DECIMAL(18, 4)    DEFAULT NULL          COMMENT 'Tasa BCV al momento del registro',
    `orden`         SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_rng_asiento` (`asiento_id`),
    KEY `idx_rng_cuenta`  (`cuenta_id`),
    CONSTRAINT `fk_rng_asiento`
        FOREIGN KEY (`asiento_id`) REFERENCES `asientos_contables` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rng_cuenta`
        FOREIGN KEY (`cuenta_id`) REFERENCES `plan_cuentas` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_rng_importe` CHECK (`debe` >= 0 AND `haber` >= 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Líneas de débito/crédito de cada asiento contable';

-- =============================================================================
-- SECCIÓN 9. TABLA `email_verificacion` — OTP para registro de usuarios
-- =============================================================================
CREATE TABLE IF NOT EXISTS `email_verificacion` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `correo`        VARCHAR(150)     NOT NULL,
    `token`         VARCHAR(64)      NOT NULL              COMMENT 'Hash SHA-256 del OTP',
    `otp_plain`     VARCHAR(8)       DEFAULT NULL          COMMENT 'OTP en claro: NULL en producción',
    `usado`         TINYINT(1)       NOT NULL DEFAULT 0,
    `intentos`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `expira_en`     DATETIME         NOT NULL,
    `creado_en`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ev_correo` (`correo`),
    KEY `idx_ev_token`  (`token`),
    KEY `idx_ev_expira` (`expira_en`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='OTP de verificación de correo en el registro de usuarios';

-- =============================================================================
-- SECCIÓN 10. VISTA `vw_totales_asiento`
--    Muestra totales debe/haber por asiento y si está cuadrado.
-- =============================================================================
CREATE OR REPLACE VIEW `vw_totales_asiento` AS
SELECT
    a.`id`                                        AS `asiento_id`,
    a.`numero`,
    a.`fecha`,
    a.`descripcion`,
    a.`estado`,
    p.`nombre`                                    AS `periodo`,
    COALESCE(SUM(r.`debe`),  0)                   AS `total_debe`,
    COALESCE(SUM(r.`haber`), 0)                   AS `total_haber`,
    COALESCE(SUM(r.`debe`),  0)
        - COALESCE(SUM(r.`haber`), 0)             AS `diferencia`,
    CASE
        WHEN ABS(COALESCE(SUM(r.`debe`), 0)
               - COALESCE(SUM(r.`haber`), 0)) < 0.005
        THEN 'CUADRADO'
        ELSE 'DESCUADRADO'
    END                                           AS `cuadre`
FROM  `asientos_contables`    a
JOIN  `periodos_contables`    p ON p.`id` = a.`periodo_id`
LEFT JOIN `renglones_asiento` r ON r.`asiento_id` = a.`id`
GROUP BY
    a.`id`, a.`numero`, a.`fecha`,
    a.`descripcion`, a.`estado`, p.`nombre`;

-- =============================================================================
-- SECCIÓN 11. PLAN DE CUENTAS — Clínica venezolana (≥ 20 cuentas, 4 niveles)
--    Se usa INSERT IGNORE para idempotencia (falla silenciosa en duplicados).
-- =============================================================================

-- ── NIVEL 1: GRUPOS PRINCIPALES ─────────────────────────────────────────────
INSERT IGNORE INTO `plan_cuentas`
    (`codigo`, `nombre`, `tipo`, `naturaleza`, `nivel`, `padre_id`, `acepta_movim`)
VALUES
    ('1',  'ACTIVOS',     'ACTIVO',     'DEUDORA',   1, NULL, 0),
    ('2',  'PASIVOS',     'PASIVO',     'ACREEDORA', 1, NULL, 0),
    ('3',  'PATRIMONIO',  'PATRIMONIO', 'ACREEDORA', 1, NULL, 0),
    ('4',  'INGRESOS',    'INGRESO',    'ACREEDORA', 1, NULL, 0),
    ('5',  'GASTOS',      'GASTO',      'DEUDORA',   1, NULL, 0),
    ('6',  'COSTOS',      'COSTO',      'DEUDORA',   1, NULL, 0);

-- ── NIVEL 2: SUBGRUPOS ───────────────────────────────────────────────────────
INSERT IGNORE INTO `plan_cuentas`
    (`codigo`, `nombre`, `tipo`, `naturaleza`, `nivel`, `padre_id`, `acepta_movim`)
VALUES
    ('1.1', 'Activo Circulante',              'ACTIVO',     'DEUDORA',   2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1') _t), 0),
    ('1.2', 'Activo No Circulante',           'ACTIVO',     'DEUDORA',   2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1') _t), 0),
    ('2.1', 'Pasivo Circulante',              'PASIVO',     'ACREEDORA', 2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='2') _t), 0),
    ('2.2', 'Pasivo No Circulante',           'PASIVO',     'ACREEDORA', 2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='2') _t), 0),
    ('4.1', 'Ingresos por Servicios Médicos', 'INGRESO',    'ACREEDORA', 2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4') _t), 0),
    ('4.2', 'Otros Ingresos',                 'INGRESO',    'ACREEDORA', 2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4') _t), 0),
    ('5.1', 'Gastos de Personal',             'GASTO',      'DEUDORA',   2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5') _t), 0),
    ('5.2', 'Gastos Operativos',              'GASTO',      'DEUDORA',   2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5') _t), 0),
    ('5.3', 'Gastos Administrativos',         'GASTO',      'DEUDORA',   2, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5') _t), 0);

-- ── NIVEL 3: CUENTAS ─────────────────────────────────────────────────────────
INSERT IGNORE INTO `plan_cuentas`
    (`codigo`, `nombre`, `tipo`, `naturaleza`, `nivel`, `padre_id`, `acepta_movim`)
VALUES
    ('1.1.01', 'Caja',                           'ACTIVO',  'DEUDORA',   3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1') _t), 0),
    ('1.1.02', 'Bancos',                          'ACTIVO',  'DEUDORA',   3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1') _t), 0),
    ('1.1.03', 'Cuentas por Cobrar Clientes',     'ACTIVO',  'DEUDORA',   3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1') _t), 0),
    ('1.1.04', 'Inventario de Medicamentos',      'ACTIVO',  'DEUDORA',   3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1') _t), 0),
    ('1.2.01', 'Equipos Médicos',                 'ACTIVO',  'DEUDORA',   3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.2') _t), 0),
    ('1.2.02', 'Depreciación Acumulada Equipos',  'ACTIVO',  'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.2') _t), 0),
    ('2.1.01', 'Cuentas por Pagar Proveedores',   'PASIVO',  'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='2.1') _t), 0),
    ('2.1.02', 'ISLR Retenido por Pagar',         'PASIVO',  'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='2.1') _t), 0),
    ('2.1.03', 'IVA por Pagar',                   'PASIVO',  'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='2.1') _t), 0),
    ('4.1.01', 'Consultas Médicas',               'INGRESO', 'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4.1') _t), 0),
    ('4.1.02', 'Procedimientos y Cirugías',        'INGRESO', 'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4.1') _t), 0),
    ('4.1.03', 'Exámenes de Laboratorio',          'INGRESO', 'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4.1') _t), 0),
    ('4.2.01', 'Intereses Ganados',                'INGRESO', 'ACREEDORA', 3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4.2') _t), 0),
    ('5.1.01', 'Sueldos y Salarios',               'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.1') _t), 0),
    ('5.1.02', 'Prestaciones Sociales',            'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.1') _t), 0),
    ('5.2.01', 'Alquiler de Local',                'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.2') _t), 0),
    ('5.2.02', 'Servicios Públicos (Agua/Luz)',    'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.2') _t), 0),
    ('5.2.03', 'Mantenimiento de Equipos',         'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.2') _t), 0),
    ('5.3.01', 'Papelería y Útiles de Oficina',   'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.3') _t), 0),
    ('5.3.02', 'Publicidad y Mercadeo',            'GASTO',   'DEUDORA',  3, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.3') _t), 0);

-- ── NIVEL 4: SUBCUENTAS ───────────────────────────────────────────────────────
INSERT IGNORE INTO `plan_cuentas`
    (`codigo`, `nombre`, `tipo`, `naturaleza`, `nivel`, `padre_id`, `acepta_movim`)
VALUES
    ('1.1.01.001', 'Caja Venezolana (Bs.)',          'ACTIVO',  'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1.01') _t), 1),
    ('1.1.01.002', 'Caja Divisas (USD)',              'ACTIVO',  'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1.01') _t), 1),
    ('1.1.02.001', 'Banco Venezuela Cta. Corriente',  'ACTIVO',  'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1.02') _t), 1),
    ('1.1.02.002', 'Banco Bicentenario Cta. Corriente','ACTIVO', 'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1.02') _t), 1),
    ('1.1.03.001', 'Cuentas por Cobrar HCM',          'ACTIVO',  'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1.03') _t), 1),
    ('1.1.03.002', 'Cuentas por Cobrar Particulares', 'ACTIVO',  'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='1.1.03') _t), 1),
    ('4.1.01.001', 'Consultas Médicos Generales',      'INGRESO', 'ACREEDORA', 4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4.1.01') _t), 1),
    ('4.1.01.002', 'Consultas Especialistas',          'INGRESO', 'ACREEDORA', 4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='4.1.01') _t), 1),
    ('5.1.01.001', 'Sueldos Médicos',                  'GASTO',   'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.1.01') _t), 1),
    ('5.1.01.002', 'Sueldos Personal Administrativo',  'GASTO',   'DEUDORA',   4, (SELECT id FROM (SELECT id FROM plan_cuentas WHERE codigo='5.1.01') _t), 1);

-- =============================================================================
-- SECCIÓN 12. PERÍODO CONTABLE — Octubre 2026 (mes actual)
--    La subconsulta WHERE NOT EXISTS garantiza idempotencia.
-- =============================================================================
INSERT INTO `periodos_contables` (`nombre`, `fecha_inicio`, `fecha_fin`, `estado`)
SELECT 'Octubre 2026', '2026-10-01', '2026-10-31', 'ABIERTO'
WHERE NOT EXISTS (
    SELECT 1 FROM `periodos_contables`
    WHERE `fecha_inicio` = '2026-10-01'
      AND `fecha_fin`    = '2026-10-31'
);

-- =============================================================================
-- FIN DE MIGRACIÓN v2
-- =============================================================================

SET foreign_key_checks = 1;
