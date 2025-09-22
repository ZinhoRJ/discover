<?php
include('../config.php');

if (!isset($_GET['id'])) {
    header('Location: ../admin/admin.php');
    exit;
}

$id = intval($_GET['id']);

// Apagar foto de perfil do usuário
$stmt_usuario = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmt_usuario->bind_param("i", $id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
if ($usuario = $result_usuario->fetch_assoc()) {
    if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])) {
        unlink($usuario['foto_perfil']);
    }
}
$stmt_usuario->close();

// Apagar arquivos de álbuns e faixas do usuário
$stmt_albuns = $conn->prepare("SELECT id, capa_album FROM albuns WHERE id_usuario = ?");
$stmt_albuns->bind_param("i", $id);
$stmt_albuns->execute();
$result_albuns = $stmt_albuns->get_result();

while ($album = $result_albuns->fetch_assoc()) {
    $album_id = intval($album['id']);
    // Apagar capa do álbum
    if (!empty($album['capa_album']) && file_exists($album['capa_album'])) {
        unlink($album['capa_album']);
    }
    // Apagar arquivos de faixas
    $stmt_faixas = $conn->prepare("SELECT caminho_audio FROM faixas WHERE id_album = ?");
    $stmt_faixas->bind_param("i", $album_id);
    $stmt_faixas->execute();
    $result_faixas = $stmt_faixas->get_result();
    while ($faixa = $result_faixas->fetch_assoc()) {
        if (!empty($faixa['caminho_audio']) && file_exists($faixa['caminho_audio'])) {
            unlink($faixa['caminho_audio']);
        }
    }
    $stmt_faixas->close();
}
$stmt_albuns->close();

// Apagar faixas, álbuns, playlists e playlist_faixas do usuário
// Não temos ON DELETE CASCADE ativado nas tabelas, então devemos deletar cada um manualmente:

// Apagar playlist_faixas das faixas dos álbuns do usuário
$stmt_del_pf_faixas = $conn->prepare("DELETE FROM playlist_faixas WHERE id_faixa IN (SELECT id FROM faixas WHERE id_album IN (SELECT id FROM albuns WHERE id_usuario = ?))");
$stmt_del_pf_faixas->bind_param("i", $id);
$stmt_del_pf_faixas->execute();
$stmt_del_pf_faixas->close();

// Apagar playlist_faixas das faixas dos álbuns do usuário (garante que não há referências antes de deletar faixas)
$stmt_del_pf_faixas = $conn->prepare("DELETE FROM playlist_faixas WHERE id_faixa IN (SELECT id FROM faixas WHERE id_album IN (SELECT id FROM albuns WHERE id_usuario = ?))");
$stmt_del_pf_faixas->bind_param("i", $id);
$stmt_del_pf_faixas->execute();
$stmt_del_pf_faixas->close();

// Apagar faixas dos álbuns do usuário
$stmt_del_faixas = $conn->prepare("DELETE FROM faixas WHERE id_album IN (SELECT id FROM albuns WHERE id_usuario = ?)");
$stmt_del_faixas->bind_param("i", $id);
$stmt_del_faixas->execute();
$stmt_del_faixas->close();

// Apagar álbuns do usuário
$stmt_del_albuns = $conn->prepare("DELETE FROM albuns WHERE id_usuario = ?");
$stmt_del_albuns->bind_param("i", $id);
$stmt_del_albuns->execute();
$stmt_del_albuns->close();

// Apagar playlist_faixas das playlists do usuário
$stmt_del_pf = $conn->prepare("DELETE FROM playlist_faixas WHERE id_playlist IN (SELECT id FROM playlists WHERE id_usuario = ?)");
$stmt_del_pf->bind_param("i", $id);
$stmt_del_pf->execute();
$stmt_del_pf->close();

// Apagar playlists do usuário
$stmt_del_playlists = $conn->prepare("DELETE FROM playlists WHERE id_usuario = ?");
$stmt_del_playlists->bind_param("i", $id);
$stmt_del_playlists->execute();
$stmt_del_playlists->close();

// Por fim, apagar o usuário
$stmt_del_usuario = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt_del_usuario->bind_param("i", $id);
$stmt_del_usuario->execute();
$stmt_del_usuario->close();

header('Location: ../admin/admin.php');
exit;
?>