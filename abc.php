<?php
$query = "SELECT * FROM musicas WHERE id IN ($idList)";
$resultRecentes = $conn->query($query);

if ($resultRecentes && $resultRecentes->num_rows > 0) {
    while ($row = $resultRecentes->fetch_assoc()) {
        echo "<div class='recentes-item' onclick='playMusic(" . $row['id'] . ")'>
                <img src='" . $row['capa_album'] . "' alt='Capa Álbum'>
                <p><b>" . $row['nome_musica'] . "</b></p>
              </div>";
    }
} else {
    echo "<p>Nenhuma música recente encontrada.</p>";
}

$query = "SELECT * FROM musicas WHERE id IN ($idList)";
            $resultRecentes = $conn->query($query);

            if ($resultRecentes->num_rows > 0) {
                while ($row = $resultRecentes->fetch_assoc()) {
                    echo "<div class='recentes-item' onclick='playMusic(" . $row['id'] . ")'>
                    <img src='" . $row['capa_album'] . "' alt='Capa Álbum'>
                    <p><b>" . $row['nome_musica'] . "</b></p>
                  </div>";
                }
            } else {
                echo "<p>Nenhuma música recente encontrada.</p>";
            }

?>