<?php
session_start();
require_once('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);  // Campo único para email ou usuário
    $senha = $_POST['senha'];

    // Consulta para buscar o usuário cadastrado pelo email OU pelo user ( o nome kkkkk) 
    

    $sql = "SELECT id_user, nome, senha_hash FROM usuarios WHERE email = :login OR nome = :login LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':login' => $login]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['id_user'] = $usuario['id_user'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            header("Location: initialpage.php");
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

//to feliz pra caramba beijo