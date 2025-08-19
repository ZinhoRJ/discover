<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Perfis</title>
    <link rel="stylesheet" href="./css/login.css">
    <!-- <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f5f5f5; }
        form { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        input { display: block; width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #007BFF; color: #fff; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style> -->
</head>
<body>
    <h1>Bem-vindo</h1>
    <form action="register.php" method="POST">
        <h3>Criar Conta</h3>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>

<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    $conn = new mysqli("localhost", "root", "", "fecip");
    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Dados do formulário
    //$nome = $_POST['username']; //esse 'username' vem do html, lá no input
    $email = $_POST['email'];
    $senha = password_hash($_POST['password'], PASSWORD_DEFAULT); // Criptografia segura
    //o campo 'password' tem que ser assim pois é dessa forma que o php entende!

    // Enviar cadastro ao banco de dados
    $stmt = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $senha);

    //O If serve para verificar se as informações realmente foram enviadas
    if ($stmt->execute()){
        $_SESSION['user_id'] = $stmt->insert_id; //inicia uma sessão com o id que acabou de ser criado!

        // O que é session? -----
        //      É uma forma de armazenar dados entre páginas html, parecido com os cookies,
        //      mas sem expor os dados (fica salvo num arquivo temporário específico).
        //      É mais seguro, pois é gerenciado pelo PHP e não pelo navegador.
        //      Pode ser acessado pela variável global $_SESSION.
        // ----------------------

        header("Location: criar_perfil.php"); //envia a gente pra outra página
        exit(); //obrigatório depois do header!
    } else {
        echo "Erro no registro dos dados!: " . $stmt->error;
    }
    $conn->close();
}
?>
