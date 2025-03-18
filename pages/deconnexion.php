<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// dettruire toutes les variables de session
$_SESSION = array();

// detruire le cookie de session si présent mais sert a rien j'ai finalement pas fait de cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Détruire la session
session_destroy();

header("Location: login.php");
exit();
?> 