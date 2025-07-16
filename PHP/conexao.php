<?php
$host = 'localhost';
$port = '3307';
$db   = 'chronipedia';
$user = 'root';
$pass = 'root'; // isso aqui deu dor de cabeça 

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=chronipedia;charset=utf8", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conectado com sucesso!";
} catch (PDOException $e) {
    die("❌ Erro na conexão: " . $e->getMessage());
}
?>
