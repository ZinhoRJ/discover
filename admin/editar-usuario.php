<?php
session_start();
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    die("ID de usuário não fornecido.");
}

$stmt = $conn->prepare("SELECT nome, email, bio, foto_perfil FROM usuarios WHERE id = ?;");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuário não encontrado.";
    exit();
}

$usuario = $result->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];

    // Salvar imagem de perfil-----------
    $caminhoImagem = $usuario['foto_perfil']; // mantém o valor atual por padrão
    if (
        isset($_FILES['imagem_perfil']) &&
        $_FILES['imagem_perfil']['error'] === UPLOAD_ERR_OK &&
        is_uploaded_file($_FILES['imagem_perfil']['tmp_name'])
    ) {
        $extensaoImagem = pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION);
        $nomeUnicoImagem = uniqid('pfp_') . '.' . $extensaoImagem;
        
        $caminhoServidor = __DIR__ . '/../uploads/' . $nomeUnicoImagem; // pra salvar no servidor
        $caminhoImagem = 'uploads/' . $nomeUnicoImagem; // para salvar no banco

        move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $caminhoServidor);
    }
    //-------------------------------------

    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ?, foto_perfil = ? WHERE id = ?;");
    $stmt->bind_param("ssssi", $nome, $email, $bio, $caminhoImagem, $id);
    
    if ($stmt->execute()) {
        echo "Usuário atualizado com sucesso.";
        header("Location: ../admin/admin.php");
        exit();
    } else {
        echo "Erro ao atualizar usuário: " . $stmt->error;
    }
}
?>

<html>
    <body>
        <h1>Editar Usuário: <?php echo htmlspecialchars($usuario['nome']); ?></h1>

        <form action="editar-usuario.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <?php if (!empty($usuario['foto_perfil'])): ?>
                <div>
                    <label>Imagem de perfil atual:</label><br>
                    <img src="../<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Imagem de perfil" style="max-width:150px;max-height:150px;">
                </div>
            <?php endif; ?>
            <input type="file" name="imagem_perfil">
            <input type="text" name="nome" placeholder="Nome" value="<?php echo $usuario['nome']; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $usuario['email']; ?>" required>
            <textarea name="bio" placeholder="Bio"><?php echo $usuario['bio']; ?></textarea>
            
            <button type="submit">Salvar</button>
        </form>
    </body>
</html>