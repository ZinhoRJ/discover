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
            if (music && music.id && music.nome_musica && music.caminho_audio) {
                // (music && music.caminho_audio && music.nome_musica && music.nome_artista && music.nome_album && music.capa_album) {
                //Atualizar o player fixo com os dados da música
                parent.document.getElementById('player-audio').src = music.caminho_audio;
                parent.document.getElementById('player-title').innerText = music.nome_musica;
                parent.document.getElementById('player-artist').innerText = "PLACEHOLDER"; //music.nome_artista + ' ● ' + music.nome_album;
                parent.document.getElementById('player-audio').play();
                parent.document.getElementById('player-cover').src = "./uploads/padrao.jpg";//music.capa_album;} else {
                console.error("Erro!");
            }
        })
        .catch(error => console.error('Erro ao buscar dados: ', error));
}