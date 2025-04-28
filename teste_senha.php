<?php
$senha = "password"; // Senha padrão do Laravel
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Testando senha 'password':<br>";
if (password_verify($senha, $hash)) {
    echo "Senha correta!<br>";
} else {
    echo "Senha incorreta!<br>";
}

echo "<br>Gerando novo hash para a senha 'password':<br>";
$novo_hash = password_hash($senha, PASSWORD_BCRYPT);
echo "Novo hash: " . $novo_hash . "<br>";

echo "<br>Testando o novo hash:<br>";
if (password_verify($senha, $novo_hash)) {
    echo "Senha correta com o novo hash!<br>";
} else {
    echo "Senha incorreta com o novo hash!<br>";
} 