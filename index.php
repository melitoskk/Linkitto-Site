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
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        <!-- Sidebar - Categorias -->
        <aside class="sidebar-categorias">
            <h2>Categorias</h2> <!-- Título da seção -->
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


        <!-- Main Content -->
        <main class="content">
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
                // Obtém o botão de alternância e o body
                const darkModeToggle = document.getElementById('dark-mode-toggle');
                const body = document.body;

                // Verifica se o modo escuro foi previamente salvo no localStorage
                if (localStorage.getItem('dark-mode') === 'enabled') {
                    body.classList.add('dark-mode');  // Aplica o modo escuro quando a página for carregada
                }

                // Evento de clique para alternar o modo
                darkModeToggle.addEventListener('click', () => {
                    body.classList.toggle('dark-mode'); // Alterna entre o modo claro e escuro

                    // Salva a preferência do usuário no localStorage
                    if (body.classList.contains('dark-mode')) {
                        localStorage.setItem('dark-mode', 'enabled'); // Ativa o modo escuro
                    } else {
                        localStorage.removeItem('dark-mode'); // Desativa o modo escuro
                    }
                });

                // Selecionando os elementos do DOM
                const prevButton = document.getElementById('prevBtn');
                const nextButton = document.getElementById('nextBtn');
                const carousel = document.querySelector('.carousel');
                const slides = document.querySelectorAll('.carousel-slide');  // Certifique-se de que isso seleciona apenas os slides
                let currentIndex = 0; // Índice da imagem atual

                // Função para mover o carousel para a esquerda
                function moveToPrevSlide() {
                    console.log('Current index before move:', currentIndex);
                    if (currentIndex === 0) {
                        currentIndex = slides.length - 1; // Voltar para o final
                    } else {
                        currentIndex--;
                    }
                    updateCarousel();
                    console.log('Current index after move:', currentIndex);
                }

                // Função para mover o carousel para a direita
                function moveToNextSlide() {
                    console.log('Current index before move:', currentIndex);
                    if (currentIndex === slides.length - 1) {
                        currentIndex = 0; // Voltar para o início
                    } else {
                        currentIndex++;
                    }
                    updateCarousel();
                    console.log('Current index after move:', currentIndex);
                }

                // Atualiza o carousel para mostrar a imagem no índice atual
                function updateCarousel() {
                    const offset = -currentIndex * 100; // Desloca as imagens em 100% por vez
                    console.log(`Moving carousel to: translateX(${offset}%)`);
                    carousel.style.transform = `translateX(${offset}%)`;
                }

                // Adicionando os eventos de clique nos botões
                prevButton.addEventListener('click', moveToPrevSlide);
                nextButton.addEventListener('click', moveToNextSlide);

                // Inicializando o carousel com a primeira imagem
                updateCarousel();





            </script>
            <!-- No final do seu arquivo HTML -->
            <script>
                document.addEventListener('click', function (event) {
                    console.log("Clicou em:", event.target);
                    const searchBar = document.getElementById('search-input');
                    const searchSuggestions = document.getElementById('search-suggestions');

                    // Verifica se o clique foi fora da search-bar ou search-suggestions
                    if (!searchBar.contains(event.target) && !searchSuggestions.contains(event.target)) {
                        console.log("fora");
                        searchSuggestions.innerHTML = '';  // Limpa as sugestões
                    } else {
                        console.log("dentro");
                    }
                });
                document.getElementById('search-input').addEventListener('click', function (event) {
                    const searchInput = document.getElementById('search-input');
                    const searchSuggestions = document.getElementById('search-suggestions');

                    // Verifica se a barra de pesquisa não está vazia e exibe as sugestões
                    if (searchInput.value.trim() !== '') {
                        fetchSuggestions();  // Chama a função para exibir as sugestões
                    }
                });

                document.addEventListener('click', function (event) {
                    const searchBar = document.getElementById('search-bar');
                    const searchSuggestions = document.getElementById('search-suggestions');

                    // Verifica se o clique foi fora da barra de pesquisa ou da lista de sugestões
                    if (searchBar && !searchBar.contains(event.target)) {
                        searchSuggestions.innerHTML = '';  // Limpa as sugestões
                    }
                });

            </script>
</body>

</html>

</main>

</div>
</body>

</html>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>