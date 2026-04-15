<?php
include_once './verifica_login.php';
include_once './config/config.php';
include_once './classes/Noticia.php';
include_once './classes/Usuario.php';

if (!usuarioEhAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$noticiaObj = new Noticia($db);
$usuarioObj = new Usuario($db);
$noticias = $noticiaObj->ler()->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $usuarioObj->listar()->fetchAll(PDO::FETCH_ASSOC);
$totalNoticias = count($noticias);
$totalUsuarios = count($usuarios);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador — ÉHistória</title>
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
            <span class="sidebar-title">📰 ÉHistória Admin</span>
            <button id="closeMenu" class="close-btn">✕</button>
        </div>
        <nav class="sidebar-nav">
            <p class="nav-section-title">Admin</p>
            <a href="admin_dashboard.php" class="nav-link">📊 Painel Admin</a>
            <a href="nova_noticia.php" class="nav-link">✏️ Nova Notícia</a>
            <a href="#admin-users" class="nav-link">👥 Gerenciar Usuários</a>
            <a href="editar_usuario.php" class="nav-link">👤 Meu Perfil</a>
            <a href="logout.php" class="nav-link">🚪 Sair</a>
        </nav>
    </aside>

    <main class="container-main">
        <?php if ($msg === 'noticia_criada'): ?>
            <div class="alert alert-success">✅ Notícia publicada com sucesso!</div>
        <?php elseif ($msg === 'noticia_editada'): ?>
            <div class="alert alert-success">✅ Notícia atualizada com sucesso!</div>
        <?php elseif ($msg === 'noticia_excluida'): ?>
            <div class="alert alert-success">✅ Notícia excluída com sucesso!</div>
        <?php elseif ($msg === 'conta_atualizada'): ?>
            <div class="alert alert-success">✅ Conta atualizada com sucesso!</div>
        <?php elseif ($msg === 'usuario_excluido'): ?>
            <div class="alert alert-success">✅ Usuário excluído com sucesso!</div>
        <?php endif; ?>

        <section class="admin-hero">
            <div class="admin-hero-content">
                <span class="admin-badge">Painel do Administrador</span>
                <h1>Administração total do portal</h1>
                <p>Este painel é exclusivo para administradores. Aqui você gerencia usuários, notícias e o conteúdo do site com controle total.</p>
                <div class="admin-actions">
                    <a href="nova_noticia.php" class="btn btn-primary">Publicar notícia</a>
                    <a href="#admin-users" class="btn btn-secondary">Ver usuários</a>
                </div>
            </div>
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <span>Usuários cadastrados</span>
                    <strong><?= $totalUsuarios ?></strong>
                </div>
                <div class="admin-stat-card">
                    <span>Total de notícias</span>
                    <strong><?= $totalNoticias ?></strong>
                </div>
            </div>
        </section>

        <div class="profile-header">
            <div class="profile-avatar"><?= mb_strtoupper(mb_substr($_SESSION['usuario_nome'], 0, 1)) ?></div>
            <div class="profile-info">
                <h2>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>! 👋</h2>
                <p><?= htmlspecialchars($_SESSION['usuario_email']) ?></p>
                <p style="margin-top:0.4rem;color:var(--text-muted);">Administrador</p>
                <p style="margin-top:0.4rem;">
                    <a href="editar_usuario.php" style="color:var(--accent);font-size:0.85rem;">✏️ Editar perfil</a>
                    &nbsp;|&nbsp;
                    <a href="logout.php" style="color:var(--danger);font-size:0.85rem;">🚪 Sair</a>
                </p>
            </div>
        </div>

        <div class="section-title" id="admin-users">
            <h2>Gerenciar Usuários</h2>
            <div class="line"></div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($u['tipo'])) ?></td>
                            <td>
                                <div class="actions-td">
                                    <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                                    <?php if ($u['id'] !== $_SESSION['usuario_id']): ?>
                                        <a href="excluir_usuario.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este usuário?')">🗑️ Excluir</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section-title" style="margin-top:2rem;">
            <h2>Gerenciar Notícias</h2>
            <div class="line"></div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($noticias as $n): ?>
                        <tr>
                            <td><?= $n['id'] ?></td>
                            <td><a href="noticia.php?id=<?= $n['id'] ?>" style="color:var(--text);"><?= htmlspecialchars(mb_substr($n['titulo'], 0, 60)) ?><?= mb_strlen($n['titulo']) > 60 ? '...' : '' ?></a></td>
                            <td><?= htmlspecialchars($n['nome_autor']) ?></td>
                            <td><?= date('d/m/Y', strtotime($n['data'])) ?></td>
                            <td>
                                <div class="actions-td">
                                    <a href="editar_noticia.php?id=<?= $n['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                                    <a href="excluir_noticia.php?id=<?= $n['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir esta notícia?')">🗑️ Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>

    <footer>
        <p>© <?= date('Y') ?> <span>ÉHistória</span> — Portal de Notícias.</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>
