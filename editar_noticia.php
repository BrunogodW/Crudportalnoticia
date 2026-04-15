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

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    $imagem = null;

    if (empty($titulo) || empty($conteudo)) {
        $erro = 'Título e conteúdo são obrigatórios.';
    } else {
        // Upload de nova imagem (se enviada)
        if (!empty($_FILES['imagem']['name'])) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $permitidos)) {
                $erro = 'Formato de imagem inválido.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem muito grande. Máximo 5MB.';
            } else {
                $nomeArquivo = uniqid('noticia_') . '.' . $ext;
                if (!is_dir('imagens'))
                    mkdir('imagens', 0755, true);
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], 'imagens/' . $nomeArquivo)) {
                    // Remove imagem antiga se existir
                    if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])) {
                        unlink('imagens/' . $noticia['imagem']);
                    }
                    $imagem = $nomeArquivo;
                } else {
                    $erro = 'Erro ao fazer upload da imagem.';
                }
            }
        }

        if (empty($erro)) {
            if ($noticiaObj->atualizar($id, $titulo, $conteudo, $imagem)) {
                header('Location: dashboard.php?msg=noticia_editada');
                exit;
            } else {
                $erro = 'Erro ao atualizar notícia.';
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
    <title>Editar Notícia — ÉHistória</title>
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
            <a href="logout.php" class="nav-link">🚪 Sair</a>
        </nav>
    </aside>

    <main class="container-main">
        <div style="margin-bottom:1rem;">
            <a href="dashboard.php" style="color:var(--text-muted);font-size:0.85rem;">← Voltar ao painel</a>
        </div>

        <div class="form-container wide">
            <h2>✏️ Editar Notícia</h2>

            <?php if ($erro): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="editar_noticia.php?id=<?= $id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo"
                        value="<?= htmlspecialchars($_POST['titulo'] ?? $noticia['titulo']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="noticia">Conteúdo *</label>
                    <textarea id="noticia" name="noticia" style="min-height:250px;"
                        required><?= htmlspecialchars($_POST['noticia'] ?? $noticia['noticia']) ?></textarea>
                </div>

                <?php if (!empty($noticia['imagem']) && file_exists('imagens/' . $noticia['imagem'])): ?>
                    <div class="form-group">
                        <label>Imagem atual</label>
                        <img src="imagens/<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem atual"
                            style="max-height:150px;border-radius:6px;display:block;margin-top:0.5rem;">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="imagem">Nova imagem (opcional — deixe em branco para manter a atual)</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*" style="padding:0.5rem;">
                </div>
                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
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