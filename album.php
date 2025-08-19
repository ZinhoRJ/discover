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
?>

<html>

<head>
    <title><?php echo $album['nome_album']; ?></title>
    <link rel="stylesheet" href="./css/album.css">
</head>

<body onload="carregarFaixas(<?php echo $id; ?>)">
    <!-- INFORMAÇÕES DO ÁLBUM -->
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

    <!-- FAIXAS -->
    <div id="faixas-dinamicas" class="faixas-container">
        <!-- Vão ser adicionadas de forma dinâmica com a função "adicionarFaixas()" -->
    </div>

    <!-- FOOTER -->
    <?php include './includes/footer.php'; ?>
</body>

<script>
function carregarFaixas(id_album) {
    fetch('./scripts/getFaixas.php?album=' + id_album)
        .then(res => res.json()) //Converte para JSON 
        .then(faixas => {
            const container = document.getElementById('faixas-dinamicas');
            container.innerHTML = ''; // Limpa a div antes de adicionar

            faixas.forEach(faixa => { //para cada faixa, criar uma div com um botão pra tocar a música
                const div = document.createElement('div');
                div.className = 'faixa-btn';
                //div.setAttribute('onclick', `playMusic('${faixa.id}')`);
                div.innerHTML = `
                    <div class='faixa-info-wrapper' onclick='playMusic(${faixa.id})'>
                        <img class='play-btn' src='./svg/play-svgrepo-com.svg' alt=''>
                        <?php echo "<img class='faixa-img' src='" . $capa . "' alt='Capa do Álbum'>"; ?>
                        <h2>${faixa.nome_musica}</h2>
                    </div>
                    <button class="btnAddFav" data-faixa-id='${faixa.id}'>
                        Adicionar Às Favoritas
                    </button>
                `;
                container.appendChild(div);

                // ADICIONAR EVENTO PARA O BOTÃO DE FAVORITAR À CADA MÚSICA
                const botaoFav = div.querySelector('.btnAddFav');
                botaoFav.addEventListener('click', () => {
                    const faixaId = botaoFav.dataset.faixaId;

                    fetch('./scripts/favoritar.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id_faixa=' + encodeURIComponent(faixaId)
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log("Resposta do servidor:", data);
                            alert(data);
                        })
                        .catch(error => {
                            console.error('Erro ao favoritar:', error);
                        });
                });
            });
        });
}

function playMusic(musicId) {
    // Fazer a requisição para obter os dados da música
    fetch(
        `./scripts/getMusicData.php?id=${musicId}`) //fetch envia uma solicitação para o servidor, pega o arquivo getMusicData.php q tem o script com o ID, o "id=${musicID}" é quem passa a variável com o ID da música clicada.
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
            if (music && music.nome_musica && music.caminho_audio) { // Verificar se os dados retornados são válidos
                //Atualizar o player fixo com os dados da música
                parent.document.getElementById('player-audio').src = music.caminho_audio;
                parent.document.getElementById('player-title').innerText = music.nome_musica;
                parent.document.getElementById('player-artist').innerText =
                    `${music.nome_usuario} ● ${music.nome_album}`;
                parent.document.getElementById('player-cover').src = music.capa_album || "./uploads/padrao.jpg";

                parent.document.getElementById('player-audio').play(); //inicia a música

                //SALVAR COOKIES PARA AS MÚSICAS RECENTES
                musicId = musicId.toString(); //transformamos o ID em uma String para salvá-los nos cookies

                let recentes = getCookie(
                "recentes"); //salva na variável "recentes" o valor dos cookies de "recente"
                let ids = recentes ? recentes.split(",") :
            []; //o "?" vai verificar se a variável é ou não vazia (null) e a função .split vai transformar em um array separado por vírgulas, esse array ficará salvo na variável ids
                //Os dois pontos depois do ".split(",")" representa "OU", então se a variável "recentes" estiver vazia, ele somente irá iniciar um array vazio "[]" (e não formatado pelo .split).

                // Adicionar o novo ID e evitar duplicados
                if (!ids.includes(
                    musicId)) { //verifica se o array "ids" já tiver salvo o valor de musicId (a música que vc clicou) com a função "includes()" 
                    ids.unshift(
                    musicId); //então ele é adicionado ao início do array (e não ao final) usando a função unshift - é o contrário da função push
                }

                // Limitar a lista a 5 IDs
                ids = ids.slice(0, 5); //a função slice corta qualquer elemento que for maior que o quinto

                // Atualizar o cookie
                document.cookie = "recentes=" + ids.join(",") +
                "; path=/"; //os elementos do array "ids" são convertidos de volta para uma string, e a função .join(",") vai separar eles por vírulas. Logo depois, é salvo no cookie "recentes"

                //updateRecentes(); //chama a função.
            } else {
                console.error("Erro!");
            }
        })
        .catch(error => console.error('Erro ao buscar dados: ', error));
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

</html>

<!-- FECHAR CONEXÃO COM O BANCO DE DADOS -->
<?php
$conn->close();
?>