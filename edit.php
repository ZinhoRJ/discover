<?php
$conn = new mysqli("localhost", "root", "", "fecip");

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM musicas");
?>

<html lang="pt-BR">
<style>
    body {
        background: linear-gradient(to left, #0052d4, #4364f7, #6fb1fc);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
    img{
        width: 100px;
    }
</style>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar</title>
</head>

<body>
    <h1>EDITAR MÚSICA</h1>

    <label for="msc" style="font-size: large">ID DA MÚSICA: </label>
    
    <select name="msc" id="musica" onchange="atualizarInfo()">
        <?php
        while($row = $result -> fetch_assoc())
            echo "<option value='" . $row['id'] . "'>" . $row['nome_musica'] . "</option>";
        ?>
    </select>

    

    <!--
    <select name="msc">
        <option value="">A</option>
    </select>
    -->

    <p></p>

    <fieldset>
        <legend>Informações da Música: </legend>
        <!-- O formulário abaixo vai enviar as informações de cada campo via método POST -->
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <label for="imagem_capa">Imagem de Capa: </label>
            <input type="file" name="imagem_capa" id="imagem_capa" accept="image/*" required><br><br>

            <label for="nome_musica">Nome da Música:</label>
            <input type="text" name="nome_musica" id="nome_musica" required><br><br>

            <label for="nome_album">Nome do Álbum:</label>
            <input type="text" name="nome_album" id="nome_album"><br><br>

            <label for="nome_artista">Nome do Artista:</label>
            <input type="text" name="nome_artista" id="nome_artista"><br><br>

            <label for="genero">Gêneros (separe por vírgulas):</label>
            <input type="text" name="genero" id="genero"><br><br>

            <label for="audio">Arquivos de Áudio:</label>
            <input type="file" name="audio[]" id="audio" accept="audio/*" multiple required><br><br>

            <button type="submit">Enviar</button>
            :: <a href="./player.php"><b>Voltar à Lista de Músicas</b></a>
        </form>
    </fieldset>

    

</body>
</html>