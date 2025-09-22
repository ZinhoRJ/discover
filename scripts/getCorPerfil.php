<?php
$id_usuario = intval($_GET['id']);

include './config.php'; //inclui o arquivo que tem a conexão com o banco de dados

$stmt = $conn->prepare("SELECT estilo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

switch ($user['estilo']) {
    case '1':
        $corFundo = "no-repeat -webkit-linear-gradient(rgb(75, 0, 145), rgb(0, 149, 160))";
        break;
    case '2':
        $corFundo = "no-repeat -webkit-linear-gradient(#9e08a9, #c30000)";
        break;
    case '3': 
        $corFundo = "no-repeat -webkit-linear-gradient(#000000, #00d6b9)";
        break;
    default:
        $corFundo = "no-repeat -webkit-linear-gradient(rgb(75, 0, 145), rgb(0, 149, 160))";
        break;
}
?>

<style>
body {
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    background: <?php echo $corFundo; ?>;
}

html {
    scrollbar-color: rgba(255, 255, 255, 0.4) transparent;
    scrollbar-width: thin;  
}
</style>