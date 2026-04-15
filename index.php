<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Noticia.php';

$noticiaObj = new Noticia($db);
$stmt = $noticiaObj->ler();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
$logado = isset($_SESSION['usuario_id']);
$nomeUsuario = $_SESSION['usuario_nome'] ?? '';
$msg = $_GET['msg'] ?? '';

// Força logout se a conta foi excluída
if ($msg === 'conta_excluida') {
    session_unset();
    session_destroy();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    $logado = false;
    $nomeUsuario = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ÉHistória - Portal de Notícias</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>

    <header>
        <div class="header-left">
            <button id="openMenu" class="menu-btn" aria-label="Abrir menu">☰</button>
            <div class="logo">É<span>História</span></div>
        </div>
        <div class="header-user">
            <?= $logado ? 'Olá, <strong>' . htmlspecialchars($nomeUsuario) . '</strong>' : 'Bem-vindo ao portal' ?>
        </div>
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
            <p class="nav-section-title">Principal</p>
            <a href="index.php" class="nav-link">🏠 Início</a>
            <?php if ($logado): ?>
                <a href="dashboard.php" class="nav-link">📊 Meu Painel</a>
                <a href="nova_noticia.php" class="nav-link">✏️ Publicar Notícia</a>
                <a href="editar_usuario.php" class="nav-link">👤 Minha Conta</a>
                <a href="logout.php" class="nav-link">🚪 Sair</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">🔓 Login</a>
                <a href="cadastro.php" class="nav-link">📝 Cadastrar</a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="container-main">
        <?php if ($msg === 'conta_excluida'): ?>
            <div class="alert alert-success">✅ Sua conta foi excluída com sucesso. Você foi desconectado.</div>
        <?php endif; ?>

        <div class="hero">
            <h2>📰 Portal de Notícias ÉHistória</h2>
            <p>Fique por dentro dos fatos mais importantes do dia</p>
            <?php if (!$logado): ?>
                <br>
                <a href="cadastro.php" class="btn btn-primary"
                    style="width:auto;display:inline-block;margin-top:1rem;">Criar conta e publicar</a>
            <?php else: ?>
                <br>
                <a href="nova_noticia.php" class="btn btn-primary"
                    style="width:auto;display:inline-block;margin-top:1rem;">+ Nova Notícia</a>
            <?php endif; ?>
        </div>

        <div class="section-title">
            <h2>Últimas Notícias</h2>
            <div class="line"></div>
        </div>

        <?php if (empty($noticias)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>Nenhuma notícia publicada ainda.</p>
                <?php if ($logado): ?>
                    <a href="nova_noticia.php" class="btn btn-primary" style="width:auto;display:inline-block;">Seja o primeiro
                        a publicar!</a>
                <?php else: ?>
                    <a href="cadastro.php" class="btn btn-primary" style="width:auto;display:inline-block;">Cadastre-se e
                        publique</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="noticias-grid">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="noticia-card">
                        <?php if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])): ?>
                            <img src="imagens/<?= htmlspecialchars($noticia['imagem']) ?>"
                                alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="card-img">
                        <?php else: ?>
                            <div class="card-img-placeholder">📰</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-titulo"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                            <p class="card-resumo">
                                <?= htmlspecialchars(mb_substr(strip_tags($noticia['noticia']), 0, 150)) ?>...</p>
                            <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn-ler-mais">Ler mais →</a>
                            <div class="card-meta">
                                <span class="autor">✍ <?= htmlspecialchars($noticia['nome_autor']) ?></span>
                                <span>🕐 <?= date('d/m/Y', strtotime($noticia['data'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias. Todos os direitos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>