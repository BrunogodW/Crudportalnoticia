<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Noticia.php';


$usuario = new Usuario($db);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    isset($_POST['nome'], $_POST['sexo'], $_POST['fone'], $_POST['email'], $_POST['senha']) or die('Preencha todos os campos!');
    $nome = $_POST['nome'];
    $sexo = $_POST['sexo'];
    $fone = $_POST['fone'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>

<body>
    <header>
        
    <h1 class="logo">ÉHistoria</h1>
        
            
        <button id="openMenu">☰</button>
        
        

        <div class="container">
            <img src="Img/perfil2.avif" alt="Profile Image" class="Img1">

            <a href="login.php" class="LoginRegistro">Login</a>
        
        </div>

    </header>

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-header">
            <h2 class="sidebar-title">Menu</h2>
            <button id="closeMenu" class="close-btn">✕</button>
        </div>
        
        <nav class="sidebar-nav">
            <h3 class="nav-section-title">Principal</h3>
            <a href="index.php" class="nav-link">
                <span class="nav-icon">🏠</span>
                <span class="nav-text">Início</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">📰</span>
                <span class="nav-text">Notícias</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">🔍</span>
                <span class="nav-text">Pesquisar</span>
            </a>
        </nav>
        
        <nav class="sidebar-nav">
            <h3 class="nav-section-title">Categorias</h3>
            <a href="#" class="nav-link">
                <span class="nav-icon">⚽</span>
                <span class="nav-text">Esportes</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">🌍</span>
                <span class="nav-text">Mundo</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">💼</span>
                <span class="nav-text">Economia</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">🎬</span>
                <span class="nav-text">Entretenimento</span>
            </a>
        </nav>
        
        <nav class="sidebar-nav">
            <h3 class="nav-section-title">Usuário</h3>
            <a href="login.php" class="nav-link">
                <span class="nav-icon">🔓</span>
                <span class="nav-text">Login</span>
            </a>
            <a href="cadastro.php" class="nav-link">
                <span class="nav-icon">✏️</span>
                <span class="nav-text">Cadastro</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">📝</span>
                <span class="nav-text">Gerenciar Perfil</span>
            </a>
            <a href="#" class="nav-link">
                <span class="nav-icon">📰</span>
                <span class="nav-text">Suas Notícias</span>
            </a>
        </nav>
    </aside>

     <div class="contMain">
            <span id="tit1">Notícias</span>
            <hr id="hrT">
            <div class="containerNoticias">
            
            <?php
            $news = new Noticia($db);   
            $user = new Usuario($db);
            $stmt = $news->ler();
            ?>
            <?php while ($noticia = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <?php
                $nome = $user->lerPorId($noticia["autor"])["nome"];
                ?>
                <div id="noticia" class="noticia-card">
                    <h1 class="noticia-titulo"><?php echo $noticia["titulo"] ?></h1>
                    <hr class="noticia-hr">
                    <p class="noticia-conteudo"><?php echo $noticia["noticia"] ?></p>
                    <p class="noticia-autor"><strong><small><?php echo $nome . ", " . $noticia["data"] ?></small></strong></p>
                </div>
            <?php endwhile; ?>
        </div>


    <script src="script.js"></script>

</body>

</html>