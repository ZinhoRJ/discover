<?php
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

//OPERADOR TERNÁRIO: ? (if) : (else). ESTRUTURA CONDICIONAL COMPACTA!
//Verifica se ('if'='?') o valor 'album' está definido (isset) na URL (pelo método GET, via '$_GET'), então converte para INT com 'intval()' e salva na variável $id_album.
//Caso contrário ('else'=':'), $id_album é igual à 0.
$id_playlist = isset($_GET['id']) ? intval($_GET['id']) : 0; 

$sql = "SELECT f.id, f.nome_musica, f.caminho_audio, a.capa_album
        FROM faixas f
        JOIN playlist_faixas pf ON f.id = pf.id_faixa
        JOIN albuns a ON f.id_album = a.id
        WHERE pf.id_playlist = ?";

$stmt = $conn->prepare($sql); //prepara uma consulta SQL para ser executada com segurança
$stmt->bind_param("i", $id_playlist); //acessa a propriedade do sql para modificar a query
$stmt->execute(); //acessa o método do objeto para executar a query

$result = $stmt->get_result(); //o resultado da query é salvo na variável $result!!!!!

//percorre os resultados da consulta SQL e armazena cada linha no array chamado "$faixas"
$faixas = []; //cria o array pra receber cada faixa obtida
while ($row = $result->fetch_assoc()) { //a variável $row é cada tupla que foi encontrada na consulta SQL, que é salva na variável $result, e modificada para ser um array associativo com o método fetch_assoc()
    $faixas[] = $row; //o array faixas recebe o atual valor de $row
} 

echo json_encode($faixas);
?>