<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Músicas</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
    </style>

</head>
<body>
    <h1>
        Músicas Enviadas
    </h1>
</body>
</html>

<?php
    include './config.php'; //inclui o arquivo que tem a conexão com o banco de dados

    $musicas_sql = "SELECT * FROM musicas";
    $musicas_resultado = $conn -> query ($musicas_sql);

    if ($musicas_resultado != null) {
        echo "<ul>";
            while ($faixa = $musicas_resultado -> fetch_assoc()){
                echo "<li>" . $faixa["nome_musica"] . " -- " . $faixa["nome_album"] ." -- " . $faixa["nome_artista"] . " -- ID: " . $faixa["id"] . "</li>";
            };

        echo "</ul>";
    }

?>