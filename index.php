<?php
session_start();
require_once 'php/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND status = 'ativo'";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
                
                // Atualizar último login
                $sql_update = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?";
                $stmt_update = $mysqli->prepare($sql_update);
                $stmt_update->bind_param("i", $usuario['id']);
                $stmt_update->execute();
                
                header("Location: php/incio.php");
                exit();
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado ou inativo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Logistck</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/Login.css">
</head>
<body>
    <div class="login-container">
        <?php if(isset($erro)) { ?>
            <div class="erro-mensagem">
                <span class="erro-icon">⚠️</span>
                <span class="erro-texto"><?php echo $erro; ?></span>
            </div>
        <?php } ?>
        
        <div class="logo-container">
            <span class="lock-icon">🔒</span>
            <span class="ambiente-seguro">Ambiente Seguro</span>
        </div>
        
        <h1>Acesse sua conta</h1>
        
        <form action="" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" placeholder="Digite seu email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"> 
            </div>
            
            <div class="form-group">
                <label>Senha</label>
                <div class="password-wrapper">
                    <input type="password" name="senha" placeholder="Digite sua senha" id="senha">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="login-button">Entrar</button>
            <a href="#" class="forgot-password">Esqueceu sua senha?</a>
        </form>
    </div>
    
    <div class="brand-side">
        <div class="shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="logo">Logistck</div>
    </div>

    <style>
        .toggle-password {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: none !important;
            border: none;
            cursor: pointer;
            padding: 0;
            margin: 0;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }
        .toggle-password svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
        }
        .toggle-password:hover {
            color: #333;
        }
        .eye-icon {
            display: none !important;
        }
    </style>

    <script>
    let isEyeOpen = false;
    function togglePassword() {
        var senha = document.getElementById('senha');
        var button = document.querySelector('.toggle-password');
        
        if (!isEyeOpen) {
            isEyeOpen = true;
            senha.type = 'text';
            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        } else {
            isEyeOpen = false;
            senha.type = 'password';
            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        }
    }
    </script>
</body>
</html> 