// Função para buscar sugestões enquanto digita
function fetchSuggestions() {
    const query = document.getElementById('search-input').value;  // Pega o texto digitado
    const suggestionsDiv = document.getElementById('search-suggestions');  // A div onde as sugestões vão aparecer

    // Exibe as sugestões somente se houver texto digitado
    if (query.length < 1) {
        suggestionsDiv.style.display = 'none';
        return;
    }

    suggestionsDiv.style.display = 'block'; // Exibe as sugestões

    // Faz a requisição GET para o script PHP que vai retornar as sugestões
    fetch(`search.php?query=${query}`)
        .then(response => response.text())  // Pega a resposta como texto
        .then(data => {
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
let autoSlideInterval; // Variável para armazenar o ID do intervalo de navegação automática

// Atualiza o carousel para o índice atual
function updateCarousel() {
    const offset = -currentIndex * 100;
    if (carousel) {
        carousel.style.transform = `translateX(${offset}%)`;
    }
}

// Funções para mover o carousel para o próximo/previo slide
function moveCarousel(direction) {
    currentIndex = (currentIndex + direction + slides.length) % slides.length; // Move circularmente
    updateCarousel();
    resetAutoSlide(); // Reseta o timer de navegação automática a cada clique
}

// Função para avançar automaticamente o carrossel a cada 3 segundos
function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
        moveCarousel(1); // Avança para o próximo slide
    }, 3000); // A cada 3 segundos
}

// Função para resetar o timer de navegação automática
function resetAutoSlide() {
    clearInterval(autoSlideInterval); // Limpa o timer atual
    startAutoSlide(); // Inicia um novo timer
}

// Eventos de clique nos botões de navegação
if (prevButton) {
    prevButton.addEventListener('click', () => moveCarousel(-1));
}
if (nextButton) {
    nextButton.addEventListener('click', () => moveCarousel(1));
}

// Inicializa o carousel com a primeira imagem
updateCarousel();

// Inicia a navegação automática quando a página carrega
startAutoSlide();

// Pausa a navegação automática quando o mouse entra no carrossel
if (carousel) {
    carousel.addEventListener('mouseenter', () => {
        clearInterval(autoSlideInterval); // Pausa a navegação automática
    });
}

// Retomar a navegação automática quando o mouse sai do carrossel
if (carousel) {
    carousel.addEventListener('mouseleave', () => {
        resetAutoSlide(); // Reinicia o timer de navegação automática
    });
}

// Função para fechar as sugestões quando o campo de pesquisa perder o foco
function closeSuggestions() {
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');

    // Verifica se o campo de pesquisa não está em foco e esconde as sugestões
    if (document.activeElement !== searchInput) {
        console.log('foi essa')
        searchSuggestions.style.display = 'none';
        searchSuggestions.innerHTML = '';  // Limpa as sugestões
        searchInput.classList.remove('active');
    }
}
document.addEventListener('click', (event) => {
    const searchInput = document.getElementById('search-bar');
    const suggestionsDiv = document.getElementById('search-suggestions');

    // Verifica se o clique foi fora do input e das sugestões
    if (
        !searchInput.contains(event.target) && // Não está no input
        !suggestionsDiv.contains(event.target) // Não está nas sugestões
    ) {
        suggestionsDiv.style.display = 'none'; // Esconde as sugestões
        searchInput.classList.remove('active');
    }
});

// Exibe sugestões ao clicar na barra de pesquisa
document.getElementById('search-input').addEventListener('click', () => {
    const searchInput = document.getElementById('search-input');
    const suggestionsDiv = document.getElementById('search-suggestions');

    if (searchInput.value.trim() !== '') {
        suggestionsDiv.style.display = 'block'; // Exibe as sugestões
        fetchSuggestions(); // Carrega as sugestões, se necessário
    }
});

const searchInput = document.getElementById('search-input');
// Exibe sugestões ao clicar na barra de pesquisa
document.getElementById('search-input').addEventListener('click', () => {
    
    const searchSuggestions = document.getElementById('search-suggestions');
    if (searchInput.value.trim() !== '') fetchSuggestions();
});

// Exibe ou oculta a barra de pesquisa em dispositivos móveis
const searchButton = document.getElementById('search-button');
const searchBar = document.querySelector('.search-bar');

searchButton.addEventListener('click', () => {
    searchBar.classList.toggle('active');
    searchInput.focus();
});

// Ajuste do comportamento de fechamento ao clicar fora do campo no mobil

// Ajuste de layout de cards dependendo da largura da tela
function atualizarLayoutCards() {
    if (window.matchMedia("(min-width: 768px)").matches) {
        const emAltaCards = document.querySelectorAll('.em-alta .card');
        emAltaCards.forEach(card => {
            card.classList.remove('vertical-card');
            card.classList.add('horizontal-card');
        });
    } else {
        const emAltaCards = document.querySelectorAll('.em-alta .card');
        emAltaCards.forEach(card => {
            card.classList.remove('horizontal-card');
            card.classList.add('vertical-card');
        });
    }
}

// Chama a função inicialmente para configurar o layout de acordo com o tamanho da tela
atualizarLayoutCards();

// Adiciona um listener para o evento de redimensionamento da janela
window.addEventListener('resize', atualizarLayoutCards);

// Função para garantir que o search-input fique visível no desktop

// Chama a função inicialmente e sempre que a tela for redimensionada

// Função para atualizar a ordem dos elementos no header, dependendo do tamanho da tela

