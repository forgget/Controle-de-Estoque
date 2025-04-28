<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

echo "Estado atual da sessão:<br>";
print_r($_SESSION);

echo "<br><br>Cookies:<br>";
print_r($_COOKIE);

echo "<br><br>Configurações do PHP:<br>";
echo "session.save_path: " . ini_get('session.save_path') . "<br>";
echo "session.cookie_domain: " . ini_get('session.cookie_domain') . "<br>";
echo "session.cookie_path: " . ini_get('session.cookie_path') . "<br>";
echo "session.cookie_secure: " . ini_get('session.cookie_secure') . "<br>";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "<br>";

echo 'user_id: ';
echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NÃO DEFINIDO';
?> 