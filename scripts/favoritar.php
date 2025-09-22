<?php
session_start();
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if (isset($_COOKIE["user_id"])){
    $id_usuario = intval($_COOKIE["user_id"]);
}
$id_faixa = intval($_POST['id_faixa']);

// Verifica se a playlist "Favoritos" já existe
$stmt = $conn->prepare("SELECT id FROM playlists WHERE nome_playlist = 'Favoritos' AND id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_playlist = $row['id'];
} else {
    // Cria playlist "Favoritos"
    $stmt = $conn->prepare("INSERT INTO playlists (nome_playlist, id_usuario, caminho_imagem_playlist) VALUES ('Favoritos', ?, './uploads/playlist.jpg')");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $id_playlist = $stmt->insert_id;
}

// Adiciona faixa à playlist (evita duplicatas)
$stmt = $conn->prepare("INSERT IGNORE INTO playlist_faixas (id_playlist, id_faixa) VALUES (?, ?)");
$stmt->bind_param("ii", $id_playlist, $id_faixa);
$stmt->execute();

echo "Faixa adicionada aos favoritos!";
?>
