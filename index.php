<?php 
//informações para conexão do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fecip";

//conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

//verificar se houve erros na conexão
if ($conn -> connect_error) {
    die("[ ERR ] Falha na conexão: " + $conn -> connect_error);
} else {
    echo 'Teste ECHOES, conexão bem-sucedida!';
}

//query tabela usuarios
$usuarios_sql = "SELECT * FROM usuarios";
$usuarios_resultado =  $conn -> query ($usuarios_sql);

//exibindo resultado da query de usuários
echo "<h2> Lista de Usuários </h2>";
if ($usuarios_resultado != null) {
    echo "<ul>";
    while ($user = $usuarios_resultado -> fetch_assoc()) {
        echo "<li>" . $user["nome"] . "  |  " . $user["email"]; //os pontos servem para contatenar (juntar) as strings, como um + (tipo: "echo + $user + echo")
        echo "</li>";
    }
    echo "</ul>";
}
?>