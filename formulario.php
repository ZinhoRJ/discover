<?php
//VERIFICAR SE O USUÁRIO ESTÁ LOGADO ANTES DE EXIBIR A PÁGINA, ELE SÓ PODERÁ FAZER UPLOAD CASO ESTEJA LOGADO EM UMA CONTA
if (!isset($_COOKIE['username']) && !isset($_COOKIE['user_id'])) { //caso os cookies 'username' e 'id' não estejam definidos (isset = is set, e o "!" é de negação. Então: "!isset" = "não definido")
    echo ("Você não está logado. <br><br> <a href='login.php'>Fazer Login</a>");
    die();
}

// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "fecip");

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Diretório onde os arquivos de áudio serão salvos
$diretorioDestino = 'uploads/';
if (!is_dir($diretorioDestino)) { //verifica se existe o diretório da variável $diretorioDestino
    mkdir($diretorioDestino, 0777, true); //caso ainda não exista, cria a pasta com a função "mkdir"
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeAlbum = $_POST['nome_album'];
    $genero = $_POST['genero'];
    $ano = $_POST['ano'];
    $descricao = $_POST['descricao'];
    $idUsuario = intval($_COOKIE['user_id']); // Certo agora!

    // Salvar imagem da capa
    $caminhoImagem = "";
    if (
        isset($_FILES['imagem_capa']) &&
        $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK &&
        is_uploaded_file($_FILES['imagem_capa']['tmp_name'])
    ) {
        $extensaoImagem = pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION);
        $nomeUnicoImagem = uniqid('capa_') . '.' . $extensaoImagem;
        $caminhoImagem = $diretorioDestino . $nomeUnicoImagem;

        move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $caminhoImagem);
    }

    // Enviar o >álbum< ao banco
    $stmt = $conn->prepare("INSERT INTO albuns (nome_album, id_usuario, genero, ano, descricao, capa_album) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisiss", $nomeAlbum, $idUsuario, $genero, $ano, $descricao, $caminhoImagem);
    $stmt->execute();
    $idAlbum = $stmt->insert_id; //salva o ID do novo registro, assim que ele foi enviado ao banco de dados!

    // insere >faixas< do álbum com o idAlbum correto
    if (isset($_POST['nome_musica']) && isset($_FILES['audio'])) {
        foreach ($_POST['nome_musica'] as $i => $nomeMusica) {
            if (!empty($nomeMusica) && isset($_FILES['audio']['name'][$i])) {
                $arquivoOriginal = $_FILES['audio']['name'][$i];
                $extensaoAudio = pathinfo($arquivoOriginal, PATHINFO_EXTENSION);
                $nomeUnicoAudio = uniqid('musica_') . '.' . $extensaoAudio;
                $caminhoAudio = $diretorioDestino . $nomeUnicoAudio;

                move_uploaded_file($_FILES['audio']['tmp_name'][$i], $caminhoAudio);

                $stmt = $conn->prepare("INSERT INTO faixas (id_album, nome_musica, caminho_audio) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $idAlbum, $nomeMusica, $caminhoAudio);
                $stmt->execute();
            }
        }
    }

    echo "✅ Álbum e faixas enviados com sucesso!";
}
?>





<!-- HTML -->
<!DOCTYPE html>
<html lang="pt-BR">
<style>
    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Músicas</title>
</head>

