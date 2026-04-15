<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?msg=login_required');
    exit;
}

if (!function_exists('usuarioEhAdmin')) {
    function usuarioEhAdmin()
    {
        return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
    }
}
