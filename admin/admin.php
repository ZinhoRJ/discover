<?php
session_start();
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if (!isset($_SESSION['usuario_admin']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login-admin.php");
    exit();
}   

$stmt = $conn->prepare("SELECT id, nome, email, bio, foto_perfil FROM usuarios ORDER BY id DESC;");
$stmt->execute();
$resultadoUsuarios = $stmt->get_result();

$stmt = $conn->prepare("SELECT id, nome_album, capa_album FROM albuns ORDER BY id DESC;");
$stmt->execute();
$resultadoAlbuns = $stmt->get_result();

$stmt = $conn->prepare("SELECT id, nome_playlist, caminho_imagem_playlist FROM playlists ORDER BY id DESC;");
$stmt->execute();
$resultadoPlaylists = $stmt->get_result();
?>

<html>
    <head>
        <link rel="stylesheet" href="../css/admin/admin.css">
    </head>
    <body>
        <div class="painel-superior">
            <h1>Painel do Administrador</h1>
            <h2>Administrador Atual: <?php echo $_SESSION['nome']; ?></h2>
            <button style="background-color: red; color: white;" onclick="window.location.href='logout-admin.php'">LOG-OUT</button>
            <button onclick="window.location.href='../index.php'">Voltar ao Discover</button>
        </div>

        <p style="font-size: larger"><b>CUIDADO: todas as mudanças são permanentes, prossiga com cautela!</b> Para pesquisar por um item, aperte o botão F3 do teclado.</p>

        <div class="usuarios">
            <h1>Usuários</h1>
            <?php
                while($usuarios = $resultadoUsuarios->fetch_assoc()){
                    if ($usuarios["nome"]){
                        $div = "<div class='usuario-lista'>";
                        $div .= "<h1><a href='../perfil.php?id=" . $usuarios['id'] . "'>" . $usuarios['nome'] . "</a><h1>";
                        $div .= "<button onclick=\"window.location.href='../admin/editar-usuario.php?id=" . $usuarios['id'] . "'\">Editar</button>";
                        $div .= "<button><a href='../admin/excluir-usuario.php?id=" . $usuarios['id'] . "'> Excluir </a></button>";
                        $div .= "</div>";

                        echo $div;
                    }
                }
            ?>
        </div>

        <break>

        <break>
        <div class="albuns">
            <h1>Álbuns</h1>
            <?php
                while($albuns = $resultadoAlbuns->fetch_assoc()){
                    if ($albuns["nome_album"]){
                        $div = "<div class='album-lista'>";
                        $div .= "<h1><a href='../album.php?id=" . $albuns['id'] . "'>" . $albuns['nome_album'] . "</a><h1>";
                        $div .= "<button onclick=\"window.location.href='../admin/editar-album.php?id=" . $albuns['id'] . "'\">Editar</button>";
                        $div .= "<button><a href='../admin/excluir-album.php?id=" . $albuns['id'] . "'> Excluir </a></button>";
                        $div .= "</div>";

                        echo $div;
                    }
                }
            ?>
        </div>

        <br>

        <div class="playlists">
            <h1>Playlists</h1>
            <?php
                while($playlists = $resultadoPlaylists->fetch_assoc()){
                    if ($playlists["nome_playlist"]){
                        $div = "<div class='playlist-lista'>";
                        $div .= "<h1><a href='../playlist.php?id=" . $playlists['id'] . "'>" . $playlists['nome_playlist'] . "</a><h1>";
                        $div .= "<button onclick=\"window.location.href='../admin/editar-playlist.php?id=" . $playlists['id'] . "'\">Editar</button>";
                        $div .= "<button><a href='../admin/excluir-playlist.php?id=" . $playlists['id'] . "'> Excluir </a></button>";
                        $div .= "</div>";

                        echo $div;
                    }
                }
            ?>
        </div>
    </body>


<script>
    // Adiciona confirmação antes de excluir um usuário ou álbum
    document.querySelectorAll('.usuario-lista button a, .album-lista button a, .playlist-lista a').forEach(function(link) {
        link.addEventListener('click', function(event) {
            if (!confirm('Tem certeza que deseja excluir este item? Esta ação é permanente.')) {
                event.preventDefault();
            }
        });
    });
</script>
</html>