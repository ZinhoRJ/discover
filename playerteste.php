<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player de Som</title>
    <style>
        .player-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .album-cover {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .audio-player {
            flex-grow: 1;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 20px;
        }
    </style>
</head>

    <body>
        <h1>Player de Áudio Com o PHP</h1>
    </body>
</html>

<?php
    require_once('getid3/getid3.php'); // Caminho para a biblioteca getID3

    $getID3 = new getID3();
    $diretorio = 'uploads/';
    $arquivos = scandir($diretorio);


    foreach ($arquivos as $arquivo) {
        if (pathinfo($arquivo, PATHINFO_EXTENSION) == "flac") {
            // caminho do arquivo de áudio
            $caminhoAudio = $diretorio . $arquivo;

            //caminho da imagem de capa
            $nomeCapa = pathinfo($arquivo, PATHINFO_FILENAME) . '.jpg';
            $caminhoCapa = file_exists($diretorio . $nomeCapa) ? $diretorio . $nomeCapa : 'padrao.jpg'; // "padrao.jpg" é a imagem padrão se a capa não existir


            echo " <div class='player-container'>
                        <audio controls>
                            <source src='$diretorio$arquivo' type='audio/mpeg'>
                            Seu navegador não suporta o elemento de áudio.
                        </audio>
                    </div>";
            
            //echo "<p>$arquivo</p>";
        }
    }
?>