<?php
include './config.php'; //inclui o arquivo que tem a conexão com o banco de dados

// Consultar as músicas no banco de dados (MAIS SIMPLES)
//$result = $conn->query("SELECT * FROM albuns"); <-- FOI REJEITADA
$sql = "SELECT albuns.id, albuns.nome_album, albuns.capa_album, usuarios.nome AS nome_artista
        FROM albuns
        JOIN usuarios ON albuns.id_usuario = usuarios.id";
$result = $conn->query($sql);
$resultado = $result->fetch_assoc();

$id_usuario = isset($_COOKIE["user_id"]) ? intval($_COOKIE["user_id"]) : 0; //procura o id do usuário. se estiver definido (isset) ent converte para INT e salva na variável, se não vai ser 0;
$sql_usuario_logado = "SELECT nome, foto_perfil, tipo_usuario FROM usuarios WHERE id = $id_usuario";
$result = $conn->query($sql_usuario_logado);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Discover</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>

<body>
    <div class="blur-overlay"></div>

    <section class="container">
        <div class="sidebar-left" style="align-items: space-between;">
            <h1 class="monotone-regular">DISCOVER</h1>
            <nav>
                <div class="btn-voltar">
                  <button class="btnEsquerda" style="background-color: green;" onclick="voltarIframe()">Voltar</button>
                </div>
                <button class="btnEsquerda" onclick="loadPage('discover.php')">Descobrir</button>
                <!--<button class="btnEsquerda" onclick="loadPage('discover.php')">Em Alta</button> -->
                <hr>
                <!-- <button class="btnEsquerda" onclick="loadPage('register.php')">Registrar</button>
                <button class="btnEsquerda" onclick="loadPage('login.php')">Login</button> -->

                <?php if (isset($user) && $user['tipo_usuario'] == 'admin') { ?>
                    <a href="./admin/admin.php"><button class="btnEsquerda">Painel do Administrador</button></a>
                <?php } ?>

                <?php if ($id_usuario != 0) { ?>
                    <button class="btnEsquerda" onclick="loadPage('playlist.php?id=<?php echo $id_usuario; ?>')">Favoritos</button>
                    <button class="btnEsquerda" onclick="loadPage('formulario.php')">Enviar Música</button>
                <?php } else { ?>
                    <a href="register.php"><button class="btnEsquerda">Registrar</button></a>
                    <a href="login.php"><button class="btnEsquerda">Login</button></a>
                <?php } ?>
                <hr>
                <?php //echo "<button class='btnEsquerda' onclick='loadPage(\"perfil.php?id=" . $id_usuario . "\")'>Perfil</button>"; ?>
                <button class="btnEsquerda" onclick="loadPage('perfil-listas.php')">Lista de Perfis</button>
            <?php 
            if ($id_usuario != 0) {
              ?>
              <div style="display: flex; align-items: center; gap: 10px; justify-content: space-between;">
                <img src="<?php echo $user["foto_perfil"]; ?>" alt="Foto de perfil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                <span style="font-weight: bold;">
                  <a href="#" onclick="loadPage('perfil.php?id=<?php echo $id_usuario; ?>')">
                    <?php echo $user["nome"]; ?>
                  </a>
                </span>
                
                  <a href="logout.php" class="btnLogout">
                    <span class="material-symbols-outlined ">exit_to_app</span>
                  </a>
              </div>
              <?php
            } else {
              echo "<p>Você não está logado!</p>";
            }
            ?>
            </div>

        <div class="main-frame">
            <iframe src="discover.php" name="conteudo" frameborder="0" id="main"></iframe>
        </div>

        <div class="sidebar-right">
            <h2></h2>

            <div class="music-player">
                <img id="player-cover" src="./uploads/padrao.jpg" alt="Capa de Álbum do Player">
                <audio id="player-audio" controls>
                    <source src="./" type="audio/mp3">
                    Seu navegador não suporta áudio HTML5.
                </audio>
                <div id="player-title">Nenhuma música tocando</div>
                <div id="player-artist">Artista</div>
            </div>
        </div>
    </section>

    <script>
    function loadPage(page) {
        document.querySelector("iframe").src =
        page; //vai retornar o primeiro elemento iframe da página, mas também poderia usar um id com o "document.getElementById"
    }

    function voltarIframe() {
        const iframe = document.getElementById('main');
        iframe.contentWindow.history.back();
    }
    </script>

    
</body>

</html>