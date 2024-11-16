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

// Buscar os produtos mais recentes
$sql_produtos_recent = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome 
                        FROM produtos p 
                        JOIN categorias c ON p.categoria_id = c.id
                        ORDER BY p.id_produto DESC LIMIT 4";
$result_produtos_recent = $conn->query($sql_produtos_recent);

// Buscar os episódios mais recentes
$sql_episodios = "SELECT id, thumb_url FROM episodios ORDER BY id DESC LIMIT 3";
$result_episodios = $conn->query($sql_episodios);

// Buscar os produtos mais populares
$sql_produtos_populares = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.clicks, c.nome AS categoria_nome 
                           FROM produtos p 
                           JOIN categorias c ON p.categoria_id = c.id
                           ORDER BY p.clicks DESC LIMIT 4";
$result_produtos_populares = $conn->query($sql_produtos_populares);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkitto</title>
    <link rel="stylesheet" href="style.css">

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

        <!-- Main Content -->
        <main class="content">
            <div class="carousel">
                <?php
                if ($result_episodios->num_rows > 0) {
                    while ($episodio = $result_episodios->fetch_assoc()) {
                        echo "<div class='carousel-slide'><img src='" . $episodio['thumb_url'] . "' alt='Thumbnail do episódio'></div>";
                    }
                }
                ?>
            </div>

            <div class="categoria">
                <h2>Mais Recentes</h2> <!-- Título para os produtos mais recentes -->
                <div class="product-row"> <!-- Mesma estrutura que está nas categorias -->
                    <?php
                    if ($result_produtos_recent->num_rows > 0) {
                        while ($produto = $result_produtos_recent->fetch_assoc()) {
                            echo "<div class='product-card'>";
                            echo "<h4>" . $produto['nome_produto'] . "</h4>";
                            echo "<p>ID do Produto: " . $produto['id_produto'] . "</p>";
                            echo "<p>Categoria: " . $produto['categoria_nome'] . "</p>";
                            echo "<p><a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")'>Ver Produto</a></p>";
                            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . $produto['nome_produto'] . "' style='width: 100px;'>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Sem produtos recentes.</p>";
                    }
                    ?>
                </div>
            </div>



            <!-- Categorias -->
            <?php
            $result_categorias = $conn->query($sql_categorias); // Reexecutar query
            if ($result_categorias->num_rows > 0) {
                while ($categoria = $result_categorias->fetch_assoc()) {
                    echo "<div class='categoria'>";
                    echo "<h2>" . $categoria['nome'] . "</h2>"; // Título da categoria fora da div
                    echo "<div class='product-row'>";

                    $sql_produtos_categoria = "SELECT nome_produto, id_produto, imagem_produto, link_produto FROM produtos WHERE categoria_id = " . $categoria['id'] . " ORDER BY id_produto DESC";
                    $result_produtos_categoria = $conn->query($sql_produtos_categoria);

                    if ($result_produtos_categoria->num_rows > 0) {
                        while ($produto = $result_produtos_categoria->fetch_assoc()) {
                            echo "<div class='product-card'>";
                            echo "<h4>Produto: " . $produto['nome_produto'] . "</h4>";
                            echo "<p>ID do Produto: " . $produto['id_produto'] . "</p>";
                            echo "<p><a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")'>Ver Produto</a></p>";
                            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . $produto['nome_produto'] . "' style='width: 100px;'>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Nenhum produto nesta categoria.</p>";
                    }
                    echo "</div></div>";
                }
            }
            ?>
        </main>
    </div>

    <!-- Sidebar - Produtos Populares (à direita) -->
    <aside class="sidebar-popular">
        <h3>Em Alta</h3> <!-- Título da seção -->
        <?php
        if ($result_produtos_populares->num_rows > 0) {
            while ($produto = $result_produtos_populares->fetch_assoc()) {
                echo "<div class='popular-card'>";
                echo "<h4>" . $produto['nome_produto'] . "</h4>";
                echo "<p>Cliques: " . $produto['clicks'] . "</p>";
                echo "<p><a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")'>Ver Produto</a></p>";
                echo "<img src='" . $produto['imagem_produto'] . "' alt='" . $produto['nome_produto'] . "'>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Sem produtos populares no momento.</p>";
        }
        ?>
    </aside>

    <script>
        function registerClick(idProduto) {
            fetch('updateClicks.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_produto=' + idProduto,
            }).catch(error => console.error('Erro na requisição:', error));
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>