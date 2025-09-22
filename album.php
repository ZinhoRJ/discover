<?php
//conexão com o banco de dados!
include './config.php'; //inclui o arquivo que tem a conexão com o banco de dados

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

                <!-- <h2><a href="perfil.php?id=<?php echo $id; ?>">
                            <img
                            src="<?php echo $pfp; ?>"
                            class="album-info-user-image"><?php echo $album['nome_usuario']; ?>
                        </a>
                </h2>-->
                
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

    <script src="./scripts/playerQueue.js"></script>
</body>

<script>

function carregarFaixas(id_album) {
    fetch('./scripts/getFaixas.php?album=' + id_album)
        .then(res => res.json()) //Converte para JSON 
        .then(faixas => {
            const container = document.getElementById('faixas-dinamicas');
            container.innerHTML = ''; // Limpa a div antes de adicionar

            let cont = 0;

            faixas.forEach(faixa => { //para cada faixa, criar uma div com um botão pra tocar a música
                cont++;

                const div = document.createElement('div');
                div.className = 'faixa-btn';
                //div.setAttribute('onclick', `playMusic('${faixa.id}')`);

                div.innerHTML = `
                    <div class='faixa-info-wrapper' onclick='loadAlbumQueue(${faixa.id})'>
                        <img class='play-btn' src='./svg/play-svgrepo-com.svg' alt=''>
                        <?php echo "<img class='faixa-img' src='" . $capa . "' alt='Capa do Álbum'>"; ?>
                        <h2> ${cont}. ${faixa.nome_musica}</h2>
                    </div>
                    <button class="btnAddFav" data-faixa-id='${faixa.id}'>
                        Adicionar Às Favoritas
                    </button>    
                `;
                container.appendChild(div);

                const hr = document.createElement('div');
                hr.innerHTML = `<div><hr></div>`
                container.appendChild(hr);  

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