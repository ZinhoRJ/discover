<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Áudio</title>
</head>
<body>
    <h1 style="font-family: Arial, Helvetica, sans-serif">Enviar Arquivo de Áudio</h1>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="audio" accept="audio/*" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Diretório de destino para salvar os arquivos
    $diretorioDestino = 'uploads/';

    // Verifica se o diretório existe, senão, cria
    if (!is_dir($diretorioDestino)) {
        mkdir($diretorioDestino, 0777, true);
    }

    // Caminho completo do arquivo enviado
    $arquivoDestino = $diretorioDestino . basename($_FILES['audio']['name']);

    // Move o arquivo enviado para o diretório de destino
    if (move_uploaded_file($_FILES['audio']['tmp_name'], $arquivoDestino)) {
        echo "Arquivo enviado com sucesso! <a href='$arquivoDestino'>Tocar Áudio</a>";
    } else {
        echo "Erro ao enviar o arquivo.";
    }
}
?>