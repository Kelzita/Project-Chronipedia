<?php
$host = 'localhost';
$db   = 'chronipedia';
$user = 'root';
$pass = '1234'; // Se você entrou no phpMyAdmin sem senha, deixe vazio

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conectado com sucesso!";
} catch (PDOException $e) {
    die("❌ Erro na conexão: " . $e->getMessage());
}
?>
