<?php
session_start();
include "config.php"; // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_playlist'];
    $faixasSelecionadas = $_POST['faixas'] ?? [];
    $id_usuario = $_COOKIE['user_id'];
    $data = date('Y-m-d');

    // Diretório onde os arquivos serão salvos
    $diretorioDestino = 'uploads/';
    if (!is_dir($diretorioDestino)) { //verifica se existe o diretório da variável $diretorioDestino
        mkdir($diretorioDestino, 0777, true); //caso ainda não exista, cria a pasta com a função "mkdir"
    }

    // Salvar imagem da capa
    $caminhoImagem = "";
    if (
        isset($_FILES['imagem_perfil']) &&
        $_FILES['imagem_perfil']['error'] === UPLOAD_ERR_OK &&
        is_uploaded_file($_FILES['imagem_perfil']['tmp_name'])
    ) {
        $extensaoImagem = pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION);
        $nomeUnicoImagem = uniqid('pfp_') . '.' . $extensaoImagem;
        $caminhoImagem = $diretorioDestino . $nomeUnicoImagem;

        move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $caminhoImagem);
    }

    // Inserir playlist
    $stmt = $conn->prepare("INSERT INTO playlists (nome_playlist, id_usuario, data_criacao, caminho_imagem_playlist) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $nome, $id_usuario, $data, $caminhoImagem);
    $stmt->execute();
    $id_playlist = $stmt->insert_id;

    // Inserir faixas na playlist
    $stmtFaixa = $conn->prepare("INSERT INTO playlist_faixas (id_playlist, id_faixa) VALUES (?, ?)");
    foreach ($faixasSelecionadas as $id_faixa) {
        $stmtFaixa->bind_param("ii", $id_playlist, $id_faixa);
        $stmtFaixa->execute();
    }

    echo "Playlist criada com sucesso!";
}
?>

<form action="criar-playlist.php" method="POST" enctype="multipart/form-data">
    <label for="imagem_perfil">Foto de Perfil:</label>
    <input type="file" name="imagem_perfil" id="imagem_perfil" accept="image/*">
    
    <input type="text" name="nome_playlist" placeholder="Nome da playlist" required>
    
    <label>Selecione as faixas:</label><br>
    <?php
    // Supondo que o usuário já está logado
    

    $id_usuario = $_COOKIE['user_id'];

    // Buscar faixas disponíveis
    $query = "
        SELECT f.id, f.nome_musica, a.nome_album 
        FROM faixas f
        JOIN albuns a ON f.id_album = a.id
        WHERE a.id_usuario = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($faixa = $result->fetch_assoc()) {
        echo "<input type='checkbox' name='faixas[]' value='{$faixa['id']}'> {$faixa['nome_musica']} ({$faixa['nome_album']})<br>";
    }
    ?>
    
    <button type="submit">Criar Playlist</button>
</form>