<?php
include 'config.php';

// Conexão com o banco de dados
$servername = $env_ip;
$username = $env_user;
$password = $env_password;
$dbname = $env_db; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Recuperar o ID da categoria a partir da URL
$categoria_id = isset($_GET['categoria_id']) ? (int) $_GET['categoria_id'] : 0;

// Inicializa o nome da categoria como vazio
$categoria_nome = '';

// Buscar o nome da categoria com base no ID
if ($categoria_id > 0) {
    $sql_categoria = "SELECT nome FROM categorias WHERE id = $categoria_id";
    $result_categoria = $conn->query($sql_categoria);
    
    if ($result_categoria->num_rows > 0) {
        $categoria = $result_categoria->fetch_assoc();
        $categoria_nome = $categoria['nome'];
    } else {
        $categoria_nome = 'Categoria não encontrada';  // Caso não encontre a categoria
    }
}

// Buscar os produtos da categoria selecionada
$sql_produtos_categoria = "
    SELECT p.nome_produto, p.id_produto, p.imagem_produto, p.link_produto, p.categoria_id, c.nome AS categoria_nome, 
           l.nome AS nome_loja, l.imagem AS logo_loja, l.link AS link_loja
    FROM produtos p
    JOIN categorias c ON p.categoria_id = c.id
    JOIN lojas l ON p.loja_produto_id = l.id
    WHERE p.categoria_id = $categoria_id
    ORDER BY p.id_produto DESC
";

$result_produtos_categoria = $conn->query($sql_produtos_categoria);

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="./imgs/icon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linkitto - Categoria: <?php echo htmlspecialchars($categoria_nome); ?></title> <!-- Título com nome da categoria -->
    <link rel="stylesheet" href="categorias.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="./" class="logo-link">
            <img class="logo" src='../imgs/logo.png' alt="Logo">
        </a>
        <div class="search-bar">
            <input type="text" placeholder="Pesquisar" id="search-input">
            <button>
                <i class="fas fa-search"></i>
            </button>
            <div class="search-suggestions" id="search-suggestions"></div>
        </div>
        <nav class="nav-icons">
            <a href="#" class="instagram-icon"><i class="fab fa-instagram-square"></i></a>
            <a href="#" class="youtube-icon"><i class="fab fa-youtube"></i></a>
            <a href="#" class="tiktok-icon"><i class="fab fa-tiktok"></i></a>
            <div id="dark-mode-toggle">
                <i class="darkmode-icon sun fas fa-sun"></i>
                <i class="darkmode-icon moon fas fa-moon"></i>
            </div>
        </nav>
    </header>

    <div class="container">
        <!-- Main Content -->
        <main class="content">
            <div class="section-category">
                <h2>Categoria: <?php echo htmlspecialchars($categoria_nome); ?></h2> <!-- Exibe o nome da categoria -->
                <div class="product-row"> <!-- Exibe os produtos dessa categoria -->
                    <?php
                    if ($result_produtos_categoria->num_rows > 0) {
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
                    } else {
                        echo "<p>Sem produtos nesta categoria.</p>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Função de pesquisa
        function fetchSuggestions() {
            const query = document.getElementById('search-input').value;  // Pega o texto digitado
            const suggestionsDiv = document.getElementById('search-suggestions');  // A div onde as sugestões vão aparecer

            // Limpa as sugestões anteriores
            suggestionsDiv.innerHTML = '';

            // Se o campo de pesquisa estiver vazio, não faz nada
            if (query.length < 1) {
                return;
            }

            console.log("Buscando por:", query);  // Verifica o valor que está sendo enviado

            // Faz a requisição GET para o script PHP que vai retornar as sugestões
            fetch('search.php?query=' + query)
                .then(response => response.text())  // Pega a resposta como texto
                .then(data => {
                    console.log("Sugestões recebidas:", data);  // Verifique se os dados estão sendo recebidos
                    suggestionsDiv.innerHTML = data;  // Preenche a div com as sugestões
                })
                .catch(error => console.error('Erro na busca de sugestões:', error));
        }

        // Vincule o evento ao input de pesquisa
        document.getElementById('search-input').addEventListener('input', fetchSuggestions);

        function registerClick(idProduto) {
            fetch('updateClicks.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_produto=' + idProduto,
            }).catch(error => console.error('Erro na requisição:', error));
        }

        // Modo escuro
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const body = document.body;

        if (localStorage.getItem('dark-mode') === 'enabled') {
            body.classList.add('dark-mode');
        }

        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('dark-mode', 'enabled');
            } else {
                localStorage.removeItem('dark-mode');
            }
        });
    </script>
</body>

</html>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>
