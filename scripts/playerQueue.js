// Fila de músicas e índice atual
let musicQueue = [];
let currentIndex = -1;

function loadAlbumQueue(startId) {
    fetch(`./scripts/getAlbumQueue.php?id=${startId}`)
        .then(response => response.json())
        .then(queue => {
            if (Array.isArray(queue)) {
                musicQueue = queue;
                currentIndex = -1;
                playMusic(startId); // Toca a faixa clicada
            } else {
                console.error("Erro ao carregar fila do álbum.");
            }
        })
        .catch(error => console.error("Erro ao buscar fila do álbum:", error));
}

// Adiciona uma música à fila
function addToQueue(musicId) {
    if (!musicQueue.includes(musicId)) {
        musicQueue.push(musicId);

        console.log(musicQueue);
    }

    if (currentIndex === -1) {
        playNextInQueue();
    }
}

// Toca a próxima música da fila
function playNextInQueue() {
    currentIndex++;

    if (currentIndex < musicQueue.length) {
        const nextId = musicQueue[currentIndex];
        playMusic(nextId);
    } else {
        currentIndex = -1; // Fila finalizada
    }
}

// Reproduz uma música específica
function playMusic(musicId) {
    fetch(`./scripts/getMusicData.php?id=${musicId}`)
        .then(response => {
            if (!response.ok) throw new Error(`Erro HTTP! Status: ${response.status}`);
            return response.json();
        })
        .then(music => {
            if (music && music.nome_musica && music.caminho_audio) {
                const audio = parent.document.getElementById('player-audio');
                parent.document.getElementById('player-title').innerText = music.nome_musica;
                parent.document.getElementById('player-artist').innerText = `${music.nome_usuario} ● ${music.nome_album}`;
                parent.document.getElementById('player-cover').src = music.capa_album || "./uploads/padrao.jpg";
                audio.src = music.caminho_audio;

                // Quando a música terminar, toca a próxima
                audio.onended = () => {
                    playNextInQueue();
                };

                audio.play();
            } else {
                console.error("Erro ao carregar música.");
            }
        })
        .catch(error => console.error('Erro ao buscar dados: ', error));
}
