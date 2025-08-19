<?php
// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'fecip');
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Consultar as músicas no banco de dados (MAIS SIMPLES)
//$result = $conn->query("SELECT * FROM albuns"); <-- FOI REJEITADA
$sql = "SELECT albuns.id, albuns.nome_album, albuns.capa_album, usuarios.nome AS nome_artista
        FROM albuns
        JOIN usuarios ON albuns.id_usuario = usuarios.id";
$result = $conn->query($sql);
$resultado = $result->fetch_assoc();

$id_usuario = isset($_COOKIE["user_id"]) ? intval($_COOKIE["user_id"]) : 0; //procura o id do usuário. se estiver definido (isset) ent converte para INT e salva na variável, se não vai ser 0;
$sql_usuario_logado = "SELECT nome, foto_perfil FROM usuarios WHERE id = $id_usuario";
$result = $conn->query($sql_usuario_logado);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Discover</title>
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <section class="container">
    <div class="sidebar-left">
      <h1>Discover</h1>
      <nav>
        <button class="btnEsquerda" onclick="loadPage('discover.php')">Descobrir</button>
        <button class="btnEsquerda" onclick="loadPage('modelo.php')">Modelo</button>
        <button class="btnEsquerda" onclick="loadPage('playlist.php')">Favoritos</button>
        <button class="btnEsquerda" onclick="loadPage('discover.php')">Em Alta</button>
        <button class="btnEsquerda" onclick="loadPage('register.php')">Registrar</button>
        <button class="btnEsquerda" onclick="loadPage('login.php')">Login</button>
        <button class="btnEsquerda" style="background-color: red;"><a href="logout.php">SAIR</a></button>
        <button class="btnEsquerda" onclick="loadPage('formulario.php')">Enviar Música</button>
        <button class="btnEsquerda" onclick="loadPage('login.php')">Em Alta</button>
        <?php echo "<button class='btnEsquerda' onclick='loadPage(\"perfil.php?id=" . $id_usuario . "\")'>Perfil</button>"; ?>
      </nav>

      <?php 
        if($id_usuario != 0){
          echo "
  <div style='display: flex; align-items: center; gap: 10px;'>
    <img src='" . $user["foto_perfil"] . "' alt='Foto de perfil' style='width: 50px; height: 50px; border-radius: 50%;'>
    <span style='font-weight: bold;'>
      <a href=\"#\" onclick=\"loadPage('perfil.php?id=$id_usuario')\">
        " . $user["nome"] . "
      </a>
    </span>
  </div>
";
        } else {
          echo "<p>Você não está logado!</p>";
        }
        
      ?>
    </div>

    <div class="main-frame">
      <iframe src="discover.php" name="conteudo" frameborder="0"></iframe>
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
      document.querySelector("iframe").src = page; //vai retornar o primeiro elemento iframe da página, mas também poderia usar um id com o "document.getElementById"
    }
  </script>
</body>
</html>
