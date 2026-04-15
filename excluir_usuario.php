<?php
include_once './verifica_login.php';
include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Noticia.php';

$usuarioObj = new Usuario($db);
$noticiaObj = new Noticia($db);
$targetId = isset($_GET['id']) && usuarioEhAdmin() ? (int) $_GET['id'] : $_SESSION['usuario_id'];
$erro = '';

if (!usuarioEhAdmin() && $targetId !== $_SESSION['usuario_id']) {
    header('Location: dashboard.php');
    exit;
}

$targetUsuario = $usuarioObj->lerPorId($targetId);
if (!$targetUsuario) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirma = $_POST['confirma'] ?? '';
    if ($confirma !== 'EXCLUIR') {
        $erro = 'Digite EXCLUIR para confirmar.';
    } else {
        // Excluir notícias do usuário primeiro
        $stmtNoticias = $noticiaObj->lerPorAutor($targetId);
        while ($n = $stmtNoticias->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($n['imagem']) && file_exists('imagens/' . $n['imagem'])) {
                unlink('imagens/' . $n['imagem']);
            }
        }
        // Excluir notícias via query direta
        $stmt = $db->prepare("DELETE FROM noticias WHERE autor = :id");
        $stmt->bindParam(':id', $targetId);
        $stmt->execute();

        // Excluir usuário
        $usuarioObj->excluir($targetId);

            // Logout completo
            session_start();
            session_destroy();
            // Remove o cookie da sessão
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            header('Location: index.php?msg=conta_excluida');
            exit;
       

        header('Location: dashboard.php?msg=usuario_excluido');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Conta — ÉHistória</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>

    <header>
        <div class="header-left">
            <button id="openMenu" class="menu-btn" aria-label="Abrir menu">☰</button>
            <div class="logo">É<span>História</span></div>
        </div>
        <div class="header-user">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></div>
        <div class="header-actions">
            <div id="localTemp" class="local-temp">--°C</div>
            <button id="themeToggle" class="theme-toggle" aria-label="Alternar tema"></button>
        </div>
    </header>

    <div id="overlay"></div>
    <aside id="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">📰 ÉHistória</span>
            <button id="closeMenu" class="close-btn">✕</button>
        </div>
        <nav class="sidebar-nav">
            <p class="nav-section-title">Conta</p>
            <a href="dashboard.php" class="nav-link">📊 Meu Painel</a>
            <a href="editar_usuario.php" class="nav-link">👤 Editar Conta</a>
            <a href="logout.php" class="nav-link">🚪 Sair</a>
        </nav>
    </aside>

    <main class="container-main">
        <div style="margin-bottom:1rem;">
            <a href="dashboard.php" style="color:var(--text-muted);font-size:0.85rem;">← Voltar ao painel</a>
        </div>

        <div class="form-container">
            <h2 style="color:var(--danger);">⚠️
                <?= usuarioEhAdmin() && $targetId !== $_SESSION['usuario_id'] ? 'Excluir usuário: ' . htmlspecialchars($targetUsuario['nome']) : 'Excluir Conta' ?>
            </h2>

            <div class="alert alert-danger">
                <strong>Atenção!</strong> Esta ação é irreversível.
                <?= usuarioEhAdmin() && $targetId !== $_SESSION['usuario_id'] ? 'O usuário e todas as suas notícias serão permanentemente excluídos.' : 'Sua conta e todas as suas notícias serão permanentemente excluídas.' ?>
            </div>

            <?php if ($erro): ?>
                <div class="alert alert-warning">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST"
                action="excluir_usuario.php<?= usuarioEhAdmin() && $targetId !== $_SESSION['usuario_id'] ? '?id=' . $targetId : '' ?>">
                <div class="form-group">
                    <label for="confirma">Para confirmar, digite <strong style="color:var(--accent);">EXCLUIR</strong>
                        abaixo:</label>
                    <input type="text" id="confirma" name="confirma" placeholder="EXCLUIR" required autocomplete="off">
                </div>
                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-danger">
                        <?= usuarioEhAdmin() && $targetId !== $_SESSION['usuario_id'] ? '🗑️ Excluir usuário' : '🗑️ Excluir minha conta' ?>
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary" style="flex:none;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>