<?php
include 'config.php';

// Conexão com o banco de dados
$servername = $env_ip;
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

// Buscar os produtos mais recentes com base no id (ordem crescente)
$sql_produtos_recent = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome 
                        FROM produtos p 
                        JOIN categorias c ON p.categoria_id = c.id
                        ORDER BY p.id_produto DESC LIMIT 4";
$result_produtos_recent = $conn->query($sql_produtos_recent);

// Buscar os episódios mais recentes com base no id (ordem crescente)
$sql_episodios = "SELECT id, thumb_url FROM episodios ORDER BY id DESC LIMIT 3";
$result_episodios = $conn->query($sql_episodios);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkitto</title>
    <link rel="stylesheet" href="style.css">
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

        .product-row {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .product-card {
            margin: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            width: 200px;
        }

        .product-row h2 {
            flex-basis: 100%; /* H2 ocupa toda a largura */
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">Linkitto</div>
        <nav class="nav-icons">
            <a href="#">🐦</a>
            <a href="#">📸</a>
            <a href="#">📘</a>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Pesquisar">
            <button>Pesquisar</button>
        </div>
    </header>

    <div class="container">
        <!-- Sidebar - Categorias -->
        <aside class="sidebar">
            <?php
            if ($result_categorias->num_rows > 0) {
                while ($categoria = $result_categorias->fetch_assoc()) {
                    echo "<button class='category-btn'>" . $categoria['nome'] . "</button>";
                }
            } else {
                echo "<p>Nenhuma categoria encontrada.</p>";
            }
            ?>
        </aside>

        <!-- Main Content - Carousel e Produtos -->
        <main class="content">
            <div class="carousel">
                <!-- Carousel de imagens -->
                <?php
                if ($result_episodios->num_rows > 0) {
                    while ($episodio = $result_episodios->fetch_assoc()) {
                        echo "<div class='carousel-slide'><img src='" . $episodio['thumb_url'] . "' alt='Thumbnail do episódio'></div>";
                    }
                }
                ?>
            </div>

            <h2>Adicionados Recentemente</h2>
            <div class="product-row">
                <?php
                if ($result_produtos_recent->num_rows > 0) {
                    while ($produto = $result_produtos_recent->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<h4>Produto: " . $produto['nome_produto'] . "</h4>";
                        echo "<p>ID do Produto: " . $produto['id_produto'] . "</p>"; // Adicionando o ID do produto
                        echo "<p>Categoria: " . $produto['categoria_nome'] . "</p>"; // Exibindo a categoria
                        echo "<p><a href='" . $produto['link_produto'] . "' target='_blank'>Ver Produto</a></p>";
                        echo "<img src='" . $produto['imagem_produto'] . "' alt='" . $produto['nome_produto'] . "' style='width: 100px;'>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Sem produtos recentes.</p>";
                }
                ?>
            </div>

            <!-- Categorias com produtos -->
            <?php
            if ($result_categorias->num_rows > 0) {
                // Reexecutar a query para percorrer as categorias novamente
                $result_categorias = $conn->query($sql_categorias);

                // Loop pelas categorias
                while ($categoria = $result_categorias->fetch_assoc()) {
                    echo "<div class='categoria'>";
                    echo "<div class='product-row'>";
                    echo "<h2>" . $categoria['nome'] . "</h2>";

                    // Verificar se há produtos na categoria
                    $sql_produtos_categoria = "SELECT nome_produto, id_produto, imagem_produto, link_produto FROM produtos WHERE categoria_id = " . $categoria['id'] . " ORDER BY id_produto DESC";
                    $result_produtos_categoria = $conn->query($sql_produtos_categoria);

                    if ($result_produtos_categoria->num_rows > 0) {
                        // Se houver produtos, exibir na product-row
                        while ($produto = $result_produtos_categoria->fetch_assoc()) {
                            echo "<div class='product-card'>";
                            echo "<h4>Produto: " . $produto['nome_produto'] . "</h4>";
                            echo "<p>ID do Produto: " . $produto['id_produto'] . "</p>"; // Adicionando o ID do produto
                            echo "<p><a href='" . $produto['link_produto'] . "' target='_blank'>Ver Produto</a></p>";
                            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . $produto['nome_produto'] . "' style='width: 100px;'>";
                            echo "</div>";
                        }
                    } else {
                        // Se não houver produtos, exibir mensagem
                        echo "<p>Nenhum produto disponível nesta categoria.</p>";
                    }

                    echo "</div>"; // Fechar a product-row
                    echo "</div>"; // Fechar a categoria
                }
            }
            ?>
        </main>

        <!-- Sidebar - Popular -->
        <aside class="sidebar-popular">
            <h2>Popular</h2>
            <!-- Produtos populares podem ser gerados aqui de maneira similar -->
        </aside>
    </div>
</body>

</html>

<?php
$conn->close();
?>
