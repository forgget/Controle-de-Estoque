<?php
session_start();
require_once 'php/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Redirecionar para a página inicial
header("Location: php/incio.php");
exit();
?> 