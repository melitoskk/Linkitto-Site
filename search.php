<?php
include 'config.php';

$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

if ($searchQuery) {
    // Cria a conexão com o banco
    $conn = new mysqli($env_ip, $env_user, $env_password, $env_db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consulta para buscar produtos que contenham o termo de pesquisa
    $sql = "SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome, 
            l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja 
            FROM produtos p
            JOIN categorias c ON p.categoria_id = c.id
            JOIN lojas l ON p.loja_produto_id = l.id
            WHERE p.id_produto LIKE ? OR p.nome_produto LIKE ?";

    $stmt = $conn->prepare($sql);
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param('ss', $likeQuery, $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    // Exibe os resultados da busca
    if ($result->num_rows > 0) {
        while ($produto = $result->fetch_assoc()) {
            echo "<div class='card horizontal-card'>";
            echo "<div class='card-image'>";
            echo "<img src='" . $produto['imagem_produto'] . "' alt='" . htmlspecialchars($produto['nome_produto']) . "' class='main-image'>";
            echo "<div class='logo-overlay'><img src='" . $produto['logo_loja'] . "' alt='" . htmlspecialchars($produto['nome_loja']) . "'></div>";
            echo "</div>";
            echo "<div class='card-details'>";
            echo "<strong class='card-name'>" . htmlspecialchars($produto['nome_produto']) . "</strong>";
            echo "<p class='card-category'>Categoria: " . htmlspecialchars($produto['categoria_nome']) . "</p>";
            echo "<p class='card-id'>ID: " . $produto['id_produto'] . "</p>";
            echo "<small class='disclaimer'>
                                    Você comprará por <a href='" . $produto['link_loja'] . "' target='_blank'>" . htmlspecialchars($produto['nome_loja']) . "</a>
                                  </small>";
            echo "<a href='" . $produto['link_produto'] . "' target='_blank' onclick='registerClick(" . $produto['id_produto'] . ")' class='buy-button'>Ver Produto</a>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>Sem resultados para sua pesquisa.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>