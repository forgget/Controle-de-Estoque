<?php
    if(!isset($_SESSION)){
        session_start();
    }
if(!isset($_SESSION['id'])){
    die("Você nao pode acessar está pagina, nao está logado. <p><a href=\"index.php\">Entrar<\a><\p>");
}
?>