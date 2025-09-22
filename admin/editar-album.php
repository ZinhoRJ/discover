<?php
session_start();
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    die("ID de usuário não fornecido.");
}

$stmt = $conn->prepare("SELECT id, nome_album, capa_album, ano, genero, descricao FROM albuns WHERE id = ?;");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Álbum não encontrado.";
    exit();
}

$album = $result->fetch_assoc();


// APÓS O ADMIN SALVAR AS MUDANÇAS --------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $ano = intval($_POST['ano']);
    $genero = $_POST['genero'];
    $descricao = $_POST['descricao'];

    // Salvar imagem de perfil-----------
    $caminhoImagem = $album['capa_album']; // mantém o valor atual por padrão e impede de enviar uma imagem vazia (caso vc não tenha mudado ela)
    if (
        isset($_FILES['capa_album']) &&
        $_FILES['capa_album']['error'] === UPLOAD_ERR_OK &&
        is_uploaded_file($_FILES['capa_album']['tmp_name'])
    ) {
        $extensaoImagem = pathinfo($_FILES['capa_album']['name'], PATHINFO_EXTENSION);
        $nomeUnicoImagem = uniqid('pfp_') . '.' . $extensaoImagem;
        
        $caminhoServidor = __DIR__ . '/../uploads/' . $nomeUnicoImagem; // pra salvar no servidor
        $caminhoImagem = 'uploads/' . $nomeUnicoImagem; // para salvar no banco

        move_uploaded_file($_FILES['capa_album']['tmp_name'], $caminhoServidor);
    }
    //-------------------------------------

    $stmt = $conn->prepare("UPDATE albuns SET nome_album = ?, capa_album = ?, ano = ?, genero = ?, descricao = ? WHERE id = ?;");
    $stmt->bind_param("ssissi", $nome, $caminhoImagem, $ano, $genero, $descricao, $id);

    if ($stmt->execute()) {
        echo "Álbum atualizado com sucesso.";
        header("Location: ../admin/admin.php");
        exit();
    } else {
        echo "Erro ao atualizar álbum: " . $stmt->error;
    }
}
?>

<html>
    <body>
        <h1>Editar Álbum: <?php echo htmlspecialchars($album['nome_album']); ?></h1>

        <form action="editar-album.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <label>Atual capa do álbum:</label><br>
            <?php echo "<img src='../" . $album['capa_album'] . "' alt='Capa do Álbum' style='width: 150px; height: 150px; object-fit: cover;'><br><br>"; ?>
            <label>Nova capa do álbum (opcional):</label><br>    
            
            <input type="file" name="capa_album">
            <input type="text" name="nome" placeholder="Nome" value="<?php echo $album['nome_album']; ?>" required>
            <input type="text" name="genero" placeholder="Separe, por, vírgulas" value="<?php echo $album['genero']; ?>" required>
            <input type="text" name="ano" placeholder="2025" value="<?php echo $album['ano']; ?>" required>
            <textarea name="descricao" placeholder="Descrição sobre o álbum"><?php echo $album['descricao']; ?></textarea>
            
            <button type="submit">Salvar</button>
        </form>

        <!-- FAIXAS DO ÁLBUM -->
        <h2>Faixas do Álbum</h2>
        <?php 
        $stmt = $conn->prepare("SELECT id AS id_faixa, nome_musica, caminho_audio, id_album FROM faixas WHERE id_album = ?;");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultFaixas = $stmt->get_result();

        while ($faixa = $resultFaixas->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
            
            echo "<form action='editar-faixa.php?id=" . $faixa['id_faixa'] . "&album=" . $faixa['id_album'] . "' method='POST'>";
            echo "<input type='text' name='nome_musica' value='" . htmlspecialchars($faixa['nome_musica']) . "' required>";
            echo "<button type='submit'>Salvar Nome</button>";
            echo "</form>";
            echo "<br>";
            
            echo "<audio controls><source src='../" . $faixa['caminho_audio'] . "' type='audio/mpeg'>Seu navegador não suporta o elemento de áudio.</audio>";
                echo "<br><a href='../admin/excluir-faixa.php?id=" . $faixa['id_faixa'] . "&album=" . $faixa['id_album'] . "'>Excluir Faixa</a>";
                echo "</div>";
        }
        ?>
    </body>
</html>