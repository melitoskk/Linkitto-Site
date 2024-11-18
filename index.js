// Função de busca de sugestões
function fetchSuggestions() {
    const query = document.getElementById('search-input').value;  // Pega o texto digitado
    const suggestionsDiv = document.getElementById('search-suggestions');  // A div onde as sugestões vão aparecer

    // Exibe as sugestões somente se houver texto digitado
    if (query.length < 1) {
        suggestionsDiv.style.display = 'none';
        return;
    }

    console.log("Buscando por:", query);  // Verifica o valor que está sendo enviado
    suggestionsDiv.style.display = 'block'; // Exibe as sugestões

    // Faz a requisição GET para o script PHP que vai retornar as sugestões
    fetch(`search.php?query=${query}`)
        .then(response => response.text())  // Pega a resposta como texto
        .then(data => {
            console.log("Sugestões recebidas:", data);  // Verifique se os dados estão sendo recebidos
            suggestionsDiv.innerHTML = data;  // Preenche a div com as sugestões
        })
        .catch(error => console.error('Erro na busca de sugestões:', error));
}

// Evento para buscar sugestões enquanto digita
document.getElementById('search-input').addEventListener('input', fetchSuggestions);

// Função para registrar cliques no produto
function registerClick(idProduto) {
    fetch('updateClicks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_produto=${idProduto}`,
    }).catch(error => console.error('Erro na requisição:', error));
}

// Alternância de modo escuro
const darkModeToggle = document.getElementById('dark-mode-toggle');
const body = document.body;

// Verifica o estado do modo escuro no localStorage
const darkModeEnabled = localStorage.getItem('dark-mode') === 'enabled';
if (darkModeEnabled) body.classList.add('dark-mode');

// Alterna o modo escuro ao clicar no botão
darkModeToggle.addEventListener('click', () => {
    const isDarkMode = body.classList.toggle('dark-mode');
    if (isDarkMode) {
        localStorage.setItem('dark-mode', 'enabled');
    } else {
        localStorage.removeItem('dark-mode');
    }
});

// Funções de navegação do carousel
const prevButton = document.getElementById('prevBtn');
const nextButton = document.getElementById('nextBtn');
const carousel = document.querySelector('.carousel');
const slides = document.querySelectorAll('.carousel-slide');
let currentIndex = 0;

// Atualiza o carousel para o índice atual
function updateCarousel() {
    const offset = -currentIndex * 100;
    console.log(`Movendo o carousel para: translateX(${offset}%)`);
    carousel.style.transform = `translateX(${offset}%)`;
}

// Funções para mover o carousel para o próximo/previo slide
function moveCarousel(direction) {
    currentIndex = (currentIndex + direction + slides.length) % slides.length; // Move circularmente
    updateCarousel();
}

// Eventos de clique nos botões de navegação
prevButton.addEventListener('click', () => moveCarousel(-1));
nextButton.addEventListener('click', () => moveCarousel(1));

// Inicializa o carousel com a primeira imagem
updateCarousel();

// Função para fechar as sugestões ao clicar fora da barra de pesquisa
function closeSuggestions(event) {
    const searchBar = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    if (!searchBar.contains(event.target) && !searchSuggestions.contains(event.target)) {
        searchSuggestions.style.display = 'none';
        searchSuggestions.innerHTML = '';  // Limpa as sugestões
    }
}

// Evento para fechar sugestões quando clicar fora
document.addEventListener('click', closeSuggestions);

// Exibe sugestões ao clicar na barra de pesquisa
document.getElementById('search-input').addEventListener('click', () => {
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    if (searchInput.value.trim() !== '') fetchSuggestions();
});

// Exibe ou oculta a barra de pesquisa em dispositivos móveis
if (window.matchMedia("(max-width: 768px)").matches) {
    document.addEventListener("DOMContentLoaded", () => {
        const searchButton = document.getElementById("search-button");
        const searchInput = document.getElementById("search-input");

        searchButton.addEventListener("click", (e) => {
            e.preventDefault(); // Evita o comportamento padrão do botão
            searchInput.classList.toggle("active");
            searchInput.style.display = searchInput.classList.contains("active") ? "block" : "none";
            searchInput.focus();
        });
    });
}
