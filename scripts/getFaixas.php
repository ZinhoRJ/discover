<?php
// get_faixas.php

$conn = new mysqli("localhost", "root", "", "fecip");

//OPERADOR TERNÁRIO: ? (if) : (else). ESTRUTURA CONDICIONAL COMPACTA!
//Verifica se ('if'='?') o valor 'album' está definido (isset) na URL (pelo método GET, via '$_GET'), então converte para INT com 'intval()' e salva na variável $id_album.
//Caso contrário ('else'=':'), $id_album é igual à 0.
$id_album = isset($_GET['album']) ? intval($_GET['album']) : 0; 

$sql = "SELECT id, nome_musica, caminho_audio FROM faixas WHERE id_album = ?";

$stmt = $conn->prepare($sql); //prepara uma consulta SQL para ser executada com segurança
$stmt->bind_param("i", $id_album); //acessa a propriedade do sql para modificar a query
$stmt->execute(); //acessa o método do objeto para executar a query
$result = $stmt->get_result(); //o resultado da query é salvo na variável $result!!!!!

//percorre os resultados da consulta SQL e armazena cada linha no array chamado "$faixas"
$faixas = [];
while ($row = $result->fetch_assoc()) { //a variável $row é cada tupla que foi encontrada na consulta SQL, que é salva na variável $result, e modificada para ser um array associativo com o método fetch_assoc()
    $faixas[] = $row; //o array faixas recebe o atual valor de $row no loop.
} 

echo json_encode($faixas);
?>