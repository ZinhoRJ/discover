<?php
session_start();
$conn = new mysqli("localhost", "root", "", "fecip");

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

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
    $stmt = $conn->prepare("INSERT INTO playlists (nome_playlist, id_usuario) VALUES ('Favoritos', ?)");
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
