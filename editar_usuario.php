<?php
include_once './verifica_login.php';
include_once './config/config.php';
include_once './classes/Usuario.php';

$usuarioObj = new Usuario($db);
$id = isset($_GET['id']) && usuarioEhAdmin() ? (int) $_GET['id'] : $_SESSION['usuario_id'];
if (!$id) {
    header('Location: dashboard.php');
    exit;
}
$usuario = $usuarioObj->lerPorId($id);
if (!$usuario || (!usuarioEhAdmin() && $usuario['id'] != $_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if (empty($nome) || empty($email)) {
        $erro = 'Nome e e-mail são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif ($usuarioObj->emailExiste($email, $id)) {
        $erro = 'Este e-mail já está em uso por outra conta.';
    } elseif (!empty($senha) && strlen($senha) < 6) {
        $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
    } elseif (!empty($senha) && $senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        $novaSenha = !empty($senha) ? $senha : null;
        if ($usuarioObj->atualizar($id, $nome, $email, $novaSenha)) {
            if ($id === $_SESSION['usuario_id']) {
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_email'] = $email;
            }
            header('Location: dashboard.php?msg=conta_atualizada');
            exit;
        } else {
            $erro = 'Erro ao atualizar conta. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Conta — ÉHistória</title>
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
            <a href="index.php" class="nav-link">🏠 Início</a>
            <a href="dashboard.php" class="nav-link">📊 Meu Painel</a>
            <a href="editar_usuario.php" class="nav-link">👤 Minha Conta</a>
            <a href="excluir_usuario.php" class="nav-link">🗑️ Excluir Conta</a>
            <a href="logout.php" class="nav-link">🚪 Sair</a>
        </nav>
    </aside>

    <main class="container-main">
        <div style="margin-bottom:1rem;">
            <a href="dashboard.php" style="color:var(--text-muted);font-size:0.85rem;">← Voltar ao painel</a>
        </div>

        <div class="form-container">
            <h2><?= usuarioEhAdmin() && $usuario['id'] !== $_SESSION['usuario_id'] ? '✏️ Editar usuário: ' . htmlspecialchars($usuario['nome']) : '👤 Editar Conta' ?></h2>

            <?php if ($erro): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="editar_usuario.php?id=<?= $usuario['id'] ?>">
                <div class="form-group">
                    <label for="nome">Nome completo *</label>
                    <input type="text" id="nome" name="nome"
                        value="<?= htmlspecialchars($_POST['nome'] ?? $usuario['nome']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? $usuario['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="senha">Nova senha (deixe em branco para manter a atual)</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label for="confirma_senha">Confirmar nova senha</label>
                    <input type="password" id="confirma_senha" name="confirma_senha" placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            </form>

            <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--border);text-align:center;">
                <a href="excluir_usuario.php?id=<?= $usuario['id'] ?>" style="color:var(--danger);font-size:0.85rem;">
                    <?= usuarioEhAdmin() && $usuario['id'] !== $_SESSION['usuario_id'] ? '🗑️ Excluir usuário' : '🗑️ Excluir minha conta' ?>
                </a>
            </div>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>