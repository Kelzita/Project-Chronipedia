<?php
session_start();
require_once('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    die("⚠️ Você precisa estar logado para criar uma comunidade.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $universo = $_POST['universo'] ?? null;
    $tags = $_POST['tags'] ?? null;
    $privacidade = $_POST['privacidade'] ?? 'publica'; // valor padrão
    $criador_id = $_SESSION['usuario_id'];
    $data_criacao = date('Y-m-d H:i:s');

    // Processar a imagem (se enviada)
    $imagem_caminho = '';
    if (!empty($_FILES['imagem']['name'])) {
        $pasta_destino = '../IMG/comunidades/';
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0777, true);
        }
        $imagem_nome = uniqid() . '_' . basename($_FILES['imagem']['name']);
        $imagem_caminho = $pasta_destino . $imagem_nome;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem_caminho);
    }

    // Insere a comunidade no banco
    $sql = "INSERT INTO comunidades (nome, descricao, universo, tags, imagem_capa, privacidade, criador_id, data_criacao)
            VALUES (:nome, :descricao, :universo, :tags, :imagem, :privacidade, :criador_id, :data_criacao)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':descricao' => $descricao,
        ':universo' => $universo,
        ':tags' => $tags,
        ':imagem' => $imagem_caminho,
        ':privacidade' => $privacidade,
        ':criador_id' => $criador_id,
        ':data_criacao' => $data_criacao
    ]);

    // Redireciona após sucesso
    header("Location: perfil.php");
    exit;
} else {
    echo "⚠️ Requisição inválida.";
}
