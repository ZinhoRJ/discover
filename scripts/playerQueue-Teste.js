function playMusic(musicId) {
    // Fazer a requisição para obter os dados da música
    fetch(
            `./scripts/getMusicData.php?id=${musicId}`
            ) //fetch envia uma solicitação para o servidor, pega o arquivo getMusicData.php q tem o script com o ID, o "id=${musicID}" é quem passa a variável com o ID da música clicada.
        //nesse fetch é enviado o musicID para o arquivo getMusicData.php, nele o servidor vai receber a solicitação, consultar o banco de dados e retornar a música via JSON.
        .then(response => {
            if (!response.ok) { //se NÃO (!) der tudo certo, ele vai escrever o erro.
                throw new Error(
                    `Erro HTTP! Status: ${response.status}`
                    ); //o erro aqui é considerado um erro em HTTP, pois a solicitação ao protocolo n foi bem-sucedida
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
                        musicId
                        )) { //verifica se o array "ids" já tiver salvo o valor de musicId (a música que vc clicou) com a função "includes()" 
                    ids.unshift(
                        musicId
                        ); //então ele é adicionado ao início do array (e não ao final) usando a função unshift - é o contrário da função push
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

        const audio = parent.document.getElementById('player-audio');
        audio.onended = () => {
            playNextInQueue();
        };
}

function addToQueue(musicId) {
    if (!musicQueue.includes(musicId)) {
        musicQueue.push(musicId);
    }

    if (currentIndex === -1) {
        playNextInQueue();
    }
}

function playNextInQueue() {
    currentIndex++;

    if (currentIndex < musicQueue.length) {
        const nextId = musicQueue[currentIndex];
        playMusic(nextId);
    } else {
        currentIndex = -1;
    }
}