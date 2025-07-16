<?php
session_start();
require_once('../PHP/conexao.php');

if (!isset($_SESSION['id_user'])) {
    header('Location: login.html');
    exit;
}

$usuario_id = $_SESSION['id_user'];
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);

    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    $sqlUser = "SELECT senha_hash, imagem_perfil FROM usuarios WHERE id_user = :id";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([':id' => $usuario_id]);
    $userAtual = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $imagem_atual = $userAtual['imagem_perfil'] ?: '../IMG/default_profile.png';

    if (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } elseif ($senha_atual || $nova_senha || $confirma_senha) {
        if (empty($senha_atual)) {
            $erro = "Informe sua senha atual.";
        } elseif (!password_verify($senha_atual, $userAtual['senha'])) {
            $erro = "Senha atual incorreta.";
        } elseif (strlen($nova_senha) < 6) {
            $erro = "Nova senha deve ter pelo menos 6 caracteres.";
        } elseif ($nova_senha !== $confirma_senha) {
            $erro = "Confirmação da nova senha não confere.";
        }
    }

    if (!$erro) {
        $params = [
            ':nome' => $novo_nome,
            ':email' => $novo_email,
            ':id' => $usuario_id
        ];

        $sqlUpdate = "UPDATE usuarios SET nome = :nome, email = :email";

        // Upload imagem
        if (!empty($_FILES['imagem']['name'])) {
            $pasta_destino = '../IMG/usuarios/';
            if (!is_dir($pasta_destino)) mkdir($pasta_destino, 0777, true);
            $imagem_nome = uniqid() . '_' . basename($_FILES['imagem']['name']);
            $caminho_imagem = $pasta_destino . $imagem_nome;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_imagem)) {
                $sqlUpdate .= ", imagem_perfil = :imagem";
                $params[':imagem'] = $caminho_imagem;
            }
        }

        if ($nova_senha) {
            $sqlUpdate .= ", senha = :senha";
            $params[':senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
        }

        $sqlUpdate .= " WHERE id_user = :id";
        $stmt = $pdo->prepare($sqlUpdate);
        $stmt->execute($params);

        $_SESSION['msg_sucesso'] = "Perfil atualizado com sucesso!";
        header("Location: perfil.php");
        exit;
    }
} else {
    $sql = "SELECT nome, email, imagem_perfil FROM usuarios WHERE id_user = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagem_atual = $usuario['imagem_perfil'] ?: '../IMG/default_profile.png';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Editar Perfil - Chronipédia</title>
  <link rel="stylesheet" href="../CSS/editar_perfil.css" />
  <style>
    .imagem-upload-wrapper {
      text-align: center;
      margin: 20px 0;
    }
    .custom-file-label {
      display: inline-block;
      cursor: pointer;
      position: relative;
      border: 2px dashed #d4a24c;
      border-radius: 10px;
      padding: 10px;
      background-color: #1a1a1a;
      transition: border-color 0.3s ease;
    }
    .custom-file-label:hover {
      border-color: #f0c75e;
    }
    .img-preview {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #d4a24c;
      display: block;
      margin: 0 auto 10px auto;
      transition: transform 0.3s ease;
    }
    .trocar-texto {
      display: block;
      font-size: 0.9rem;
      color: #d4a24c;
    }
    .mensagem-erro {
      color: #e74c3c;
      font-weight: bold;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <main>
    <section>
      <h2>Editar Perfil</h2>
      <?php if ($erro): ?>
        <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>
      <form method="POST" enctype="multipart/form-data" novalidate>
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($novo_nome ?? $usuario['nome']) ?>" required />

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($novo_email ?? $usuario['email']) ?>" required />

        <div class="imagem-upload-wrapper">
          <label for="imagem" class="custom-file-label" id="preview-container" title="Clique para mudar a foto">
            <img src="<?= htmlspecialchars($imagem_atual) ?>" alt="Foto de Perfil" id="preview-img" class="img-preview"/>
            <span class="trocar-texto">Trocar Imagem</span>
          </label>
          <input type="file" name="imagem" id="imagem" accept="image/*" style="display:none;" />
        </div>

        <hr style="border: 1px solid #444; margin: 25px 0;" />
        <h3 style="color:#f39c12; text-align:center; margin-bottom:15px;">Trocar Senha</h3>

        <label for="senha_atual">Senha Atual</label>
        <input type="password" name="senha_atual" id="senha_atual" placeholder="Digite sua senha atual" autocomplete="current-password" />

        <label for="nova_senha">Nova Senha</label>
        <input type="password" name="nova_senha" id="nova_senha" placeholder="Nova senha (mínimo 6 caracteres)" autocomplete="new-password" />

        <label for="confirma_senha">Confirme a Nova Senha</label>
        <input type="password" name="confirma_senha" id="confirma_senha" placeholder="Confirme a nova senha" autocomplete="new-password" />

        <button type="submit">Salvar Alterações</button>
      </form>
      <a href="perfil.php">Cancelar</a>
    </section>
  </main>

  <script>
    const inputImagem = document.getElementById('imagem');
    const previewImg = document.getElementById('preview-img');
    const previewContainer = document.getElementById('preview-container');

    previewContainer.addEventListener('click', () => {
      inputImagem.click();
    });

    inputImagem.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.transform = 'scale(1.1)';
          setTimeout(() => {
            previewImg.style.transform = 'scale(1)';
          }, 300);
        }
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
