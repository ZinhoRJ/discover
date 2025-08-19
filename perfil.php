<?php
    $conn= new mysqli("localhost", "root", "", "fecip");

    if ($conn -> connect_error){
        die($conn->connect_error);
    }
    
    $id_usuario = intval($_GET['id']); //variável id_usuário é igual ao valor da URL "id" convertido para INT.
    /*$stmt =  $conn->prepare("SELECT id, nome, email, bio FROM usuarios WHERE id = ?");*/
    
    //IMPORTANTE: Adicionar "LEFT" antes do "JOIN" permite que a consulta retorne resultados mesmo que o usuário não tenha nehuma música públicada!
    $stmt = $conn->prepare("
        SELECT 
            usuarios.id AS usuario_id,
            usuarios.nome,
            usuarios.email,
            usuarios.bio,
            usuarios.foto_perfil,
            albuns.id AS album_id,
            albuns.nome_album,
            albuns.genero,
            albuns.ano,
            albuns.descricao,
            albuns.capa_album
        FROM usuarios
        LEFT JOIN albuns ON usuarios.id = albuns.id_usuario
        WHERE usuarios.id = ?;
    "); 
    $stmt -> bind_param("i", $id_usuario); //vincular parâmetros variáveis (os "?") na consulta SQL
    $stmt->execute(); //chama o método do objeto $stmt que executa a consulta
    $resultado = $stmt->get_result(); //obtém o resultado da consulta e salva na variável $resultado
    $user = $resultado->fetch_assoc(); //cria um array associativo com os resultados da consulta SQL

    if (!$id_usuario){
        die("404 Page Not Found");
    }
?>

<html>
    <title>
        teste
    </title>
    <body>
        ID: <?php echo $id_usuario; ?> <br>
        <?php echo $user['nome']; ?> <br>
        <?php echo $user['email']; ?> <br>
        <?php echo $user['bio']; ?> <br>
        <?php echo $user['bio']; ?> <br>

        <?php
        foreach($resultado as $album){
            echo "<p><a href=album.php?id=". $album['album_id'] .">" . $album['nome_album']. "</a></p>";
        }
        ?>
    </body>
</html>

<!-- FECHAR CONEXÃO COM O BANCO DE DADOS -->
<?php
$conn->close();
?>