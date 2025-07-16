<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

require_once('conexao.php');

// Pega dados atualizados do usuário
$sqlUser = "SELECT nome, imagem_perfil FROM usuarios WHERE id_user = :id";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute([':id' => $_SESSION['id_user']]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

$imagem_perfil = $usuario['imagem_perfil'] ?: '../IMG/default_profile.png';
$nome_usuario = $usuario['nome'];

// Busca comunidades públicas
$sql = "SELECT * FROM comunidades WHERE privacidade = 'publica' ORDER BY data_criacao DESC";
$stmt = $pdo->query($sql);
$comunidades_publicas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca comunidades do usuário
$sql2 = "SELECT * FROM comunidades WHERE criador_id = :criador_id ORDER BY data_criacao DESC";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([':criador_id' => $_SESSION['id_user']]);
$comunidades_do_usuario = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Remove comunidades públicas que o usuário já tem
$ids_do_usuario = array_column($comunidades_do_usuario, 'id_comunidade');
$comunidades_publicas = array_filter($comunidades_publicas, function($com) use ($ids_do_usuario) {
    return !in_array($com['id_comunidade'], $ids_do_usuario);
});
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Chronipédia - Seu Painel</title>
    <link rel="icon" href="../IMG/logo-chronipedia-title.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* --- Estilos básicos do header e corpo --- */
        body {
          margin: 0;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          background-color: #121212;
          color: #e6e1d5;
        }
        .header {
          background-color: #161617;
          padding: 10px 20px;
          display: flex;
          align-items: center;
          justify-content: space-between;
          box-shadow: 0 2px 5px rgba(0,0,0,0.7);
          position: relative;
          z-index: 10;
        }
        .logo {
          height: 50px;
        }

        /* Perfil e nome */
        .perfil-container {
          position: relative;
          display: flex;
          align-items: center;
        }

        #perfil-circulo {
          display: flex;
          flex-direction: column;
          align-items: center;
          cursor: pointer;
          width: 70px;
          user-select: none;
          color: #d4a24c;
          font-weight: 600;
          font-size: 0.9rem;
          text-align: center;
        }

        .foto-perfil {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          object-fit: cover;
          border: 2px solid #d4a24c;
          transition: transform 0.3s ease;
          background-color: #333;
        }

        #perfil-circulo:hover .foto-perfil {
          transform: scale(1.1);
        }

        .nome-usuario {
          margin-top: 5px;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          max-width: 70px;
        }

        /* Sidebar */
        #sidebar {
          position: fixed;
          top: 0;
          right: -250px;
          width: 250px;
          height: 100%;
          background-color: #1e1e1e;
          color: #d4a24c;
          box-shadow: -3px 0 8px rgba(0,0,0,0.7);
          transition: right 0.3s ease;
          z-index: 1000;
          padding-top: 60px;
          font-weight: 600;
        }
        #sidebar.sidebar-aberta {
          right: 0;
        }
        #sidebar ul {
          list-style: none;
          padding: 0 20px;
          margin: 0;
        }
        #sidebar ul li {
          margin: 20px 0;
        }
        #sidebar ul li a {
          color: #d4a24c;
          text-decoration: none;
          display: flex;
          align-items: center;
          font-size: 1.1rem;
          transition: color 0.2s ease;
        }
        #sidebar ul li a:hover {
          color: #f0c75e;
        }
        #sidebar ul li a i {
          margin-right: 12px;
        }

        /* Conteúdo principal */
        main {
          max-width: 1000px;
          margin: 20px auto;
          padding: 0 20px;
        }

        section h2 {
          color: #d4a24c;
        }

        .lista-comunidades {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
        }

        .comunidade-card {
          background: #222;
          padding: 15px;
          border-radius: 8px;
          width: 250px;
          box-shadow: 0 0 10px rgba(212,162,76,0.3);
        }

        .comunidade-card img {
          max-width: 100%;
          border-radius: 5px;
          margin-bottom: 10px;
        }

        .btn {
          display: inline-block;
          margin-top: 8px;
          padding: 8px 12px;
          background-color: #d4a24c;
          color: #121212;
          border-radius: 5px;
          text-decoration: none;
          font-weight: 700;
          transition: background-color 0.3s ease;
        }

        .btn:hover {
          background-color: #f0c75e;
        }

        footer {
          text-align: center;
          padding: 15px;
          background-color: #161617;
          color: #d4a24c;
          margin-top: 40px;
        }
    </style>
</head>
<body>

<header>
  <div class="header">
    <a href="initialpage.php">
      <img src="../img/logo-chronipedia.png" alt="Chronipédia Logo" class="logo" />
    </a>

    <div class="perfil-container">
      <div id="perfil-circulo" title="Clique para abrir opções">
        <img src="<?= htmlspecialchars($imagem_perfil) ?>" alt="Foto de perfil" class="foto-perfil" />
        <span class="nome-usuario"><?= htmlspecialchars($nome_usuario) ?></span>
      </div>
    </div>
  </div>
</header>

<nav id="sidebar" class="sidebar-fechada">
  <ul>
    <li><a href="perfil.php"><i class="fas fa-user"></i> Meu Perfil</a></li>
    <li><a href="../HTML/criarwiki.html"><i class="fas fa-book"></i> Criar Wiki</a></li>
    <li><a href="../PHP/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
  </ul>
</nav>


<main>
    <section class="introducao">
        <h2>Seu Painel</h2>
        <p>Gerencie suas comunidades e wikis aqui.</p>
    </section>

    <section class="minhas-comunidades">
        <h2>Suas Comunidades</h2>
        <?php if (count($comunidades_do_usuario) > 0): ?>
            <div class="lista-comunidades">
                <?php foreach ($comunidades_do_usuario as $com): ?>
                    <div class="comunidade-card">
                        <h3><?= htmlspecialchars($com['nome']) ?></h3>
                        <img src="<?= htmlspecialchars($com['imagem_capa']) ?>" alt="Imagem da comunidade" />
                        <p><?= nl2br(htmlspecialchars($com['descricao'])) ?></p>
                        <a href="../HTML/comunidade_detalhe.php?id=<?= $com['id_comunidade'] ?>" class="btn">Ver Comunidade</a>
                        <a href="../HTML/adicionar_personagem.php?comunidade_id=<?= $com['id_comunidade'] ?>" class="btn">Adicionar Personagem</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Você ainda não criou nenhuma comunidade.</p>
        <?php endif; ?>
    </section>

    <section class="comunidades-publicas">
        <h2>Comunidades Públicas Recentes</h2>
        <?php if (count($comunidades_publicas) > 0): ?>
            <div class="lista-comunidades">
                <?php foreach ($comunidades_publicas as $com): ?>
                    <div class="comunidade-card">
                        <h3><?= htmlspecialchars($com['nome']) ?></h3>
                        <img src="<?= htmlspecialchars($com['imagem_capa']) ?>" alt="Imagem da comunidade" />
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

<script>
  const perfilCirculo = document.getElementById('perfil-circulo');
  const sidebar = document.getElementById('sidebar');

  perfilCirculo.addEventListener('click', () => {
    if (sidebar.classList.contains('sidebar-aberta')) {
      sidebar.classList.remove('sidebar-aberta');
      sidebar.classList.add('sidebar-fechada');
    } else {
      sidebar.classList.remove('sidebar-fechada');
      sidebar.classList.add('sidebar-aberta');
    }
  });

  // Fecha sidebar ao clicar fora
  document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && !perfilCirculo.contains(e.target)) {
      sidebar.classList.remove('sidebar-aberta');
      sidebar.classList.add('sidebar-fechada');
    }
  });
</script>

</body>
</html>
