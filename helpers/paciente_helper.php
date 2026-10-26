<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Paciente.php';

function setCurrentPaciente($id) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['paciente_id'] = (int) $id;
}

function clearCurrentPaciente() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    unset($_SESSION['paciente_id']);
    unset($_SESSION['paciente_nombre']);
}

function getCurrentPaciente($db) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['paciente_id'])) return null;
    $pacienteModel = new Paciente($db);
    $p = $pacienteModel->obtenerPorId((int) $_SESSION['paciente_id']);
    return $p ?: null;
}

function getCurrentPacienteId() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return isset($_SESSION['paciente_id']) ? (int) $_SESSION['paciente_id'] : null;
}

?>
