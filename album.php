<?php
//conexão com o banco de dados!
$conn = new mysqli("localhost", "root", "", "fecip");
if ($conn->connect_error) {
    die("Erro ao conectar com o Banco de Dados!" . $conn->connect_error);
}

//Variável que guarda salva o ID como o da URL (lá do, "album.php?id=7")
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
  SELECT a.nome_album, a.ano, a.capa_album, a.descricao, u.nome AS artista
  FROM albuns a
  JOIN usuarios u ON a.id_usuario = u.id
  WHERE a.id = ?
");
$stmt->bind_param("i", $id); //dizer que o "?" na query SQL é igual à variável $id
$stmt->execute();

$result = $stmt->get_result();
$album = $result->fetch_assoc();


$capa = !empty($album['capa_album']) ? $album['capa_album'] : 'padrao.png'; //Se o álbum tiver uma capa definida, use essa capa. Caso contrário, use a imagem padrão ‘padrao.png’.
/*if ($album) {
  echo "<h1>" . $album['nome_album'] . "</h1>";
  echo "<p>Artista: " . $album['artista'] . "</p>";
  echo "<p>Ano: " . $album['ano'] . "</p>";
  echo "<img src='" . $capa . "' alt='Capa do Álbum'>";
  echo "<p>" . $album['descricao'] . "</p>";
} else {
  echo "<p>Álbum não encontrado.</p>";
}*/
?>

<html>

<head>
    <title><?php echo $album['nome_album']; ?></title>
    <link rel="stylesheet" href="./css/album.css">
</head>

<body onload="carregarFaixas(<?php echo $id; ?>)">
    <!--
        HEADER
        -->
    <?php include './includes/header.php'; ?>


    <!--
        INFORMAÇÕES DO ÁLBUM
        -->
    <div class="container">
        <div class="album-info">
            <?php echo "<img src='" . $capa . "' alt='Capa do Álbum'>"; ?>
            <div class="texto-info">
                <?php echo "<h1>" . $album['nome_album'] . "</h1>"; ?>
                <?php echo "<h2>" . $album['ano'] . "</h2>"; ?>

                <?php echo "<p>" . $album['descricao'] . "</p>"; ?>
            </div>
        </div>
    </div>

    <!--
        FAIXAS
        -->
    <div id="faixas-dinamicas" class="faixas-container">
        <div class="faixa-btn" onclick="playMusic()">
            <img class="play-btn" src="./svg/play-svgrepo-com.svg" alt="">
            <!-- <img class="faixa-img" src="./uploads/padrao.jpg" alt=""> -->
            <?php echo "<img class='faixa-img' src='" . $capa . "' alt='Capa do Álbum'>"; ?>

            <h2><a href="">Teste</a></h2>
        </div>
    </div>


    <!-- Player fixo -->
    <div id="music-player">
        <div class="info">
            <img id="player-cover" src="./uploads/padrao.jpg" alt="Capa">
            <div>
                <h3 id="player-title">Nenhuma música tocando</h3>
                <p id="player-artist"></p>
            </div>
        </div>
        <audio id="player-audio" controls>
            <source id="player-source" src="" type="audio/mpeg">
            <h1>"Seu navegador não suporta o elemento de áudio."</h1>
        </audio>
    </div>
    <!--
        FOOTER
        -->
    <?php include './includes/footer.php'; ?>

    <div style="height: 90px; "id="bottom-spacer"></div>
</body>

<script>
    function carregarFaixas(id_album) {
        fetch('./scripts/getFaixas.php?album=' + id_album)
            .then(res => res.json()) //Converte para JSON 
            .then(faixas => {
                const container = document.getElementById('faixas-dinamicas');
                container.innerHTML = ''; // Limpar antes de adicionar

                faixas.forEach(faixa => {
                    const div = document.createElement('div');
                    div.className = 'faixa-btn';
                    div.setAttribute('onclick', `playMusic('${faixa.id}')`);
                    div.innerHTML = `
                    <?php echo "
                    <img class='play-btn' src='./svg/play-svgrepo-com.svg' alt=''>
                    <img class='faixa-img' src='" . $capa . "' alt='Capa do Álbum'>"; ?>
                    <h2>${faixa.nome_musica}</h2>
                `;
                    container.appendChild(div);
                });
            });
    }

    function playMusic(musicId) {
            // Fazer a requisição para obter os dados da música
            fetch(`./scripts/getMusicData.php?id=${musicId}`) //fetch envia uma solicitação para o servidor, pega o arquivo getMusicData.php q tem o script com o ID, o "id=${musicID}" é quem passa a variável com o ID da música clicada.
                //nesse fetch é enviado o musicID para o arquivo getMusicData.php, nele o servidor vai receber a solicitação, consultar o banco de dados e retornar a música via JSON.
                .then(response => {
                    if (!response.ok) { //se NÃO (!) der tudo certo, ele vai escrever o erro.
                        throw new Error(`Erro HTTP! Status: ${response.status}`); //o erro aqui é considerado um erro em HTTP, pois a solicitação ao protocolo n foi bem-sucedida
                    }
                    return response.json(); //o JSON é transformado em um objeto javascript chamado "response", pra gente pode manipular as informações.
                })
                .then(music => {
                    // Verificar se os dados retornados são válidos
                    if (music && music.id && music.nome_musica && music.caminho_audio){
                    // (music && music.caminho_audio && music.nome_musica && music.nome_artista && music.nome_album && music.capa_album) {
                        // Atualizar o player fixo com os dados da música
                        document.getElementById('player-audio').src = music.caminho_audio;
                        document.getElementById('player-title').innerText = music.nome_musica;
                        document.getElementById('player-artist').innerText = "PLACEHOLDER";//music.nome_artista + ' ● ' + music.nome_album;
                        document.getElementById('player-cover').src = "./uploads/padrao.jpg";//music.capa_album;
                        // Reproduzir o áudio
                        document.getElementById('player-audio').play();
                    } else {
                        console.error("Erro!");
                    }
                })
                .catch(error => console.error('Erro ao buscar dados: ', error));
            }
</script>

<style>
    /* Estilo do player fixo */
    #music-player {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #1e1e1e;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }

    #music-player img {
        width: 60px;
        height: 60px;
        border-radius: 5px;
        object-fit: cover;
        margin-right: 15px;
        overflow: hidden;
    }

    #music-player .info {
        display: flex;
        align-items: center;
    }

    #music-player audio {
        width: 700px;
        margin-right: 40px;
    }
</style>

</html>

<!-- FECHAR CONEXÃO COM O BANCO DE DADOS -->
<?php
$conn->close();
?>