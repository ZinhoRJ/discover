<?php
include './config.php'; //inclui o arquivo que tem a conexão com o banco de dados

session_start(); //chama as informações da Session criada no "register.php"

//verifica se tem um Id salvo na session, caso o contrário, significa que a pessoa não está logada!
if(!isset($_SESSION["user_id"])){ 
    die("Acesso negado!");
}

// Diretório onde os arquivos de áudio serão salvos
$diretorioDestino = 'uploads/';
if (!is_dir($diretorioDestino)) { //verifica se existe o diretório da variável $diretorioDestino
    mkdir($diretorioDestino, 0777, true); //caso ainda não exista, cria a pasta com a função "mkdir"
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Salvar imagem da capa
    $caminhoImagem = "";
    if (
        isset($_FILES['imagem_perfil']) &&
        $_FILES['imagem_perfil']['error'] === UPLOAD_ERR_OK &&
        is_uploaded_file($_FILES['imagem_perfil']['tmp_name'])
    ) {
        $extensaoImagem = pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION);
        $nomeUnicoImagem = uniqid('pfp_') . '.' . $extensaoImagem;
        $caminhoImagem = $diretorioDestino . $nomeUnicoImagem;

        move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $caminhoImagem);
    }

    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, bio = ?, foto_perfil = ? WHERE id=?");
    $stmt->bind_param("sssi", $_POST["nome"], $_POST["bio"], $caminhoImagem, $_SESSION["user_id"]);

    if ($stmt->execute()){
        setcookie("username", $_POST['nome'], time() + (86400 * 7), "/", "", true, true);
        setcookie("user_id", $_SESSION['user_id'], time() + (86400 * 7), "/");

        header("Location: index.php");
        exit();
    } else {
        die("ERRO AO CRIAR PERFIL: " . $stmt->error);
    }
    $stmt->close();
}
?>

<html>

<head>
    <title>Criar Perfil</title>
    <link rel="stylesheet" href="./css/criar-perfil.css">
</head>

<body>
    <div class="container">
        <h1 id="title">Criar Perfil</h1>
        <hr>

        <form action="criar_perfil.php" method="post" enctype="multipart/form-data">
            <!-- sem o enctype, o PHP não tem acesso à imagem -->
            <img id="preview" src="./uploads/padrao.jpg" alt="Prévia da imagem">


            <label for="imagem_perfil">Foto de Perfil:</label>
            <input type="file" name="imagem_perfil" id="imagem_perfil" accept="image/*" required
                class="imagem-perfil"><br><br>

            <br>

            <label for="nome">Nome (público)</label>
            <input type="text" name="nome" id="nome" class="input-form">

            <br>

            <label for="nome">Biografia</label>
            <input type="text" name="bio" id="bio" placeholder="conte mais sobre você!" class="input-form">

            <br>

            <button type="submit">Terminar</button>
        </form>
    </div>
</body>

<script>
    document.getElementById('imagem_perfil').addEventListener('change', function(event) {
    const arquivo = event.target.files[0];
    const preview = document.getElementById('preview');

    if (arquivo) {
        const leitor = new FileReader();
        leitor.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        leitor.readAsDataURL(arquivo);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
});
</script>

</html>