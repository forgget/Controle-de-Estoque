<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Função para fazer login
function login($email, $password) {
    global $conn;
    
    try {
        error_log("Tentando fazer login com email: " . $email);
        
        $stmt = $conn->prepare("SELECT id, nome, senha, nivel_acesso FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            error_log("Usuário encontrado: " . $user['nome']);
            
            if(password_verify($password, $user['senha'])) {
                error_log("Senha verificada com sucesso");
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['nivel_acesso'] = $user['nivel_acesso'];
                return true;
            } else {
                error_log("Senha incorreta");
            }
        } else {
            error_log("Usuário não encontrado");
        }
        return false;
    } catch(PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        return false;
    }
}

// Função para fazer logout
function logout() {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Função para verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Função para verificar se o usuário é administrador
function isAdmin() {
    return isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 'admin';
}
?> 