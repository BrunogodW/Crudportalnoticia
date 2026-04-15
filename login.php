<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';

if (isset($_SESSION['usuario_id'])) {
    if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$usuario = new Usuario($db);
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $user = $usuario->lerPorEmail($email);
        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            $_SESSION['usuario_email'] = $user['email'];
            $_SESSION['usuario_tipo'] = $user['tipo'] ?? 'usuario';
            if ($_SESSION['usuario_tipo'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}

$msgParam = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ÉHistória</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>

    <header>
        <div class="header-left">
            <button id="openMenu" class="menu-btn" aria-label="Abrir menu">☰</button>
            <div class="logo">É<span>História</span></div>
        </div>
        <div class="header-user">Bem-vindo ao portal</div>
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
            <p class="nav-section-title">Acesso</p>
            <a href="index.php" class="nav-link">🏠 Início</a>
            <a href="login.php" class="nav-link">🔓 Login</a>
            <a href="cadastro.php" class="nav-link">📝 Cadastrar</a>
        </nav>
    </aside>

    <main class="container-main">
        <div class="form-container">
            <h2>🔓 Entrar</h2>

            <?php if ($msgParam === 'login_required'): ?>
                <div class="alert alert-warning">⚠️ Você precisa estar logado para acessar esta página.</div>
            <?php endif; ?>
            <?php if ($msgParam === 'logout'): ?>
                <div class="alert alert-success">✅ Você saiu com sucesso.</div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="seu@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>

            <p class="form-footer">Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>