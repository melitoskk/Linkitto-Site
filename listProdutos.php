<?php
// Conexão com o banco de dados
$servername = "50.116.87.79";
$username = "linkit58_admin";
$password = "^+(<E;Mf%0QFVVT";
$dbname = "linkit58_main"; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Buscar as categorias
$sql_categorias = "SELECT id, nome FROM categorias";
$result_categorias = $conn->query($sql_categorias);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorias, Episódios e Produtos</title>
    <style>
        .categoria {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .episodio {
            margin-left: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .produto {
            margin-left: 40px;
            padding: 10px;
            border: 1px solid #ddd;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <h1>Lista de Categorias, Episódios e Produtos</h1>

    <?php
    if ($result_categorias->num_rows > 0) {
        // Loop pelas categorias
        while ($categoria = $result_categorias->fetch_assoc()) {
            echo "<div class='categoria'>";
            echo "<h2>" . $categoria['nome'] . "</h2>";

            // Buscar episódios dessa categoria
            $sql_episodios = "SELECT id, numero_episodio FROM episodios WHERE categoria_id = " . $categoria['id'];
            $result_episodios = $conn->query($sql_episodios);

            if ($result_episodios->num_rows > 0) {
                // Loop pelos episódios
                while ($episodio = $result_episodios->fetch_assoc()) {
                    echo "<div class='episodio'>";
                    echo "<h3>Episódio: " . $episodio['numero_episodio'] . "</h3>";

                    // Buscar produtos desse episódio
                    $sql_produtos = "SELECT nome_produto, id_produto, link_produto, imagem_produto FROM produtos WHERE episodio_id = " . $episodio['id'];
                    $result_produtos = $conn->query($sql_produtos);

                    if ($result_produtos->num_rows > 0) {
                        // Loop pelos produtos
                        while ($produto = $result_produtos->fetch_assoc()) {
                            echo "<div class='produto'>";
                            echo "<h4>Produto: " . $produto['nome_produto'] . "</h4>";
                            echo "<p>ID: " . $produto['id_produto'] . "</p>";
                            echo "<p><a href='" . $produto['link_produto'] . "' target='_blank'>Ver Produto</a></p>";
                            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . $produto['nome_produto'] . "' style='width: 100px;'>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Nenhum produto encontrado.</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>Nenhum episódio encontrado.</p>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma categoria encontrada.</p>";
    }

    $conn->close();
    ?>

</body>

</html>