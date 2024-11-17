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
$sql_categorias = "SELECT id, nome, imagem FROM categorias";
$result_categorias = $conn->query($sql_categorias);

// Buscar os produtos mais recentes
$sql_produtos_recent = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome, l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja 
                        FROM produtos p 
                        JOIN categorias c ON p.categoria_id = c.id
                        JOIN lojas l ON p.loja_produto_id = l.id
                        ORDER BY p.id_produto DESC LIMIT 4";
$result_produtos_recent = $conn->query($sql_produtos_recent);

// Buscar os episódios mais recentes
$sql_episodios = "SELECT id, thumb_url FROM episodios ORDER BY id DESC LIMIT 3";
$result_episodios = $conn->query($sql_episodios);

// Buscar os produtos mais populares
$sql_produtos_populares = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.clicks, c.nome AS categoria_nome, l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja 
                           FROM produtos p 
                           JOIN categorias c ON p.categoria_id = c.id
                           JOIN lojas l ON p.loja_produto_id = l.id
                           WHERE p.clicks > 0  -- Filtra produtos com mais de zero cliques
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="./" class="logo-link">
            <img class="logo" src='../imgs/logo.png' alt="Logo">
        </a>

        <div class="search-bar">
            <input type="text" placeholder="Pesquisar">
            <button>
                <i class="fas fa-search"></i>
            </button>
        </div>
        <nav class="nav-icons">
            <a href="#" class="instagram-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="youtube-icon"><i class="fab fa-youtube"></i></a>
            <a href="#" class="tiktok-icon"><i class="fab fa-tiktok"></i></a>
        </nav>
    </header>

    <div class="container">
        <!-- Sidebar - Categorias -->
        <aside class="sidebar-categorias">
        <h2>Categorias</h2> <!-- Título da seção -->
            <?php
            if ($result_categorias->num_rows > 0) {
                while ($categoria = $result_categorias->fetch_assoc()) {
                    echo "<button class='category-btn' style='background-image: url(\"" . $categoria['imagem'] . "\");'>
                            <span>" . htmlspecialchars($categoria['nome']) . "</span>
                          </button>";
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

            <div class="section-category">
                <h2 class="seccion">Mais Recentes</h2> <!-- Título para os produtos mais recentes -->
                <div class="product-row"> <!-- Mesma estrutura que está nas categorias -->
                    <?php
                    if ($result_produtos_recent->num_rows > 0) {
                        while ($produto = $result_produtos_recent->fetch_assoc()) {
                            echo "<div class='product-card'>";
                            echo "<div class='product-image'>";
                            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                            echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>"; // Logo sobreposta
                            echo "</div>";
                            echo "<div class='product-details'>";
                            echo "<strong class='product-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                            echo "<p class='product-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>"; // Exibe o nome da categoria
                            echo "<p class='product-id'>ID: " . $produto['id_produto'] . "</p>";
                            echo "<small class='disclaimer'>
                                    Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
                                  </small>"; // Nome da loja com link
                            echo "<a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")' class='buy-button'>Ver Produto</a>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Sem produtos recentes.</p>";
                    }
                    ?>
                </div>
            </div>

            <!-- Categorias -->
            <!-- Categorias -->
            <?php
            $result_categorias = $conn->query($sql_categorias); // Reexecutar query
            if ($result_categorias->num_rows > 0) {
                while ($categoria = $result_categorias->fetch_assoc()) {
                    // Query para produtos desta categoria
                    $sql_produtos_categoria = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome, l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja
                                   FROM produtos p
                                   JOIN categorias c ON p.categoria_id = c.id
                                   JOIN lojas l ON p.loja_produto_id = l.id
                                   WHERE p.categoria_id = " . $categoria['id'] . " ORDER BY p.id_produto DESC";
                    $result_produtos_categoria = $conn->query($sql_produtos_categoria);

                    // Verificar se há produtos nesta categoria
                    if ($result_produtos_categoria->num_rows > 0) {
                        echo "<div class='section-category'>";
                        echo "<h2 class='seccion'>" . htmlspecialchars($categoria['nome']) . "</h2>"; // Título dentro da product-row
            
                        echo "<div class='product-row'>";

                        while ($produto = $result_produtos_categoria->fetch_assoc()) {
                            echo "<div class='product-card'>";
                            echo "<div class='product-image'>";
                            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                            echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>"; // Logo sobreposta
                            echo "</div>";
                            echo "<div class='product-details'>";
                            echo "<strong class='product-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                            echo "<p class='product-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>"; // Exibe o nome da categoria
                            echo "<p class='product-id'>ID: " . $produto['id_produto'] . "</p>";
                            echo "<small class='disclaimer'>
                        Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
                      </small>"; // Nome da loja com link
                            echo "<a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")' class='buy-button'>Ver Produto</a>";

                            echo "</div>";
                            echo "</div>";
                        }

                        echo "</div>"; // Fecha product-row
                        echo "</div>"; // Fecha categoria
                    }
                }
            }
            ?>

            <!-- Sidebar Right (copiada da sidebar, mas com cards horizontais) -->
            <aside class="sidebar-popular">
                <h3>Em Alta</h3> <!-- Título da seção -->
                <?php
                if ($result_produtos_populares->num_rows > 0) {
                    while ($produto = $result_produtos_populares->fetch_assoc()) {
                        echo "<div class='popular-card'>";
                        echo "<div class='popular-card-image'>";
                        echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                        echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>";
                        echo "</div>";
                        echo "<div class='popular-card-details'>";
                        echo "<strong class='popular-card-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                        echo "<p class='popular-card-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>";
                        echo "<p class='popular-card-id'>ID: " . $produto['id_produto'] . "</p>";
                        echo "<p class='popular-card-views'>Views: " . $produto['clicks'] . "</p>";
                        echo "<small class='disclaimer'>
        Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
      </small>";
                        echo "<a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")' class='buy-button'>Ver Produto</a>";
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

        </main>

    </div>

    <footer>
        <p>© 2024 Linkitto. Todos os direitos reservados.</p>
    </footer>
</body>

</html>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>