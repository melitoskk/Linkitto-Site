<?php
include 'config.php';

// Função para buscar produtos de uma categoria específica ou todos os produtos
function getProdutos($conn, $categoriaId = 0, $limit = null, $orderBy = 'p.id DESC')
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
    
    // Se for fornecido um ID de categoria, filtra os produtos por categoria
    if ($categoriaId != 0) {
        $sql .= " WHERE p.categoria_id = " . intval($categoriaId);
    }
    
    $sql .= " ORDER BY $orderBy";
    
    // Limita o número de produtos, se necessário
    if ($limit !== null) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return $conn->query($sql);
}

// Verifica se há um ID de categoria na URL
$categoriaId = isset($_GET['categoria_id']) ? $_GET['categoria_id'] : 0; // Usa 0 caso não exista categoria_id

// Consulta os produtos (todos ou da categoria específica)
$produtos = getProdutos($conn, $categoriaId, 10); // Limita a 10 produtos por consulta

// Obtém o nome da categoria para exibir no título, se existir
$categoria = null;
if ($categoriaId != 0) {
    $categoria = $conn->query("SELECT nome FROM categorias WHERE id = " . intval($categoriaId))->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkitto - <?= $categoria ? htmlspecialchars($categoria['nome']) : 'Todos os Produtos' ?></title>
    <link rel="icon" href="./imgs/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="header">
        
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
        <a href="./" class="logo-link">
            <img class="logo" src="../imgs/logo.png" alt="Logo">
        </a>
        <div class="search-bar" id="search-bar">
            <input type="text" placeholder="Pesquisar" id="search-input" autocomplete="off">
            <button id="search-button"><i class="fas fa-search"></i></button>
            <div class="search-suggestions" id="search-suggestions"></div>
        </div>
    </header>

    <main class="category-content">
        <!-- Seção de Produtos -->
        <div class="section-category">
            <h3><?= $categoria ? 'Produtos da Categoria: ' . htmlspecialchars($categoria['nome']) : 'Todos os Produtos' ?></h3>
            <div class="product-table">
                <?php if ($produtos->num_rows > 0): ?>
                    <?php while ($produto = $produtos->fetch_assoc()): ?>
                        <div class="card vertical-card">
                            <div class="card-image">
                                <img src="<?= htmlspecialchars($produto['imagem_produto']) ?>" alt="<?= htmlspecialchars($produto['nome_produto']) ?>" class="main-image">
                                <div class="logo-overlay"><img src="<?= htmlspecialchars($produto['logo_loja']) ?>" alt="<?= htmlspecialchars($produto['nome_loja']) ?>"></div>
                            </div>
                            <div class="card-details">
                                <strong class="card-name"><?= htmlspecialchars($produto['nome_produto']) ?></strong>
                                <p class="card-category">Categoria: <?= htmlspecialchars($produto['categoria_nome']) ?></p>
                                <p class="card-id">ID: <?= $produto['id_produto'] ?></p>
                                <small class="disclaimer">
                                    Você comprará por <a href="<?= htmlspecialchars($produto['link_loja']) ?>" target="_blank"><?= htmlspecialchars($produto['nome_loja']) ?></a>
                                </small>
                                <a href="<?= htmlspecialchars($produto['link_produto']) ?>" target="_blank" onclick="registerClick(<?= $produto['id_produto'] ?>)" class="buy-button">Ver Produto</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Sem produtos disponíveis nesta categoria.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="./index.js" defer></script>
</body>

</html>

<?php
$conn->close();
?>
