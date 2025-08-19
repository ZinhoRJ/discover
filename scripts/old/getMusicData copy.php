<?php
$conn = new mysqli('localhost', 'root', '', 'fecip');
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    //$stmt = $conn->prepare("SELECT id, nome_musica, caminho_audio FROM faixas WHERE id = ?");
    $stmt = $conn->prepare("
        SELECT 
            faixas.id AS id_faixa,
            faixas.nome_musica,
            faixas.caminho_audio,
            albuns.nome_album,
            albuns.capa_album,
            usuarios.nome AS nome_usuario
        FROM faixas
        INNER JOIN albuns ON faixas.id_album = albuns.id
        INNER JOIN usuarios ON albuns.id_usuario = usuarios.id
        WHERE faixas.id = ?
    ");
    
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $music = $result->fetch_assoc();
    
    //echo json_encode($music);
    if ($music) {
        header('Content-Type: application/json'); //com isso, ele só envia o JSON, sem nenhum html misturado!
        echo json_encode($music); 
    } else {
        echo json_encode(["error" => "Faixa não encontrada."]);
    }
}
?>