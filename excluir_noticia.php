<?php
include_once './verifica_login.php';
include_once './config/config.php';
include_once './classes/Noticia.php';

$noticiaObj = new Noticia($db);
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$noticia = $noticiaObj->lerPorId($id);

if (!$noticia || ($noticia['autor'] != $_SESSION['usuario_id'] && !usuarioEhAdmin())) {
    header('Location: dashboard.php');
    exit;
}

// Remove a imagem do disco se existir
if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])) {
    unlink('imagens/' . $noticia['imagem']);
}

$noticiaObj->excluir($id);
header('Location: dashboard.php?msg=noticia_excluida');
exit;
