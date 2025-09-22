<?php
//String de conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "fecip");

//Verificar se a conexão foi bem sucedida
if ($conn->connect_error) {
    die("[config.php] Falha na conexão com o banco de dados: " . $conn->connect_error);
}
?>