<?php
    include './config.php'; //inclui o arquivo que tem a conexão com o banco de dados
    include './scripts/getCorPerfil.php'; //inclui o arquivo que define a variável $corFundo com o estilo do perfil
    
    $id_usuario = intval($_GET['id']); //variável id_usuário é igual ao valor da URL "id" convertido para INT.
    /*$stmt =  $conn->prepare("SELECT id, nome, email, bio FROM usuarios WHERE id = ?");*/
    
    // Consulta dos dados do usuário
    $stmt = $conn->prepare("
        SELECT nome, email, bio, foto_perfil 
        FROM usuarios 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultadoUsuario = $stmt->get_result();
    $user = $resultadoUsuario->fetch_assoc();

    if (!$id_usuario){
        die("404 Page Not Found");
    }
    
    // Consulta dos álbuns do usuário
    $stmt = $conn->prepare("
        SELECT id AS album_id, nome_album, genero, ano, descricao, capa_album 
        FROM albuns 
        WHERE id_usuario = ?
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultadoAlbuns = $stmt->get_result();

    // Consulta das playlists do usuário
    $stmt = $conn->prepare("
        SELECT id AS playlist_id, nome_playlist, data_criacao, caminho_imagem_playlist 
        FROM playlists 
        WHERE id_usuario = ?
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultadoPlaylists = $stmt->get_result();
?>

<html>
    <head>
        <title>Perfil</title>

        <link rel="stylesheet" href="./css/perfil.css">
    </head>
    <body>
        <div class="container">
            <div class="div-perfil">
                <img class="imagem-perfil" src=" <?php echo $user['foto_perfil']; ?>" alt="Foto de Perfil de <?php echo $user['nome']; ?>">

                <div class="info-perfil">
                    <h1><?php echo $user['nome']; ?></h1>
                    <br>
                    <p><?php echo $user['bio']; ?></p>
                </div>
            </div>
        
        
        <br>
        ID: <?php echo $id_usuario; ?> <br>
        

        <h2>Lançamentos</h2>
        <div class="container-albuns">
            <?php
            // Exibe todos os álbuns do usuário, se houver
            $temAlbum = false;
            while ($album = $resultadoAlbuns->fetch_assoc()) {
                if (!empty($album['album_id'])) {
                    $temAlbum = true;
                    echo "<div class='albuns'>";
                    echo "<a href='album.php?id=" . $album['album_id'] . "'>";
                    echo "<img src='" . $album['capa_album'] . "' alt='Capa do álbum " . $album['nome_album'] . "'>";
                    echo "<h3>" . $album['nome_album'] . "</h3> </a>";
                    echo "</div>";
                }
            }
            if (!$temAlbum) {
                echo "<p>Este usuário não possui álbuns publicados.</p>";
            }
            ?>
        </div>

        <h2>Playlists</h2>
        <div class="container-playlists">
            <?php
            if (!isset($_COOKIE['user_id']) || $_COOKIE['user_id'] == $id_usuario) {
                // Exibe todas as playlists do usuário, se houver
                $temPlaylist = false;
                // Verifica se o usuário logado é o dono do perfil
                if (isset($_COOKIE['user_id']) && $_COOKIE['user_id'] == $id_usuario) {
                    // Botão para adicionar nova playlist
                    echo "<div class='playlists'>";
                    echo "<a href='criar-playlist.php'>";
                    echo "<div class='nova-playlist-btn'>+</div>";
                    echo "<h3>Criar Playlist</h3></a>";
                    echo "</div>";
                }

                // Exibe todas as playlists do usuário, se houver
                $temPlaylist = false;
                while ($playlist = $resultadoPlaylists->fetch_assoc()) {
                    if (!empty($playlist['playlist_id'])) {
                        $temPlaylist = true;
                        echo "<div class='playlists'>";
                        echo "<a href='playlist.php?id=" . $playlist['playlist_id'] . "'>";
                        echo "<img src='" . $playlist['caminho_imagem_playlist'] . "' alt='Capa da playlist " . $playlist['nome_playlist'] . "'>";
                        echo "<h3>" . $playlist['nome_playlist'] . "</h3></a>";
                        echo "</div>";
                    }
                }
                if (!$temPlaylist) {
                    echo "<p>Este usuário não possui playlists publicadas.</p>";
                }
            }
            ?>
        </div>
    </body>
</html>

<!-- FECHAR CONEXÃO COM O BANCO DE DADOS -->
<?php
$conn->close();
?>