<body>
    <h1>Envie suas músicas</h1>
    <h3>
        Logado como:
        <?php
            echo "<a href=''>" . $_COOKIE['username'] . "</a>";
        ?>
    </h3>

    <!-- O formulário abaixo vai enviar as informações de cada campo via método POST -->
    
    <button class="btnType" id="btnAlbum" onclick="enviarTipo(1)">
        <img src="./svg/music-album-track-svgrepo-com.svg" alt="">
        Álbum
    </button>
    
    <button class="btnType" id="btnEP" onclick="enviarTipo(2)">
        <img src="./svg/music-album-album-svgrepo-com.svg" alt="">
        EP
    </button>

    <button class="btnType" id="btnSingle" onclick="enviarTipo(3)">
        <img src="./svg/compact-disc-cd-svgrepo-com.svg" alt="">
        Single
    </button>
    
    <br>
    
    <!-- ALBUM -->
    <form id="formAlbum" style="display: none" action="formulario.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Informações do Álbum</legend>

            <!-- Preview da Imagem de Capa -->
            <img id="preview" src="#" alt="Preview da capa" style="display:none; max-width: 300px; margin-top: 10px;" />

            <label for="imagem_capa">Imagem de Capa:</label>
            <input type="file" name="imagem_capa" id="imagem_capa" accept="image/*" required><br><br>

            <label for="nome_album">Nome do Álbum:</label>
            <input type="text" name="nome_album" id="nome_album"><br><br>

            <label for="nome_artista">Nome do Artista:</label>
            <input type="text" name="nome_artista" id="nome_artista"><br><br>

            <label for="genero">Gêneros (separe por vírgulas):</label>
            <input type="text" name="genero" id="genero"><br><br>

            <label for="ano">Ano:</label>
            <input type="text" name="ano" id="ano"><br><br>

            <label for="descricao">Descrição:</label>
            <input type="text" name="descricao" id="descricao"><br><br>
        </fieldset>

        <div id="faixasContainer">
            <!-- FIELDSET DAS FAIXAS VÃO SER ADICIONADOS AQUI, DINAMICAMENTE -->
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="button" class="btnType" onclick="adicionarFaixa()">
                <img src="./svg/add-circle-svgrepo-com.svg" alt="">
                Adicionar Faixa
            </button>

            <button type="submit" class="btnType">
                <img src="./svg/save-floppy-svgrepo-com.svg" alt="">
                Salvar Álbum
            </button>
        </div>
    </form>



    <form id="formEP" style="display: none" action="formulario.php" method="POST" enctype="multipart/form-data">
        <label for="imagem_capa">Imagem de Capa: </label>
        <input type="file" name="imagem_capa" id="imagem_capa" accept="image/*" required><br><br>

        <label for="nome_musica">Nome da Música:</label>
        <input type="text" name="nome_musica" id="nome_musica" required><br><br>

        <label for="nome_album">Nome do Álbum:</label>
        <input type="text" name="nome_album" id="nome_album" placeholder="aaa"><br><br>

        <label for="nome_artista">Nome do Artista:</label>
        <input type="text" name="nome_artista" id="nome_artista"><br><br>

        <label for="genero">Gêneros (separe por vírgulas):</label>
        <input type="text" name="genero" id="genero"><br><br>

        <label for="audio">Arquivos de Áudio:</label>
        <input type="file" name="audio[]" id="audio" accept="audio/*" multiple required><br><br>
    
        <button type="submit">Enviar</button>
    </form>



    <form id="formSingle" style="display: none" action="formulario.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Informações do Single</legend>

            <label for="imagem_capa">Imagem de Capa: </label>
            <input type="file" name="imagem_capa" id="imagem_capa" accept="image/*" required><br><br>

            <label for="nome_album">Nome do Single:</label>
            <input type="text" name="nome_album" id="nome_album" placeholder="aaa"><br><br>

            <label for="nome_artista">Nome do Artista:</label>
            <input type="text" name="nome_artista" id="nome_artista"><br><br>

            <label for="genero">Gêneros (separe por vírgulas):</label>
            <input type="text" name="genero" id="genero"><br><br>
        </fieldset>
        
        <button class="btnType">
            <img src="./svg/add-circle-svgrepo-com.svg" alt="">
            Adicionar Faixa
        </button>

        <fieldset>
            <legend>Faixa 1</legend>

            <label for="nome_musica">Nome da Música:</label>
            <input type="text" name="nome_musica" id="nome_musica" required><br><br>

            <label for="audio">Arquivo de Áudio:</label>
            <input type="file" name="audio[]" id="audio" accept="audio/*" multiple required><br><br>
        </fieldset>
        
        
        <br>
    
        <button type="submit" class="btnType">
            <img src="./svg/save-floppy-svgrepo-com.svg" alt="">
            Salvar
        </button>
    </form>



    <br>
    <a href="./discover.php"><b>SAIR</b></a>

    <style>
        .btnType{
            cursor:pointer;
            width: 200px;
            height: 50px;

            margin-top: 10px;
            padding: 10px;
            gap: 10px;
            display: flex;
            align-items: center;

            border: 5px solid black;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btnType img {
            width: 28px;
        }
    </style>

    <script>
        var formSingle = document.getElementById("formSingle");
        var formAlbum = document.getElementById("formAlbum");
        var formEP = document.getElementById("formEP");

        function enviarTipo(num) { //SELEÇÃO DE TIPO DE ENVIO (ÁLBUM, LP, SINGLE)
            switch (num){
                case 1:
                    formAlbum.style.display = "block";

                    document.getElementById("btnAlbum").style.display = "none";
                    document.getElementById("btnSingle").style.display = "none";
                    document.getElementById("btnEP").style.display = "none";
                    break;
                case 2:
                    formEP.style.display = "block";

                    document.getElementById("btnAlbum").style.display = "none";
                    document.getElementById("btnSingle").style.display = "none";
                    document.getElementById("btnEP").style.display = "none";
                    break;
                case 3:
                    formSingle.style.display = "block";

                    document.getElementById("btnAlbum").style.display = "none";
                    document.getElementById("btnSingle").style.display = "none";
                    document.getElementById("btnEP").style.display = "none";
                    break;
                default:
                    
            }
        }


        //Adicionar mais de uma faixa em Álbuns
        let faixaCount = 0;

        function adicionarFaixa() {
            faixaCount++;
        
            const container = document.getElementById("faixasContainer");

            const fieldset = document.createElement("fieldset");
            
            if (faixaCount<=15){
            fieldset.innerHTML = `
                <legend>Faixa ${faixaCount}/15</legend>

                <label for="nome_musica_${faixaCount}">Nome da Música:</label>
                <input type="text" name="nome_musica[]" id="nome_musica_${faixaCount}" required><br><br>

                <label for="audio_${faixaCount}">Arquivo de Áudio:</label>
                <input type="file" name="audio[]" id="audio_${faixaCount}" accept="audio/*" required><br><br>
            `;
            container.appendChild(fieldset);
            }
        }

        // Adiciona a primeira faixa por padrão
        window.onload = () => adicionarFaixa();


        //Preview de Imagem
        function previewImagem() {
            const input = document.getElementById('imagem_capa');
            const preview = document.getElementById('preview');
  
            if (input.files && input.files[0]) {
                const reader = new FileReader();
    
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
    
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>