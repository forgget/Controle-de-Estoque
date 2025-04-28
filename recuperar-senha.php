<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - LOGISTCK</title>
    <link rel="stylesheet" href="recuperar-senha.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="form-side">
            <div class="form-container">
                <h1>Esqueceu a senha?</h1>
                <p>Enviaremos um e-mail com instruções de como redefinir sua senha.</p>
                
                <form action="" id="recuperarSenha" method="POST">
                    <div class="form-group">
                        <label for="email">Insira seu e-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="login-button">ENVIAR</button>
                </form>
            </div>
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
    </div>
</body>
</html>