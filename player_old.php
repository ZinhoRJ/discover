<!-- CONFIGURAÇÃO INICIAL DO BANCO DE DADOS -->
<?php
// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'fecip');
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}


// Consultar as músicas no banco de dados
$result = $conn->query("SELECT * FROM albuns");
?>


<!-- INICIO DO HTML -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player</title>
    <link rel="stylesheet" href="./css/player.css">
</head>

<body>
    <div class="container-buttons">
        <div class="buttons">
            <div>
                <!--
                BOTÃO PRA ENVIAR MÚSICAS
                -->
                <a href="./formulario.php"> <button class="button-enviar">ENVIAR MÚSICAS</button> </a>
            </div>
        </div>

        <div>
            <b><a href="player.php" style="font-size: larger; color: black">disco.ver</a></b>
        </div>

        <div style="display: flex">
            
            <!-- 
                VERIFICAR SE O USUÁRIO TÁ LOGADO PARA MOSTRAR O BOTÃO DE LOGIN/MOSTRAR O NOME DE USUÁRIO 
                -->
            <?php
            //mostrar os dados salvos posteriormente em cookies
            if (isset($_COOKIE['username'])) { //caso o cookie 'username' esteja definido (isset = is set) - isso significa que o usuário está logado.
                echo "<a href='./edit.php'> <button class='button-logar' style='width: auto;'>menu super secreto do admin</button> </a>";
                echo "<div class='perfil' style='padding-left: 10px'> <b>Usuário:</b> " . htmlspecialchars($_COOKIE['username']) . " <b>ID:</b> " . htmlspecialchars($_COOKIE['user_id']); //a função htmlspecialchars() impede exploit de XSS na hora de enviar os cookies.
                echo (" <a href='logout.php'><button class='button-logar' style='background-color: red; color: #fff; width: 50px;'>SAIR</button></a> </div>");
                
            } else {
                echo "<a href='./login.php'> <button class='button-logar'>FAZER LOGIN</button> </a>";
            }
            ?>
        </div>
    </div>


    <h1>Lista de Músicas</h1>

    <div class="music-list">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='music-item' onclick='playMusic(" . $row['id'] . ")'>
                    <img src='" . $row['capa_album'] . "' alt='Capa do Álbum'>
                    <h3>" . $row['nome_musica'] . "</h3>
                    <p>" . $row['nome_artista'] . "</p>
                </div>";
        }
        ?>
    </div>


    <!-- Recentes -->
    <h2>Recentes</h2>
    <div id="recentes-lista">        
        <?php 
        if (isset($_COOKIE['recentes'])) {
            $ids = explode(',', $_COOKIE['recentes']); // Obter os IDs salvos no cookie
            $idList = implode(',', array_map('intval', $ids)); // Sanitizar os IDs

            // Consultar as músicas recentes no banco de dados
            $query = "SELECT * FROM musicas WHERE id IN ($idList) ORDER BY FIELD(id, $idList)"; //o FIELD vai respeitar a ordem do array... segundo a internet...
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
        
        <!-- MOLDE: 
        <div class="recentes-item" onclick="playMusic('')">
            <img src="./uploads/padrao.jpg" alt="Capa Álbum">
            <p><b>Motherboard</b></p>
        </div>
    -->
    </div>

    
    
    <!-- Álbum TESTE -->




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
            Seu navegador não suporta o elemento de áudio.
        </audio>
    </div>



    <script>
        //FUNÇÃO DE REQUISIÇÃO AJAX PARA ATUALIZAR A LISTA DE MÚSICAS
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
                    if (music && music.caminho_audio && music.nome_musica && music.nome_artista && music.nome_album && music.capa_album) {
                        // Atualizar o player fixo com os dados da música
                        document.getElementById('player-audio').src = music.caminho_audio;
                        document.getElementById('player-title').innerText = music.nome_musica;
                        document.getElementById('player-artist').innerText = music.nome_artista + ' ● ' + music.nome_album;
                        document.getElementById('player-cover').src = music.capa_album;
                        // Reproduzir o áudio
                        document.getElementById('player-audio').play();


                        //SALVAR COOKIES PARA AS MÚSICAS RECENTES
                        musicId = musicId.toString(); //transformamos o ID em uma String para salvá-los nos cookies
                        
                        let recentes = getCookie("recentes"); //salva na variável "recentes" o valor dos cookies de "recente"
                        let ids = recentes ? recentes.split(",") : [];  //o "?" vai verificar se a variável é ou não vazia (null) e a função .split vai transformar em um array separado por vírgulas, esse array ficará salvo na variável ids
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
                .catch(error => console.error('Erro ao atualizar lista de recentes:', error)); //parecido com a verificação de response, mas retorna o erro da função e não a do HTTP.
        }


        // Função auxiliar para obter cookies
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(";").shift();
        }
    </script>

</body>

</html>

<!-- FECHAR CONEXÃO COM O BANCO DE DADOS -->
<?php
$conn->close();
?>