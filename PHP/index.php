<?php
session_start();
if (isset($_SESSION['id_user'])) {
    header("Location: initialpage.php");
    exit;
}

require_once('../PHP/conexao.php');

$sql = "SELECT * FROM comunidades WHERE privacidade = 'publica' ORDER BY data_criacao DESC";
$stmt = $pdo->query($sql);
$comunidades_publicas = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chronipédia - Bem-vindo</title>
    <link rel="icon" href="../IMG/logo-chronipedia-title.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
    <div class="header">
        <a href="index.php">
            <img src="../img/logo-chronipedia.png" alt="Chronipédia Logo" class="logo" />
        </a>
        <div class="botoes-login">
    <a class="botaocreate" href="../HTML/login.html">
        <i class="fas fa-sign-in-alt"></i> Entrar
    </a>
    <a class="botaocreate" href="../HTML/cadastro.html">
        <i class="fas fa-user-plus"></i> Cadastrar-se
    </a>
</div>

</header>
<main>
    <section class="introducao">
        <h2>Bem-Vindo à Comunidade!</h2>
        <p>Crie Wikis, Explore Wikis e divirta-se em nossa comunidade!</p>
    </section>

    <section class="comunidades-publicas">
        <h2>Comunidades Recentes (Públicas)</h2>
        <?php if (count($comunidades_publicas) > 0): ?>
            <div class="lista-comunidades">
                <?php foreach ($comunidades_publicas as $com): ?>
                    <div class="comunidade-card">
                        <h3><?= htmlspecialchars($com['nome']) ?></h3>
                        <img src="<?= htmlspecialchars($com['imagem_capa']) ?>" alt="Imagem da comunidade" width="150" />
                        <p><?= nl2br(htmlspecialchars($com['descricao'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Nenhuma comunidade pública ainda.</p>
        <?php endif; ?>
    </section>
</main>
<footer>
    <p>&copy; 2025 site para os autistas.</p>
</footer>
</body>
</html>
