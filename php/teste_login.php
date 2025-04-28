<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Dados de teste
$email = 'admin@admin.com';
$senha = 'admin123';

try {
    $host = 'localhost:3307';
    $dbname = 'login';
    $username = 'root';
    $password = '';
    
    echo "Tentando conectar ao banco de dados...<br>";
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão estabelecida com sucesso!<br>";
    
    // Buscar usuário
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Usuário encontrado:<br>";
        print_r($user);
        
        // Verificar senha
        if(password_verify($senha, $user['senha'])) {
            echo "<br>Senha correta!<br>";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            echo "Sessão criada:<br>";
            print_r($_SESSION);
        } else {
            echo "<br>Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado!";
    }
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 