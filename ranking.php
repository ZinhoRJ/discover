<?php
include './config.php'; // Conexão com o banco de dados

$stmt = $conn->prepare("SELECT * FROM usuarios ORDER BY ranking");
$stmt->execute();
$result = $stmt->get_result();

$usuarios = $result->fetch_assoc();
?>

<html>
<head>
    <title>Ranking</title>
</head>
<body>
        
</body>
</html>