<?php
$conn = new mysqli('localhost', 'root', '', 'fecip');
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT caminho_audio, nome_musica, nome_artista, nome_album, capa_album FROM musicas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $music = $result->fetch_assoc();
    echo json_encode($music);
}
?>