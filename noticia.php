<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

$noticiaObj = new Noticia($db);
$noticia = $noticiaObj->lerPorId($id);

if (!$noticia) {
    header('Location: index.php');
    exit;
}

$logado = isset($_SESSION['usuario_id']);
$nomeUsuario = $_SESSION['usuario_nome'] ?? '';
$eAutor = $logado && $_SESSION['usuario_id'] == $noticia['autor'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?> — ÉHistória</title>
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
                <a href="logout.php" class="nav-link">🚪 Sair</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">🔓 Login</a>
                <a href="cadastro.php" class="nav-link">📝 Cadastrar</a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="container-main">
        <div style="margin-bottom:1rem;">
            <a href="index.php" style="color:var(--text-muted);font-size:0.85rem;">← Voltar às notícias</a>
        </div>

        <article class="noticia-single">
            <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
            <div class="meta-single">
                <span class="autor-single">✍ <?= htmlspecialchars($noticia['nome_autor']) ?></span>
                <span>📅 <?= date('d/m/Y H:i', strtotime($noticia['data'])) ?></span>
            </div>

            <?php if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])): ?>
                <img src="imagens/<?= htmlspecialchars($noticia['imagem']) ?>"
                    alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="img-single">
            <?php endif; ?>

            <div class="corpo-noticia">
                <?= nl2br(htmlspecialchars($noticia['noticia'])) ?>
            </div>

            <?php if ($eAutor): ?>
                <div style="margin-top:2rem;padding-top:1rem;border-top:1px solid var(--border);display:flex;gap:1rem;">
                    <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                    <a href="excluir_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">🗑️ Excluir</a>
                </div>
            <?php endif; ?>
        </article>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias. Todos os direitos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>