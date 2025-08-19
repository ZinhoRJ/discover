<!-- CONFIGURAÇÃO INICIAL DO BANCO DE DADOS -->
<?php
// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'fecip');
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Consultar as músicas no banco de dados (MAIS SIMPLES)
//$result = $conn->query("SELECT * FROM albuns"); <-- FOI REJEITADA
$sqlAlbuns = "SELECT albuns.id, albuns.nome_album, albuns.capa_album, usuarios.nome AS nome_artista
        FROM albuns
        JOIN usuarios ON albuns.id_usuario = usuarios.id";
$resultAlbuns = $conn->query($sqlAlbuns);

$sqlArtistas = "SELECT * FROM usuarios ORDER BY id ASC";
$resultArtistas = $conn->query($sqlArtistas);

$sqlPlaylists = "SELECT playlists.id, playlists.nome_playlist, usuarios.nome AS nome_usuario
    FROM playlists
    JOIN usuarios ON playlists.id_usuario = usuarios.id";
$resultPlaylists = $conn->query($sqlPlaylists);
?>


<!-- INICIO DO HTML -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player</title>
    <link rel="stylesheet" href="./css/discover.css">
</head>

<body>
    <!-- <?php //include './includes/header.php'; ?> Incluir Header do Arquivo Separado -->

    <h1>Lista de Músicas</h1>

    <div class="music-list">
        <?php
        while ($row = $resultAlbuns->fetch_assoc()) {
            $album = htmlspecialchars($row['nome_album']);
            $artista = htmlspecialchars($row['nome_artista']);
            $idAlbum = htmlspecialchars($row['id']);
            $capa = !empty($row['capa_album']) ? $row['capa_album'] : 'padrao.png';
            
            echo "<div class='music-item'>
                    <img src='" . $capa . "' alt='Capa do Álbum'>
                    <a href='album.php?id=" . $idAlbum . "'> <h3>" . $album . "</h3> </a>
                    <p>" . $artista . "</p>
                </div>";
        }
        ?>
    </div>


    <!-- Recentes 
    <h2>Recentes</h2>
    <div id="recentes-lista">
        <?php 
        if (isset($_COOKIE['recentes'])) {
            $ids = explode(',', $_COOKIE['recentes']); // Obter os IDs salvos no cookie
            $idList = implode(',', array_map('intval', $ids)); // Sanitizar os IDs

            // Consultar as músicas recentes no banco de dados
            $query = "SELECT * FROM faixas WHERE id IN ($idList) ORDER BY FIELD(id, $idList)"; //o FIELD vai respeitar a ordem do array... segundo a internet...
            $resultRecentes = $conn->query($query);

            if ($resultRecentes && $resultRecentes->num_rows > 0) {
                while ($row = $resultRecentes->fetch_assoc()) {
                    echo "<div class='recentes-item' onclick='playMusic(" . $row['id'] . ")'>
                <img src='" . $row['capa_album'] . "' alt='Capa Álbum'>
                <p><b>" . $row['nome_musica'] . "</b></p>
              </div>";
                }
            } else {
                echo "<p>Nenhuma música recente encontrada.</p>";
            }
        } else {
            echo "<p id='recentes-nada'>Nenhuma música recente tocada ainda.</p>";
        }
        ?>
    </div>-->

    <!-- Usuários -->
    <h1>Usuários</h1>
    <div class="music-list" id="users">
        <?php
        while ($row = $resultArtistas->fetch_assoc()) {
            $nome = htmlspecialchars($row['nome']);
            $id = htmlspecialchars($row['id']);
            $pfp = !empty($row['foto_perfil']) ? $row['foto_perfil'] : 'padrao.png';
            
            echo "<div class='music-item''>
                    <img src='" . $pfp . "' alt='Capa do Álbum' style='border-radius: 100%;'>
                    <a href='perfil.php?id=" . $id . "'> <h3>" . $nome . "</h3> </a>
                </div>";
        }
        ?>
    </div>

    <h1>Playlists</h1>

    <div class="music-list">
        <?php
        while ($row = $resultPlaylists->fetch_assoc()) {
            $playlist = htmlspecialchars($row['nome_playlist']);
            $usuario = htmlspecialchars($row['nome_usuario']);
            $idPlaylist = htmlspecialchars($row['id']);
            
            echo "<div class='music-item'>
                    <img src='./uploads/padrao.jpg' alt='Capa do Álbum'>
                    <a href='playlist.php?id=" . $idPlaylist . "'> <h3>" . $playlist . "</h3> </a>
                    <p>" . $usuario . "</p>
                </div>";
        }
        ?>
    </div>

    <script>
    function playMusic(musicId) {
        // Fazer a requisição para obter os dados da música
        fetch(`./scripts/getMusicData.php?id=${musicId}`) //fetch envia uma solicitação para o servidor, pega o arquivo getMusicData.php q tem o script com o ID, o "id=${musicID}" é quem passa a variável com o ID da música clicada.
            //nesse fetch é enviado o musicID para o arquivo getMusicData.php, nele o servidor vai receber a solicitação, consultar o banco de dados e retornar a música via JSON.
            .then(response => {
                if (!response.ok) { //se NÃO (!) der tudo certo, ele vai escrever o erro.
                    throw new Error(
                    `Erro HTTP! Status: ${response.status}`); //o erro aqui é considerado um erro em HTTP, pois a solicitação ao protocolo n foi bem-sucedida
                }
                return response
            .json(); //o JSON é transformado em um objeto javascript chamado "response", pra gente pode manipular as informações.
            })
            .then(music => {
                // Verificar se os dados retornados são válidos
                if (music && music.caminho_audio && music.nome_musica && music.nome_artista && music.nome_album &&
                    music.capa_album) {
                    // Atualizar o player fixo com os dados da música
                    document.getElementById('player-audio').src = music.caminho_audio;
                    document.getElementById('player-title').innerText = music.nome_musica;
                    document.getElementById('player-artist').innerText = music.nome_artista + ' ● ' + music
                        .nome_album;
                    document.getElementById('player-cover').src = music.capa_album;
                    // Reproduzir o áudio
                    document.getElementById('player-audio').play();

                    //SALVAR COOKIES PARA AS MÚSICAS RECENTES
                    musicId = musicId.toString(); //transformamos o ID em uma String para salvá-los nos cookies

                    let recentes = getCookie(
                    "recentes"); //salva na variável "recentes" o valor dos cookies de "recente"
                    let ids = recentes ? recentes.split(",") :[]; //o "?" vai verificar se a variável é ou não vazia (null) e a função .split vai transformar em um array separado por vírgulas, esse array ficará salvo na variável ids
                    //Os dois pontos depois do ".split(",")" representa "OU", então se a variável "recentes" estiver vazia, ele somente irá iniciar um array vazio "[]" (e não formatado pelo .split).

                    // Adicionar o novo ID e evitar duplicados
                    if (!ids.includes(musicId)) { //verifica se o array "ids" já tiver salvo o valor de musicId (a música que vc clicou) com a função "includes()" 
                        ids.unshift(musicId); //então ele é adicionado ao início do array (e não ao final) usando a função unshift - é o contrário da função push
                    }

                    // Limitar a lista a 5 IDs
                    ids = ids.slice(0, 5); //a função slice corta qualquer elemento que for maior que o quinto

                    // Atualizar o cookie
                    document.cookie = "recentes=" + ids.join(",") + "; path=/"; //os elementos do array "ids" são convertidos de volta para uma string, e a função .join(",") vai separar eles por vírulas. Logo depois, é salvo no cookie "recentes"

                    updateRecentes(); //chama a função.
                } else {
                    console.error('Erro: Dados incompletos ou inválidos retornados do servidor.');
                }
            })
            .catch(error => console.error('Erro ao buscar dados da música:', error));
    }

    // Função para obter e exibir músicas recentes
    function updateRecentes() {
        //o fetch serve para chamar um script ou API, e estamos chamando o script updateRecentes.php
        fetch('./scripts/getRecentes.php') // Endpoint para consultar músicas recentes no servidor
            .then(response => {
                if (!response.ok) { //se a resposta do servidor NÃO (!) for ok, jogamos o código de erro HTTP
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }
                return response.text(); // Receber a lista como HTML, para sabermos melhor do bug que tá ocorrendo
            })
            .then(html => { //a variável "html" foi definida dentro do script, e aqui retornamos ela
                document.getElementById('recentes-lista').innerHTML = html; // Atualizar o conteúdo da lista
                //pegamos o elemento "recentes-lista" e já modificamos o html com a resposta usando o ".innerHTML".
            })
            .catch(error => console.error('Erro ao atualizar lista de recentes:',
            error)); //parecido com a verificação de response, mas retorna o erro da função e não a do HTTP.
    }


    function getCookie(nome) {
        let cookies = document.cookie.split(";");

        for (let i = 0; i < cookies.length; i++) {
            let c = cookies[i].trim();
            if (c.startsWith(nome + "=")) {
                return c.substring((nome + "=").length);
            }
        }

        return null;
    }
    </script>

</body>

</html>

<!-- FECHAR CONEXÃO COM O BANCO DE DADOS -->
<?php
$conn->close();
?>