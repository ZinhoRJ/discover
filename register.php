<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Perfis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f5f5f5; }
        form { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        input { display: block; width: 100%; margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #007BFF; color: #fff; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <form action="register.php" method="POST">
        <h2>Cadastro</h2>
        <input type="text" name="username" placeholder="Nome de Usuário" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $nome = $_POST['username']; //esse 'username' vem do html, lá no input
    $email = $_POST['email'];
    $senha = password_hash($_POST['password'], PASSWORD_DEFAULT); // Criptografia segura
    //o campo 'password' tem que ser assim pois é dessa forma que o php entende!

    // Conexão com o banco de dados
    $conn = new mysqli("localhost", "root", "", "fecip");

    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Inserir no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Perfil criado com sucesso!</p>";
    } else {
        echo "<p>Erro: " . $sql . "<br>" . $conn->error . "</p>";
    }

    $conn->close();
}
?>
