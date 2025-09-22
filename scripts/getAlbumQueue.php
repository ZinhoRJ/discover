<?php
include "../config.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Buscar faixa atual
    $stmt = $conn->prepare("SELECT id_album, id FROM faixas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faixaAtual = $result->fetch_assoc();

    if ($faixaAtual) {
        // Buscar faixas seguintes do mesmo álbum
        $stmt = $conn->prepare("SELECT id FROM faixas WHERE id_album = ? AND id > ? ORDER BY id ASC");
        $stmt->bind_param("ii", $faixaAtual['id_album'], $faixaAtual['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        $fila = [];
        while ($row = $result->fetch_assoc()) {
            $fila[] = $row['id'];
        }

        header('Content-Type: application/json');
        echo json_encode($fila);
    } else {
        echo json_encode(["error" => "Faixa não encontrada."]);
    }
}
?>