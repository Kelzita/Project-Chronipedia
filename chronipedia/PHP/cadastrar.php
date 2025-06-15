<?php
require_once('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['username']);
    $email = strtolower(trim($_POST['email']));
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $data_nascimento = $_POST['data-nascimento'];
    $genero = $_POST['genero'];

    $sql = "INSERT INTO usuarios (nome, email, senha_hash, data_nascimento, genero) 
            VALUES (:nome, :email, :senha, :data_nascimento, :genero)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
            ':data_nascimento' => $data_nascimento,
            ':genero' => $genero
        ]);
        echo "<script>
                alert('✅ Cadastro realizado com sucesso!');
                window.location.href = '../HTML/login.html';
              </script>";
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>alert('⚠️ Este e-mail já está cadastrado.'); window.history.back();</script>";
        } else {
            echo "<script>alert('❌ Erro ao cadastrar: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
        exit;
    }
} else {
    echo "<script>alert('Método inválido.'); window.history.back();</script>";
    exit;
}
?>
