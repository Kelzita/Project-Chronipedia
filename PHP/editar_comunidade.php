<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

require_once('conexao.php');

$id_comunidade = intval($_GET['id'] ?? 0);
$id_usuario = $_SESSION['id_user'];
$redirect = $_GET['redirect'] ?? 'perfil.php';

// Busca comunidade
$sql = "SELECT * FROM comunidades WHERE id_comunidade = :id AND criador_id = :criador_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_comunidade, ':criador_id' => $id_usuario]);
$comunidade = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comunidade) {
    die("Você não tem permissão para editar essa comunidade.");
}

// Preenchendo variáveis para o formulário
$nome_val = $comunidade['nome'];
$descricao_val = $comunidade['descricao'];
$privacidade_val = $comunidade['privacidade'] ?? 'publica';
$universo_val = $comunidade['universo'] ?? '';
$tags_val = $comunidade['tags'] ?? '';
$imagem_capa = $comunidade['imagem_capa'];

$erro = null;
$erro_upload = null;

// Atualiza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['apagar'])) {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $privacidade = trim($_POST['privacidade'] ?? '');
    $universo = trim($_POST['universo'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    
    // Validação simples de privacidade (garantir valores esperados)
    $privacidades_validas = ['publica', 'privada', 'restrita'];
    if (!in_array($privacidade, $privacidades_validas)) {
        $privacidade = 'publica'; // padrão
    }

    // Upload da imagem capa, se houver novo arquivo
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagem']['tmp_name'];
        $nome_arquivo = basename($_FILES['imagem']['name']);
        $ext = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $permitidos)) {
            $novo_nome = 'uploads/capas/' . uniqid() . '.' . $ext;
            // Criar pasta se não existir
            if (!is_dir('../uploads/capas')) {
                mkdir('../uploads/capas', 0777, true);
            }
            if (move_uploaded_file($tmp, '../' . $novo_nome)) {
                $imagem_capa = $novo_nome;
            } else {
                $erro_upload = "Erro ao enviar imagem.";
            }
        } else {
            $erro_upload = "Formato inválido. Use jpg, jpeg, png ou gif.";
        }
    }

    if (empty($nome)) {
        $erro = "Nome não pode ser vazio.";
    }

    if (!$erro && !$erro_upload) {
        $sql_upd = "UPDATE comunidades 
                    SET nome = :nome, descricao = :descricao, privacidade = :privacidade, 
                        universo = :universo, tags = :tags, imagem_capa = :imagem 
                    WHERE id_comunidade = :id";

        $stmt_upd = $pdo->prepare($sql_upd);
        $stmt_upd->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':privacidade' => $privacidade,
            ':universo' => $universo,
            ':tags' => $tags,
            ':imagem' => $imagem_capa,
            ':id' => $id_comunidade
        ]);
        header("Location: $redirect?msg=editada");
        exit;
    }
}

// Apagar
if (isset($_POST['apagar'])) {
    $sql_del = "DELETE FROM comunidades WHERE id_comunidade = :id AND criador_id = :criador_id";
    $stmt_del = $pdo->prepare($sql_del);
    $stmt_del->execute([':id' => $id_comunidade, ':criador_id' => $id_usuario]);
    header("Location: $redirect?msg=apagada");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Comunidade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/editar_comunidade.css">
    <script src="../js/editar_comunidade.js" defer></script>
</head>
<body>
<div class="form-container">
    <h1>Editar Comunidade</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome_val) ?>" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required><?= htmlspecialchars($descricao_val) ?></textarea>

        <label for="privacidade">Privacidade:</label>
        <select name="privacidade" id="privacidade" required>
            <option value="publica" <?= $privacidade_val === 'publica' ? 'selected' : '' ?>>Pública</option>
            <option value="privada" <?= $privacidade_val === 'privada' ? 'selected' : '' ?>>Privada</option>
            <option value="restrita" <?= $privacidade_val === 'restrita' ? 'selected' : '' ?>>Restrita</option>
        </select>

        <label for="universo">Universo:</label>
        <input type="text" id="universo" name="universo" value="<?= htmlspecialchars($universo_val) ?>" placeholder="Ex: Fantasia, Sci-Fi, Histórico" />

        <label for="tags">Tags (separadas por vírgula):</label>
        <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($tags_val) ?>" placeholder="ex: magia, aventura, medieval" />

        <div class="imagem-upload-wrapper">
            <label for="imagem" class="custom-file-label" id="preview-container" title="Clique para trocar a capa">
                <img src="<?= htmlspecialchars($imagem_capa ?? '../IMG/default_capa.png') ?>" alt="Capa da Comunidade" id="preview-img" class="img-preview" />
                <span class="trocar-texto">Trocar Capa</span>
            </label>
            <input type="file" name="imagem" id="imagem" accept="image/*" style="display:none;" />
        </div>

        <?php if ($erro): ?>
            <p class="mensagem erro"><?= htmlspecialchars($erro) ?></p>
        <?php elseif ($erro_upload): ?>
            <p class="mensagem erro"><?= htmlspecialchars($erro_upload) ?></p>
        <?php endif; ?>

        <button type="submit" class="btn">Salvar Alterações</button>
        <button type="submit" name="apagar" class="btn btn-danger" onclick="return confirm('Apagar esta comunidade?')">Apagar</button>
    </form>
</div>
</body>
</html>
