<?php
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscar faixa
    $stmt = $conn->prepare("SELECT id, nome_musica, caminho_audio, id_album FROM faixas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faixa = $result->fetch_assoc();

    if ($faixa) {
        // Buscar álbum
        $stmt = $conn->prepare("SELECT nome_album, capa_album, id_usuario FROM albuns WHERE id = ?");
        $stmt->bind_param('i', $faixa['id_album']);
        $stmt->execute();
        $result = $stmt->get_result();
        $album = $result->fetch_assoc();

        // Buscar usuário
        $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
        $stmt->bind_param('i', $album['id_usuario']);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        // Montar o retorno
        $retorno = [
            'id' => $faixa['id'],
            'nome_musica' => $faixa['nome_musica'],
            'caminho_audio' => $faixa['caminho_audio'],
            'nome_album' => $album['nome_album'],
            'capa_album' => $album['capa_album'],
            'nome_usuario' => $usuario['nome']
        ];

        header('Content-Type: application/json');
        echo json_encode($retorno);
    } else {
        echo json_encode(["error" => "Faixa não encontrada."]);
    }
}
?>