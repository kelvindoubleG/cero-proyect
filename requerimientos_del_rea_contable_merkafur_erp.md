# Requerimientos del Área Contable - Merkafur ERP

Basado en el documento de arquitectura de la v2, a continuación se detallan los requerimientos funcionales, técnicos y reglas de negocio correspondientes al área contable del sistema.

## 1. Reglas de Negocio y Motor Contable (`MotorContable.php`)
*   **Principio de Partida Doble:** Todo registro contable (asiento) debe cumplir estrictamente con la ecuación patrimonial (Total Debe = Total Haber).
*   **Gestión de Periodos:** Solo se pueden registrar asientos si la fecha de la transacción pertenece a un **periodo contable abierto**. El sistema debe validar el año, mes, estado, fecha de apertura y fecha de cierre.
*   **Estructura de Asientos:**
    *   Los montos totales del asiento no se guardan en la cabecera, se derivan dinámicamente de los renglones mediante la vista `vw_totales_asiento`.
    *   Un asiento debe contener al menos una línea por el Debe y una por el Haber.
    *   Las líneas con monto cero (`0.00`) deben ser ignoradas.
    *   El motor debe guardar primero los registros del Debe y luego los del Haber.

## 2. Plan de Cuentas
*   **Estructura:** El plan de cuentas debe soportar hasta 4 niveles de jerarquía.
*   **Movimientos:** Solo las cuentas de **nivel 4** (`acepta_movimientos = 1`) pueden recibir registros contables directamente. Las cuentas de niveles superiores actúan como agrupadores.
*   **Cuentas Fijas (Hardcoded):** El sistema depende de IDs de cuentas específicas configuradas en `config/config.php` para la automatización (ej. Caja Principal, IVA, CxP, Cuentas de Inventario por departamento, etc.).
*   **Generación Automática:** Al registrar una nueva cuenta bancaria o una nueva moneda, el sistema debe crear automáticamente su subcuenta contable correspondiente bajo los grupos 1.01.02 (Bancos) o 1.01.01 (Cajas) a través de `ContabilidadModel::crearSubcuentaAutomatica()`.

## 3. Asientos Contables Automáticos (Integraciones)
El sistema debe generar comprobantes contables de forma automatizada a partir de las operaciones operativas:
*   **Compras:** Al registrar una factura, debe afectar Inventario por departamento, Crédito fiscal IVA, CxP proveedores (neto), Retención IVA y Retención ISLR.
*   **Cierre Z (Ventas):** Debe generar un asiento consolidado afectando Cajas (por moneda), POS por liquidar, Pago móvil (bancos), Faltantes/Sobrantes, Ventas por departamento, Débito fiscal IVA, IGTF, y aplicar el costo de ventas contra el inventario.
*   **Mermas de Inventario:** Debe cargar al Gasto por merma del departamento respectivo y abonar al Inventario al Costo Promedio Ponderado (CPP).
*   **Tesorería:**
    *   *Pago a proveedores:* Disminuye CxP contra Bancos.
    *   *Liquidación POS:* Entra el neto a Bancos, carga Comisión Bancaria y abona a la cuenta transitoria POS por liquidar.
    *   *Depósitos:* Traslado de fondos de Cajas en moneda origen a Bancos.

## 4. Asientos Manuales
*   **Interfaz del Contador:** Debe existir un formulario donde el contador pueda ingresar múltiples renglones (cuenta, tipo Debe/Haber, monto).
*   **Validación en vivo:** La interfaz debe validar visualmente y a nivel de backend que el asiento esté cuadrado antes de permitir guardarlo.

## 5. Reportes y Consultas Contables
*   **Libro Diario:** Visualización cronológica de todos los asientos y sus renglones.
*   **Libro Mayor (`vw_libro_mayor`):** 
    *   Debe permitir consolidar subcuentas (si se consulta un grupo).
    *   Debe mostrar saldo inicial, movimientos del periodo separados en debe/haber.
    *   Debe calcular el `efecto_saldo_bs` respetando la naturaleza de la cuenta (Deudora/Acreedora) y mostrar el saldo final.
*   **Estados Financieros:** Generación de balances.
*   **Conciliación de Inventario:** Herramienta que compare el valor monetario en el Kardex (costo) contra el saldo contable de la cuenta de inventario en el Mayor General.

## 6. Funciones exclusivas del rol "Contador"
*   Mantenimiento del plan de cuentas.
*   Creación de asientos manuales.
*   Apertura y cierre de periodos contables.
*   Conciliación bancaria.
*   Generación y consulta de estados financieros y libros contables (Diario/Mayor).

## 7. Requerimientos Pendientes (Roadmap - Tareas por hacer)
*   **Cierre de Ejercicio Anual:** Automatizar el proceso de final de año fiscal donde se cierren las cuentas de resultados (Ingresos 4, Costos 5, Gastos 6) contra la cuenta de Utilidades Retenidas (3.01.01.004).
*   **Diferencial Cambiario:** Crear el proceso de fin de mes para revaluar los saldos de cajas y bancos en divisas a la tasa de cierre, utilizando las cuentas de ganancias/pérdidas por diferencial cambiario (73 y 88).
*   **Declaración y Pago de Impuestos:** Implementar el egreso en Tesorería para "enterar" (pagar) las retenciones e IGTF al SENIAT (descargando las cuentas de pasivo 56, 57 y 58 contra bancos).