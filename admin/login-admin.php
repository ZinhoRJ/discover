<?php
session_start();

include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nome = ? AND tipo_usuario = 'admin';");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()){
        if (!password_verify($password, $usuario['senha'])) {
            echo "<p>Senha incorreta. Tente novamente.</p>";
            exit();
        } else {
            $_SESSION['usuario_admin'] = $usuario['id'];
            $_SESSION['tipo'] = $usuario['tipo_usuario'];
            $_SESSION['nome'] = $usuario['nome'];
            header("Location: ../admin/admin.php");
            exit();
        }
    }
}
?>

<html>
    <head>
        <link rel="stylesheet" href="../css/admin/login-admin.css">
    </head>
    <body>
        <div class="container">
            <h1>Login do Administrador</h1>
            <form method="POST" action="login-admin.php">
                <input type="text" name="username" placeholder="Nome de Usuário" required>
                <input type="password" name="password" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>
        </div>
    </body>