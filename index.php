<?php
include 'config.php';

// Função para buscar produtos de uma categoria específica
function getProdutosPorCategoria($conn, $categoriaId = null, $limit = null, $orderBy = 'p.id DESC')
{
    $sql = "SELECT 
                p.nome_produto, 
                p.id_produto, 
                p.imagem_produto, 
                p.link_produto, 
                p.categoria_id, 
                p.clicks,
                c.nome AS categoria_nome, 
                l.nome AS nome_loja, 
                l.imagem AS logo_loja, 
                l.link AS link_loja 
            FROM produtos p
            JOIN categorias c ON p.categoria_id = c.id
            JOIN lojas l ON p.loja_produto_id = l.id";
    if ($categoriaId !== null) {
        $sql .= " WHERE p.categoria_id = " . intval($categoriaId);
    }
    $sql .= " ORDER BY $orderBy";
    if ($limit !== null) {
        $sql .= " LIMIT " . intval($limit);
    }
    return $conn->query($sql);
}

// Consultas principais
$categorias = $conn->query("SELECT id, nome, imagem FROM categorias");
$produtosRecentes = getProdutosPorCategoria($conn, null, 4);
$produtosPopulares = getProdutosPorCategoria($conn, null, 4, 'p.clicks DESC');
$slides = $conn->query("SELECT * FROM slides WHERE url != '' LIMIT 5");
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkitto</title>
    <link rel="icon" href="./imgs/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="darkmode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="./" class="logo-link">
            <img class="logo" src="../imgs/logo.png" alt="Logo">
        </a>
        <nav class="nav-icons">
            <a href="https://www.instagram.com/linkitto.br/" class="instagram-icon" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.youtube.com/@linkitto_br" class="youtube-icon" target="_blank">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="https://www.tiktok.com/@linkitto.br" class="tiktok-icon" target="_blank">
                <i class="fab fa-tiktok"></i>
            </a>
            <div id="dark-mode-toggle">
                <i class="darkmode-icon sun fas fa-sun"></i>
                <i class="darkmode-icon moon fas fa-moon"></i>
            </div>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Pesquisar" id="search-input" autocomplete="off">
            <button id="search-button"><i class="fas fa-search"></i></button>
            <div class="search-suggestions" id="search-suggestions"></div>
        </div>
    </header>

    <main class="content">
        <!-- Menu Categorias -->
        <aside class="menu-categorias">
            <?php while ($categoria = $categorias->fetch_assoc()): ?>
                <a href="categorias.php?categoria_id=<?= $categoria['id'] ?>">
                    <button class="category-btn"
                        style="background-image: url('<?= htmlspecialchars($categoria['imagem']) ?>');">
                        <span><?= htmlspecialchars($categoria['nome']) ?></span>
                    </button>
                </a>
            <?php endwhile; ?>
        </aside>

        <!-- Carousel -->
        <div class="carousel-container">
            <div class="carousel-prev" id="prevBtn"><i class="fa-solid fa-arrow-left"></i></div>
            <div class="carousel-next" id="nextBtn"><i class="fa-solid fa-arrow-right"></i></div>
            <div class="carousel">
                <?php while ($slide = $slides->fetch_assoc()): ?>
                    <div class="carousel-slide">
                        <img src="<?= htmlspecialchars($slide['url']) ?>" alt="Imagem">
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Seção de Produtos -->
        <?php
        function renderProdutos($produtos, $titulo)
        {
            if($titulo == 'Em Alta'){
                echo "<div class='em-alta'>";
            }else{
                echo "<div class='section-category'>";
            }
            
            echo "<h3>$titulo</h3>";
            echo "<div class='product-row'>";
            if ($produtos->num_rows > 0) {
                while ($produto = $produtos->fetch_assoc()) {
                    echo "<div class='card vertical-card'>";
                    echo "<div class='card-image'>";
                    echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
                    echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>";
                    echo "</div>";
                    echo "<div class='card-details'>";
                    echo "<strong class='card-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
                    echo "<p class='card-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>";
                    echo "<p class='card-id'>ID: " . $produto['id_produto'] . "</p>";
                    if($titulo == 'Em Alta'){
                        echo "<p class='card-views'>Views: " . $produto['clicks'] . "</p>";
                    }
                    echo "<small class='disclaimer'>
                                    Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
                                  </small>";
                    echo "<a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")' class='buy-button'>Ver Produto</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Sem produtos disponíveis.</p>";
            }
            echo "</div></div>";
        }

        renderProdutos($produtosPopulares, "Em Alta");
        renderProdutos($produtosRecentes, "Mais Recentes");
        ?>

        <!-- Produtos por Categoria -->
        <?php
        $categorias->data_seek(0); // Reseta ponteiro para reutilizar a consulta
        while ($categoria = $categorias->fetch_assoc()):
            $produtosCategoria = getProdutosPorCategoria($conn, $categoria['id'], 4);
            if ($produtosCategoria->num_rows > 0) {
                renderProdutos($produtosCategoria, $categoria['nome']);
            }
        endwhile;
        ?>
    </main>

    <script src="./index.js" defer></script>
</body>

</html>

<?php
$conn->close();
?>