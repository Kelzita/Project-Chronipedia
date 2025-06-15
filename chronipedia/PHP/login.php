<?php
session_start();
require_once('conexao.php'); // Ajuste o caminho se necessário

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);  // Campo único para email ou usuário
    $senha = $_POST['senha'];

    // Consulta para buscar o usuário pelo email OU pelo nome
    $sql = "SELECT id_usuarios, nome, senha_hash FROM usuarios WHERE email = :login OR nome = :login LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':login' => $login]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuarios'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            header("Location: ../HTML/initialpage.html");
            exit;
        } else {
            echo "❌ Senha incorreta.";
        }
    } else {
        echo "⚠️ Usuário não encontrado.";
    }
} else {
    echo "Método inválido.";
}
?>
