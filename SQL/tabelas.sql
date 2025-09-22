CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    bio TEXT,
    foto_perfil VARCHAR(255)
);

CREATE TABLE albuns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_album VARCHAR(100) NOT NULL,
    id_usuario INT NOT NULL,
    genero VARCHAR(50),
    ano INT,
    descricao TEXT,
    capa_album VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE faixas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_album INT NOT NULL,
    nome_musica VARCHAR(100) NOT NULL,
    caminho_audio VARCHAR(255),
    FOREIGN KEY (id_album) REFERENCES albuns(id)
);

CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_playlist VARCHAR(255) NOT NULL,
    id_usuario INT NOT NULL,
    data_criacao DATE NOT NULL
);

CREATE TABLE playlist_faixas (
    id_playlist INT NOT NULL,
    id_faixa INT NOT NULL,
    PRIMARY KEY (id_playlist, id_faixa),
    FOREIGN KEY (id_playlist) REFERENCES playlists(id)
);

/*
id na tabela playlists é a chave primária e é auto-incrementada.

playlist_faixas funciona como uma tabela de associação (ideal para relacionamentos muitos-para-muitos).

A chave primária composta em playlist_faixas evita duplicatas da mesma faixa na mesma playlist.

FOREIGN KEY garante que id_playlist sempre se refira a uma playlist existente.
*/