<?php
include('../config.php'); // inclui o arquivo que tem a conexão com o banco de dados

if (!isset($_GET['id'])) {
    header('Location: ../admin/admin.php');
    exit;
}

$id = intval($_GET['id']);

// Buscar capa do álbum e excluir arquivo físico
$stmt_album = $conn->prepare("SELECT capa_album FROM albuns WHERE id = ?");
$stmt_album->bind_param("i", $id);
$stmt_album->execute();
$result_album = $stmt_album->get_result();
if ($album = $result_album->fetch_assoc()) {
    if (!empty($album['capa_album']) && file_exists($album['capa_album'])) {
        unlink($album['capa_album']);
    }
}
$stmt_album->close();

// Buscar faixas do álbum e excluir arquivos físicos
$stmt_faixas = $conn->prepare("SELECT id, caminho_audio FROM faixas WHERE id_album = ?");
$stmt_faixas->bind_param("i", $id);
$stmt_faixas->execute();
$result_faixas = $stmt_faixas->get_result();
$faixa_ids = [];
while ($faixa = $result_faixas->fetch_assoc()) {
    if (!empty($faixa['caminho_audio']) && file_exists($faixa['caminho_audio'])) { //caso não esteja vazio e o arquivo exista...
        unlink($faixa['caminho_audio']); //apaga o arquivo físico
    }
    $faixa_ids[] = $faixa['id'];
}
$stmt_faixas->close();

// Apagar referências das faixas nas playlists (playlist_faixas)
if (!empty($faixa_ids)) {
    $in = implode(',', array_fill(0, count($faixa_ids), '?'));
    $types = str_repeat('i', count($faixa_ids));
    $stmt_del_pf = $conn->prepare("DELETE FROM playlist_faixas WHERE id_faixa IN ($in)");
    $stmt_del_pf->bind_param($types, ...$faixa_ids);
    $stmt_del_pf->execute();
    $stmt_del_pf->close();
}

// Apagar faixas do álbum
$stmt_del_faixas = $conn->prepare("DELETE FROM faixas WHERE id_album = ?");
$stmt_del_faixas->bind_param("i", $id);
$stmt_del_faixas->execute();
$stmt_del_faixas->close();

// Apagar o álbum
$stmt_del_album = $conn->prepare("DELETE FROM albuns WHERE id = ?");
$stmt_del_album->bind_param("i", $id);
$stmt_del_album->execute();
$stmt_del_album->close();

header('Location: ../admin/admin.php');
exit();
?>