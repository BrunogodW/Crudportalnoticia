<?php
include_once './verifica_login.php';
include_once './config/config.php';
include_once './classes/Noticia.php';

$noticiaObj = new Noticia($db);
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $noticia = trim($_POST['noticia'] ?? '');
    $autor = $_SESSION['usuario_id'];
    $imagem = null;

    if (empty($titulo) || empty($noticia)) {
        $erro = 'Título e conteúdo são obrigatórios.';
    } else {
        // Upload de imagem
        if (!empty($_FILES['imagem']['name'])) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $permitidos)) {
                $erro = 'Formato de imagem inválido. Use JPG, PNG, GIF ou WEBP.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem muito grande. Máximo 5MB.';
            } else {
                $nomeArquivo = uniqid('noticia_') . '.' . $ext;
                if (!is_dir('imagens'))
                    mkdir('imagens', 0755, true);
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], 'imagens/' . $nomeArquivo)) {
                    $imagem = $nomeArquivo;
                } else {
                    $erro = 'Erro ao fazer upload da imagem.';
                }
            }
        }

        if (empty($erro)) {
            if ($noticiaObj->criar($titulo, $noticia, $autor, $imagem)) {
                header('Location: dashboard.php?msg=noticia_criada');
                exit;
            } else {
                $erro = 'Erro ao publicar notícia. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Notícia — ÉHistória</title>
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
            <p class="nav-section-title">Painel</p>
            <a href="index.php" class="nav-link">🏠 Início</a>
            <a href="dashboard.php" class="nav-link">📊 Meu Painel</a>
            <a href="nova_noticia.php" class="nav-link">✏️ Nova Notícia</a>
            <a href="logout.php" class="nav-link">🚪 Sair</a>
        </nav>
    </aside>

    <main class="container-main">
        <div style="margin-bottom:1rem;">
            <a href="dashboard.php" style="color:var(--text-muted);font-size:0.85rem;">← Voltar ao painel</a>
        </div>

        <div class="form-container wide">
            <h2>✏️ Nova Notícia</h2>

            <?php if ($erro): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="nova_noticia.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Título da notícia"
                        value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="noticia">Conteúdo *</label>
                    <textarea id="noticia" name="noticia" placeholder="Escreva o conteúdo completo da notícia..."
                        style="min-height:250px;" required><?= htmlspecialchars($_POST['noticia'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="imagem">Imagem (opcional — JPG, PNG, GIF, WEBP, máx. 5MB)</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*" style="padding:0.5rem;">
                </div>
                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-primary">📤 Publicar Notícia</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="flex:none;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias. Todos os direitos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>