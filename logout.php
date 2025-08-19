<?php
//mostrar os dados salvos posteriormente
if (isset($_COOKIE['username'])) { //caso o cookie 'username' esteja definido (isset = is set)
    echo "Usuário: " . htmlspecialchars($_COOKIE['username']) . "  ID: " . htmlspecialchars($_COOKIE['user_id']);
    //a função htmlspecialchars() impede exploit de XSS.
}


// Limpar cookies
setcookie("username", "", time() - 3600, "/"); // Tempo negativo para remover, padrão é mesmo 3600
setcookie("user_id", "", time() - 3600, "/");
echo "<H1>Você saiu com sucesso.</H1>";
?>

<html>
<style>
    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
</style>
<body>    
    <br>

    
    <h2 id="tempo" onload="atualizarTempo()">
        Tempo para retornar ao início: 5
    </h2>
    
</body>
<script>
    let tempo = 4; //quantidade de segundos

    function atualizarTempo () { //função a ser executada para atualizar o tempo
        let temporizador = document.getElementById("tempo"); //salva na variável temporizador o elemento com id="tempo"
        temporizador.textContent = tempo; //diz que o conteúdo do H2 é o mesmo valor da variável tempo
        
        if (tempo > 0){ //enquanto o tempo for maior doq 0
            temporizador.textContent = "Tempo para retornar ao início: " + tempo;
            tempo--;
        }
    }

    const intervaloTempo = setInterval(atualizarTempo, 1000); //a cada 1000 milissegundos, executar a função atualizarTempo.
</script>
</html>

<?php
//redirecionar para a tela inicial (player.php)
//echo '<meta http-equiv="refresh" content="5; url=./index.php">'; //esse 'meta' faz o redirecionamento em um tempo personalizado, diferente do 'header' que faz no mesmo instante

header("Location: index.php");
exit();
?>