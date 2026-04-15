<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$usuario = new Usuario($db);
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($confirma)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } elseif ($usuario->emailExiste($email)) {
        $erro = 'Este e-mail já está cadastrado.';
    } else {
        if ($usuario->criar($nome, $email, $senha)) {
            $sucesso = 'Conta criada com sucesso! Faça login para continuar.';
        } else {
            $erro = 'Erro ao criar conta. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro — ÉHistória</title>
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
            <h2>📝 Criar Conta</h2>

            <?php if ($erro): ?>
                <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <?php if ($sucesso): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <form method="POST" action="cadastro.php">
                <div class="form-group">
                    <label for="nome">Nome completo *</label>
                    <input type="text" id="nome" name="nome" placeholder="Seu nome"
                        value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" placeholder="seu@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha * (mín. 6 caracteres)</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label for="confirma_senha">Confirmar senha *</label>
                    <input type="password" id="confirma_senha" name="confirma_senha" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary">Criar Conta</button>
            </form>

            <p class="form-footer">Já tem conta? <a href="login.php">Faça login</a></p>
        </div>
    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>