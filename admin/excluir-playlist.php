<?php
include '../config.php'; //

if (isset($_GET['id'])){
    $id = $_GET['id'];
} else {
    die("ID não fornecido.");
}

// Buscar capa da playlist e excluir arquivo físico
$stmt = $conn->prepare("SELECT caminho_imagem_playlist FROM playlists WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($playlist = $result->fetch_assoc()) {
    if (!empty($playlist['caminho_imagem_playlist']) && file_exists($playlist['caminho_imagem_playlistt'])) {
        unlink($playlist['caminho_imagem_playlist']);
    }
}
$stmt->close();

// Apagar Faixas da Playlist
$stmt = $conn->prepare("DELETE FROM playlist_faixas WHERE id_playlist = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Apagar Playlist
$stmt = $conn->prepare("DELETE FROM playlists WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: ../admin/admin.php");
exit();
?>