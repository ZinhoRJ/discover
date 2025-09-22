<?php
include '../config.php'; //inclui o arquivo que tem a conexão com o banco de dados
session_start();

$stmt = $conn->prepare("DELETE FROM faixas WHERE id = ?;");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();

if ($stmt->affected_rows > 0) { //verifica se alguma linha foi afetada
    header("Location: " . $_SERVER['HTTP_REFERER']); //volta para a página anterior
    exit();
} else {
    echo "Erro ao excluir faixa: " . $stmt->error;
}
?>