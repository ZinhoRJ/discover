<?php
include "../config.php"; //inclui o arquivo que tem a conexão com o banco de dados

// Verificar se o cookie "recentes" existe
if (isset($_COOKIE['recentes'])) { //se o cookie "recentes" já está definido (isset)...
    $ids = explode(',', $_COOKIE['recentes']);
    $idList = implode(',', array_map('intval', $ids));

    // Consultar músicas recentes no banco de dados
    $query = "SELECT * FROM faixas WHERE id IN ($idList) ORDER BY FIELD(id, $idList)"; //seleciona as músicas em que o ID esteja dentro do array idList, ordernando elas COM A mesma ordem do array idList
    $resultFaixa = $conn->query($query);

    //consultar o álbum no banco de dados
    $query = "SELECT capa_album FROM albuns WHERE id IN ($idList) ORDER BY FIELD(id, $idList)";
    $resultAlbum = $conn->query($query);

    if ($resultFaixa->num_rows > 0) { //se retornar algum valor...
        while ($faixa = $resultFaixa->fetch_assoc() && $album = $resultAlbum->fetch_assoc()) { //cria um loop while na variável $row, essa $row é o array $result (que tem todas as tuplas da tabela) que foi dividido em partes separadas com a função fetch_assoc()
            
            //criamos um botão com a capa, nome e link da faixa de música
            echo "<div class='recentes-item' onclick='playMusic(" . $faixa['id'] . ")'>
                    <img src='" . $album['capa_album'] . "' alt='Capa Álbum'>
                    <p><b>" . $faixa['nome_musica'] . "</b></p>
                  </div>";
        }
        echo json_encode($html);
    } else { //caso não tenha nenhum valor no array $result, então exibe a mensagem abaixo
        echo "<p>Nenhuma música recente encontrada.</p>";
    }
} else {
    echo "<p>Nenhuma música recente tocada ainda.</p>";
}

// Fechar conexão
$conn->close();
?>