<?php
session_start();

// Limpa todas as variáveis da sessão
$_SESSION = array();

// Destrói a sessão
session_destroy();

// Redireciona para a página inicial ou de login
header("Location: ../index.php");
exit();
?>