<?php 
session_start();
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    die("ID de playlist não fornecido.");
}

$stmt = $conn->prepare("SELECT id, nome_playlist, caminho_imagem_playlist FROM playlists ORDER BY id DESC;");
$stmt->execute();
$result = $stmt->get_result();
$playlist = $result->fetch_assoc();

if ($result->num_rows === 0) {
    echo "Playlist não encontrada.";
    exit();
}
?>

<html>
<body>
<form action="../admin/editar-playlist?id= <?php echo $id ?>" method="POST" enctype="multipart/form-data">
    <label for="caminho_imagem_playlist">Capa da Playlist:</label>
    <input type="file" id="caminho_imagem_playlist" name="caminho_imagem_playlist" accept="image/*"><br>
    <img src="../<?php echo $playlist['caminho_imagem_playlist']; ?>" alt="Capa Atual" style="width:100px;height:100px;"><br>

    <label for="nome">Nome da Playlist:</label>
    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($playlist['nome_playlist']); ?>" required><br>

    <input type="submit" value="Salvar Alterações">
</form>
</body>
</html>