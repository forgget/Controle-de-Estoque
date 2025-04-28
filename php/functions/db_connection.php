<?php
function getConnection() {
    $host = 'localhost';
    $dbname = 'login';
    $username = 'root';
    $password = '';
    $port = 3307;
    
    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}
?> 