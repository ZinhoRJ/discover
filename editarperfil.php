<?php
$conn = new mysqli("localhost", "root", "", "fecip");

if ($conn -> erro){
    die("Erro de conexão: " . $conn->erro);
}

//Pegar cookies de id e senha
if (isset($_COOKIE["user_id"]) && isset($_COOKIE["senha"])){
    $userId = $_COOKIE["user_id"];
    $userPass = $_COOKIE["pass"];
}

//Verificar se o id e senha são iguais

$stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE id=''")
?>