<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fazer Login</title>

    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <h1>discover</h1>

    <form action="login.php" method="POST">
        <h3>Fazer Login</h3>
        <br>
        <br>
        <input type="text" name="username" placeholder="E-mail" required>
        <input type="password" name="password" placeholder="Senha" required>
        <br>
        <button type="submit">Entrar</button>
    </form>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") { //verifica se o método é POST, e só executa o código depois do envio do formulário (quando o método for POST)
    $username = $_POST['username']; //salva na variável $username o conteúdo do html nomeado como 'username'
    $password = $_POST['password']; //vai salvar na variável $password onde no html estiver nomeado como 'password' (é a mesma coisa do de cima, só mudei a explicação)

    // Conexão com o banco de dados
    $conn = new mysqli("localhost", "root", "", "fecip");

    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Consultar usuário no banco de dados
    $sql = "SELECT * FROM usuarios WHERE nome = ?"; //O "?" serve como placeholder pra evitar SQL Injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); //vai substituir o ? pela variável $username, o "s" define como uma string (parecido com a linguagem C)
    $stmt->execute(); //executa a consulta SQL (query) depois de subsituir o ? pela variável
    $result = $stmt->get_result(); //vai pegar o resultado daquela query, usar a função get_result() para organizar os dados e então salvar na variável $result.

    if ($result->num_rows > 0) { //verifica se a query tem um valor não nulo (pelo menos uma tupla)
        $user = $result->fetch_assoc(); //e então salva num array para poder acessar o hash da senha e logo depois descriptografar ela

        // Verificar senha
        if (password_verify($password, $user['senha'])) { //a função password_verify() compara a senha fornecida na variável $password (que foi pega do html) com o hash armazenado no banco de dados ($user['password'], que foi salva no array pelo fetch_assoc())
            
            //SALVANDO O LOGIN NOS COOKIES PARA SER USADO EM OUTRAS PÁGINAS
            setcookie("username", $user['nome'], time() + (86400 * 7), "/", "", true, true); //nome de usuário 
            setcookie("user_id", $user['id'], time() + (86400 * 7), "/"); //id do usuário
            setcookie("pass", $user['senha'], time() + (86400 * 7), "/", "", true, true); //nome de usuário 

            
            echo "<p>Login bem-sucedido! Bem-vindo, " . htmlspecialchars($user['nome']) . ".</p>"; //a função htmlspecialchars() serve pra impedir ataques de Cross-Site Scripting, n sei como funciona mas sei lá, melhor evitar.
        } else {
            echo "<p>Senha incorreta. Tente novamente.</p>"; //caso as senhas sejam diferentes, ele exibe a mensagem de erro.
        }
    } else {
        echo "<p>Usuário não encontrado. Verifique suas credenciais.</p>"; //caso n tenha nenhuma tupla no banco de dados, logo damos esse erro
        //esse erro n tem relação direta com oq está acontecendo no código (ele n achou nada no banco de dados, mas disse que n tinha o usuário, enquanto naverdade n tem usuário nenhum)
        //isso acontece pq devemos ser o mais genérico o possível com erros, pra não dar nenhuma dica pra hackers e sim atrapalhar a vida deles.
    }

    $stmt->close(); //fecha a query preparada com o hash
    $conn->close(); //fecha qualquer conexão com o banco de dados

    echo '<meta http-equiv="refresh" content="2; url=./discover.php">'; //esse 'meta' faz o redirecionamento em um tempo personalizado, diferente do 'header' que faz no mesmo instante
    exit();
}
?>