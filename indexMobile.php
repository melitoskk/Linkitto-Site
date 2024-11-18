<?php
include 'config.php';

// Buscar as categorias
$sql_categorias = "SELECT id, nome, imagem FROM categorias";
$result_categorias = $conn->query($sql_categorias);

$sql_produtos_recent = "SELECT p.nome_produto, p.id, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome, l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja 
                        FROM produtos p 
                        JOIN categorias c ON p.categoria_id = c.id
                        JOIN lojas l ON p.loja_produto_id = l.id
                        ORDER BY p.id DESC LIMIT 4";


$result_produtos_recent = $conn->query($sql_produtos_recent);

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
    <link rel="icon" href="./imgs/icon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkitto</title>
    <link rel="stylesheet" href="styleMobile.css">
    <link rel="stylesheet" href="darkmode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="./" class="logo-link">
            <img class="logo" src='../imgs/logo.png' alt="Logo">
        </a>
        <nav class="nav-icons">
            <div id="dark-mode-toggle">
                <i class="darkmode-icon sun fas fa-sun"></i>
                <i class="darkmode-icon moon fas fa-moon"></i>
            </div>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Pesquisar" id="search-input">
            <button id="search-button">
                <i class="fas fa-search"></i>
            </button>
            <div class="search-suggestions" id="search-suggestions"></div>
        </div>


    </header>

    <main class="content">
        <aside class="sidebar-categorias">
            <?php
            if ($result_categorias->num_rows > 0) {
                while ($categoria = $result_categorias->fetch_assoc()) {
                    // Gerar o link dinâmico com o ID da categoria
                    echo "<a href='categorias.php?categoria_id=" . $categoria['id'] . "'>
                    <button class='category-btn' style='background-image: url(\"" . $categoria['imagem'] . "\");'>
                        <span>" . htmlspecialchars($categoria['nome']) . "</span>
                    </button>
                  </a>";
                }
            } else {
                echo "<p>Nenhuma categoria encontrada.</p>";
            }
            ?>
        </aside>
        <?php
        // Consultar as URLs da tabela 'slides'
        $resultSlides = $conn->query("SELECT * FROM slides WHERE url != '' LIMIT 5"); // Limita a 5 slides e filtra URLs não vazias
        ?>
        <div class="carousel-container">
            <div class="carousel-prev" id="prevBtn">
                <i class="fa-solid fa-arrow-left"></i>
            </div>
            <div class="carousel-next" id="nextBtn">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
            <div class="carousel">
                <?php
                // Gerar os slides dinamicamente
                while ($row = $resultSlides->fetch_assoc()) {
                    // Exibir apenas os slides com URL não vazia
                    if (!empty($row['url'])) {
                        echo "<div class='carousel-slide'>
                        <img src='" . $row['url'] . "' alt='Imagem'>
                      </div>";
                    }
                }
                ?>
            </div>
        </div>
        <div class="section-category">
            <h3>Em Alta</h3> <!-- Título da seção -->
            <div class="product-row">
                <?php
                if ($result_produtos_populares->num_rows > 0) {
                    while ($produto = $result_produtos_populares->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<div class='product-card-image'>";
                        echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                        echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>";
                        echo "</div>";
                        echo "<div class='product-card-details'>";
                        echo "<strong class='product-card-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                        echo "<p class='product-card-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>";
                        echo "<p class='product-card-id'>ID: " . $produto['id_produto'] . "</p>";
                        echo "<p class='product-card-views'>Views: " . $produto['clicks'] . "</p>";
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

            </div>
        </div>
        <div class="section-category">
            <h2 class="seccion">Mais Recentes</h2>
            <div class="product-row">
                <?php
                if ($result_produtos_recent->num_rows > 0) {
                    while ($produto = $result_produtos_recent->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<div class='product-card-image'>";
                        echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                        echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>";
                        echo "</div>";
                        echo "<div class='product-card-details'>";
                        echo "<strong class='product-card-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                        echo "<p class='product-card-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>";
                        echo "<p class='product-card-id'>ID: " . $produto['id_produto'] . "</p>";
                        echo "<small class='disclaimer'>
        Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
      </small>";
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
        <?php
        $result_categorias = $conn->query($sql_categorias); // Reexecutar query
        if ($result_categorias->num_rows > 0) {
            while ($categoria = $result_categorias->fetch_assoc()) {
                // Query para produtos desta categoria, ordenando por 'p.id' ao invés de 'p.id_produto'
                $sql_produtos_categoria = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome, l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja
                                               FROM produtos p
                                               JOIN categorias c ON p.categoria_id = c.id
                                               JOIN lojas l ON p.loja_produto_id = l.id
                                               WHERE p.categoria_id = " . $categoria['id'] . " ORDER BY p.id DESC"; // Ordena pela coluna 'id'
        
                $result_produtos_categoria = $conn->query($sql_produtos_categoria);

                // Verificar se há produtos nesta categoria
                if ($result_produtos_categoria->num_rows > 0) {
                    echo "<div class='section-category'>";
                    echo "<h2 class='seccion'>" . htmlspecialchars($categoria['nome']) . "</h2>"; // Título dentro da product-row
        
                    echo "<div class='product-row'>";

                    while ($produto = $result_produtos_categoria->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<div class='product-card-image'>";
                        echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                        echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>";
                        echo "</div>";
                        echo "<div class='product-card-details'>";
                        echo "<strong class='product-card-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                        echo "<p class='product-card-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>";
                        echo "<p class='product-card-id'>ID: " . $produto['id_produto'] . "</p>";
                        echo "<small class='disclaimer'>
        Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
      </small>";
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


    </main>

    <script src="./index.js" defer></script>
</body>

</html>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>