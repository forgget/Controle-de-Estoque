<?php
include('protect.php');
include('conexao.php');
$nome_loja = 'Minha Loja';
$email = '';
$nome = '';
if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
    echo "<!-- Debug: ID do usuário = " . htmlspecialchars($id_usuario) . " -->";
    $q = $mysqli->query("SELECT nome_loja, email, nome FROM usuarios WHERE id = $id_usuario");
    if (!$q) {
        echo "Erro na consulta: " . $mysqli->error;
    }
    else {
        $row = $q->fetch_assoc();
        if ($row) {
            $nome_loja = $row['nome_loja'];
            $email = $row['email'];
            $nome = $row['nome'];
            echo "<!-- Debug: Dados encontrados = " . print_r($row, true) . " -->";
        } else {
            echo "<!-- Debug: Nenhum dado encontrado para o ID " . htmlspecialchars($id_usuario) . " -->";
        }
    }
} else {
    echo "<!-- Debug: Sessão não possui ID do usuário -->";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta - <?php echo htmlspecialchars($nome_loja); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: #2d2d2d !important;
        }
        main.content {
            background: #2d2d2d !important;
            min-height: 100vh;
            padding: 48px 32px 32px 32px;
            position: relative;
        }
        .back-button {
            display: inline-flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            position: absolute;
            top: 32px;
            left: 32px;
        }
        .back-button svg {
            margin-right: 8px;
        }
        .account-info {
            text-align: center;
            color: #fff;
            margin-top: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .account-info h2 {
            font-size: 2.2rem;
            margin-bottom: 32px;
            font-weight: 600;
        }
        .account-info p {
            font-size: 1.3rem;
            margin-bottom: 16px;
            line-height: 1.6;
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
        .account-info .change-password {
            display: block;
            font-size: 1.3rem;
            margin-top: 32px;
            margin-bottom: 24px;
            line-height: 1.6;
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .account-info .change-password:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .password-form {
            display: none;
            margin-top: 16px;
        }
        .password-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1.1rem;
        }
        .password-form button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .password-form button[type="submit"]:hover {
            background: #2563eb;
        }
        .password-input {
            position: relative;
        }
        .password-input input {
            padding-right: 40px;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            padding: 0;
            margin: 0;
            width: auto;
            background: transparent !important;
        }
    </style>
</head>
<body>
    <main class="content">
        <a href="#" onclick="parent.document.getElementById('conta-frame').src=''; return false;" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;">
                <path d="M19 12H5M12 19l-7-7 7-7"></path>
            </svg>
            Voltar
        </a>
        <div class="account-info">
            <h2>Informações da Conta</h2>
            <p>Nome da Loja: <?php echo htmlspecialchars($nome_loja); ?></p>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <p>Nome do Usuário: <?php echo htmlspecialchars($nome); ?></p>
            <button class="change-password" onclick="document.getElementById('password-form').style.display = 'block';">Alterar Senha</button>
            <form id="password-form" class="password-form" method="POST" action="alterar_senha.php">
                <div class="password-input">
                    <input type="password" name="senha_atual" placeholder="Senha Atual" required>
                    <button type="button" class="toggle-password" onclick="togglePassword(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div class="password-input">
                    <input type="password" name="nova_senha" placeholder="Nova Senha" required>
                    <button type="button" class="toggle-password" onclick="togglePassword(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <button type="submit">Mudar Senha</button>
            </form>
        </div>
    </main>
    <script>
        function togglePassword(button) {
            const input = button.previousElementSibling;
            if (input.type === "password") {
                input.type = "text";
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                input.type = "password";
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        }
    </script>
</body>
</html> 