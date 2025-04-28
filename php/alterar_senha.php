<?php
include('protect.php');
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $id_usuario = $_SESSION['usuario_id'];

    // Verificar senha atual
    $q = $mysqli->query("SELECT senha FROM usuarios WHERE id = $id_usuario");
    if ($q && $row = $q->fetch_assoc()) {
        if (password_verify($senha_atual, $row['senha'])) {
            // Senha atual correta, atualizar para nova senha
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $update = $mysqli->query("UPDATE usuarios SET senha = '$nova_senha_hash' WHERE id = $id_usuario");
            
            if ($update) {
                echo "<script>alert('Senha alterada com sucesso!'); window.location.href = 'conta.php';</script>";
            } else {
                echo "<script>alert('Erro ao alterar senha. Tente novamente.'); window.location.href = 'conta.php';</script>";
            }
        } else {
            echo "<script>alert('Senha atual incorreta.'); window.location.href = 'conta.php';</script>";
        }
    }
}
?> 