<?php
// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'fecip');

if ($conn->connect_error) { //verifica se a conexão vai retornar um erro...
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error); //então mata (die) a conexão e aponta o erro.
}

// Verificar se o cookie "recentes" existe
if (isset($_COOKIE['recentes'])) { //se o cookie "recentes" já está definido (isset)...
    $ids = explode(',', $_COOKIE['recentes']);
    $idList = implode(',', array_map('intval', $ids));

    // Consultar músicas recentes no banco de dados
    $query = "SELECT * FROM musicas WHERE id IN ($idList) ORDER BY FIELD(id, $idList)"; //seleciona as músicas em que o ID esteja dentro do array idList, ordernando elas COM A mesma ordem do array idList
    $result = $conn->query($query);

    if ($result->num_rows > 0) { //se retornar algum valor...
        while ($row = $result->fetch_assoc()) { //cria um loop while na variável $row, essa $row é o array $result (que tem todas as tuplas da tabela) que foi dividido em partes separadas com a função fetch_assoc()
            
            //criamos um botão com a capa, nome e link da faixa de música
            echo "<div class='recentes-item' onclick='playMusic(" . $row['id'] . ")'>
                    <img src='" . $row['capa_album'] . "' alt='Capa Álbum'>
                    <p><b>" . $row['nome_musica'] . "</b></p>
                  </div>";
        }
    } else { //caso não tenha nenhum valor no array $result, então exibe a mensagem abaixo
        echo "<p>Nenhuma música recente encontrada.</p>";
    }
} else {
    echo "<p>Nenhuma música recente tocada ainda.</p>";
}

// Fechar conexão
$conn->close();
?>