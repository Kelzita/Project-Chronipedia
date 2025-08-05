<?php
$host = 'localhost';
$port = '3306';
$dbname   = 'chronipedia';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=chronipedia;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conectado com sucesso!";
} catch (PDOException $e) {
    die("❌ Erro na conexão: " . $e->getMessage());
}
?>
