<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn = new PDO('mysql:host=localhost;port=3307;dbname=login', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Conexão bem-sucedida!';
    
    // Testar consulta
    $stmt = $conn->query("SELECT * FROM usuarios LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Dados do primeiro usuário:<br>";
    print_r($result);
    
} catch(PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
}
?> 