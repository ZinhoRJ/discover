<?php
header("Content-type: text/css"); // Diz ao navegador que isso é CSS

session_start();
include "../config.php"; // inclui o arquivo que tem a conexão com o banco de dados

$user_id = $_COOKIE['user_id'] ?? null;

// Valor padrão para $corFundo caso não seja definido pelo usuário
$corFundo = "#850303ff"; // branco padrão

if ($user_id) {
    $stmt = $conn->prepare("SELECT estilo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        switch ($usuario['estilo']) {
            case '1': $corFundo = "-webkit-linear-gradient(rgb(75, 0, 145), rgb(0, 149, 160))"; break; // azul claro
            case '2': $corFundo = "#38ce2aff"; break; // rosa claro
            case '3': $corFundo = "#ffff00ff"; break; // bege
            default: $corFundo = "-webkit-linear-gradient(rgb(75, 0, 145), rgb(0, 149, 160))"; break;
        }
    }
}
?>

body {
    background: <?php echo $corFundo; ?>;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}