<?php
try {
    $host = 'localhost:3307';
    $dbname = 'login';
    $username = 'root';
    $password = '';
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão com o banco de dados estabelecida com sucesso!";
    
    // Testar se as tabelas existem
    $tables = ['usuarios', 'produtos', 'movimentacoes'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<br>Tabela '$table' existe.";
        } else {
            echo "<br>Tabela '$table' não existe!";
        }
    }
    
} catch(PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?> 