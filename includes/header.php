<head>
    <link rel="stylesheet" href="./css/player.css">
</head>

<div class="container-buttons" style="background-color:rgba(48, 48, 48, 0.34); border-radius: 8px;">
    <div class="buttons">
        <div>
            <!--
            BOTÃO PRA ENVIAR MÚSICAS
            -->
            <a href="./formulario.php"> <button class="button-enviar">ENVIAR MÚSICAS</button> </a>
        </div>
    </div>

    <div>
        <b><a href="player.php" style="font-size: larger; color: black">discover // vibra</a></b>
    </div>

    <div style="display: flex">   
        <!-- 
        VERIFICAR SE O USUÁRIO TÁ LOGADO PARA MOSTRAR O BOTÃO DE LOGIN/MOSTRAR O NOME DE USUÁRIO 
        -->
        <?php
        //mostrar os dados salvos posteriormente em cookies
        if (isset($_COOKIE['username'])) { //caso o cookie 'username' esteja definido (isset = is set) - isso significa que o usuário está logado.
            echo "<a href='./edit.php'> <button class='button-logar' style='width: auto;'>menu super secreto do admin</button> </a>";
            echo "<div class='perfil' style='padding-left: 10px'> <b>Usuário:</b> " . htmlspecialchars($_COOKIE['username']) . " <b>ID:</b> " . htmlspecialchars($_COOKIE['user_id']); //a função htmlspecialchars() impede exploit de XSS na hora de enviar os cookies.
            echo (" <a href='logout.php'><button class='button-logar' style='background-color: red; color: #fff; width: 50px;'>SAIR</button></a> </div>");
            
        } else {
            echo "<a href='./login.php'> <button class='button-logar'>FAZER LOGIN</button> </a>";
        }
        ?>
    </div>
</div>

<style>
    .container-buttons {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px
}
</style>