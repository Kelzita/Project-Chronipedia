<?php
session_start();
require_once('../PHP/conexao.php');

if (!isset($_SESSION['id_user'])) {
  header('Location: login.html');
  exit;
}

$usuario_id = $_SESSION['id_user'];


// Dados do usuário
$sqlUser = "SELECT nome, imagem_perfil FROM usuarios WHERE id_user = :id";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute([':id' => $usuario_id]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
$foto_perfil = $usuario['imagem_perfil'] ?: '../IMG/default_profile.png';
$usuario_nome = $usuario['nome'];

// Comunidades do usuário
$sqlComunidades = "SELECT * FROM comunidades WHERE criador_id = :id";
$stmt = $pdo->prepare($sqlComunidades);
$stmt->execute([':id' => $usuario_id]);
$comunidades = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Perfil de <?= htmlspecialchars($usuario_nome) ?></title>
  <link rel="stylesheet" href="../CSS/perfil.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<header>
  <div class="header">
    <img src="../IMG/logo-chronipedia.png" alt="Logo Chronipédia" class="logo" onclick="location.href='initialpage.php'" style="cursor:pointer;">
    <!-- Aqui pode ter outros elementos do header, se quiser -->
  </div>
</header>


<body>

<div class="painel">
  <aside class="menu-lateral">
    <div class="perfil-usuario">
      <img src="<?= $foto_perfil ?>" alt="Foto de perfil" class="foto-perfil">
      <h2><?= htmlspecialchars($usuario_nome) ?></h2>
      <a href="editar_perfil.php" class="botao-editar"><i class="fas fa-user-edit"></i> Editar Perfil</a>

    </div>
    <nav>
      <ul>
        <li><a href="#comunidades"><i class="fas fa-users"></i> Comunidades</a></li>
        <li><a href="#"><i class="fas fa-user-astronaut"></i> Personagens</a></li>
        <li><a href="#"><i class="fas fa-cog"></i> Configurações</a></li>
      </ul>
    </nav>
  </aside>

  <main class="conteudo">
  <section id="comunidades">
    <h2>Painel de Comunidades</h2>
    <?php if (count($comunidades) > 0): ?>
      <div class="grid-comunidades">
        <?php foreach ($comunidades as $com): ?>
          <div class="comunidade-card">
            <img src="<?= htmlspecialchars($com['imagem_capa']) ?>" alt="Capa" class="img-capa">
            <h3><?= htmlspecialchars($com['nome']) ?></h3>
            <p><?= nl2br(htmlspecialchars($com['descricao'])) ?></p>
            <div class="botoes">
              <a href="editar_comunidade.php?id=<?= $com['id_comunidade'] ?>" class="btn">Editar</a>
              <a href="comunidade.php?id=<?= $com['id_comunidade'] ?>" class="btn">Ver</a>
              <a href="add_personagem.php?comunidade_id=<?= $com['id_comunidade'] ?>" class="btn">+ Personagem</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Você ainda não criou comunidades. <a href="criarwiki.html" class="btn">Criar agora</a></p>
    <?php endif; ?>
  </section>
</main>
  