<?php
include('auth.php');

if(!isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}
?>