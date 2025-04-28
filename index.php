<?php
include ('conexao.php');

if(!isset($_SESSION)) {
    session_start();
}

$erro = '';

if(isset($_POST['email']) || isset($_POST['senha'])){
    
    if (strlen($_POST['email']) == 0){
        $erro = "Prencha seu email";
    }else if (strlen($_POST['senha']) == 0){
        $erro = "Prencha sua senha";
    }else {
        $email = $mysqli->real_escape_string($_POST['email']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do codigo SQL: " . $mysqli->error);

        $quantidade = $sql_query->num_rows;
        
        if ($quantidade == 1){
            $usuario = $sql_query->fetch_assoc();

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];

            header("Location: incio.php");
            exit();
        }else{
            $erro = "Credenciais inválidas";
            }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <svg class="lock-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span class="ambiente-seguro">Ambientes Seguro</span>
        </div>
        
        <h1>Acessar a Logistck</h1>
        
        <form action="" id="loginForm" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <div class="password-wrapper">
                    <input type="password" id="senha" name="senha" required>
                    <button type="button" class="toggle-password" id="togglePassword">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            
            <?php
                if(!empty($erro)){
                    echo "<div class=\"error-message\">$erro</div>";       
                }
                ?>
                <button type="submit" class="login-button">Entrar</button>
            
            <a href="forgget-passworld.php" class="forgot-password">Esqueci minha senha</a>
        </form>
    </div>
    
    <div class="brand-side">
        <div class="logo">LOGISTCK</div>
        <div class="shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>
</body>
</html>