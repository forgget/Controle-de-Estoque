<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obter informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$nivel_acesso = $_SESSION['nivel_acesso'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Logistck</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</h1>
        <p>Nível de acesso: <?php echo htmlspecialchars($nivel_acesso); ?></p>
        
        <div class="menu">
            <a href="produtos.php">Produtos</a>
            <a href="movimentacoes.php">Movimentações</a>
            <?php if ($nivel_acesso == 'admin' || $nivel_acesso == 'gerente') { ?>
                <a href="relatorios.php">Relatórios</a>
            <?php } ?>
            <a href="../logout.php">Sair</a>
        </div>
    </div>
</body>
</html> 