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
        isHovered = true;
        clearInterval(autoSlideInterval); // Pausa a navegação automática
    });
}

// Retomar a navegação automática quando o mouse sai do carrossel
if (carousel) {
    carousel.addEventListener('mouseleave', () => {
        isHovered = false;
        resetAutoSlide(); // Reinicia o timer de navegação automática
    });
}

// Função para fechar as sugestões ao clicar fora da barra de pesquisa
function closeSuggestions(event) {
    const searchBar = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    const searchBut = document.getElementById("search-button");

    // Verifica se o clique foi fora da barra de pesquisa ou das sugestões
    if (!searchBar.contains(event.target) && !searchSuggestions.contains(event.target) && !searchBut.contains(event.target)) {
        searchSuggestions.style.display = 'none';
        searchSuggestions.innerHTML = '';  // Limpa as sugestões
        searchBar.classList.remove('active');  // Esconde o campo de pesquisa no mobile
    }
}

// Detecta se o usuário clica no campo de pesquisa, sem considerar o toque no teclado
document.getElementById('search-input').addEventListener('focus', () => {
    const searchInput = document.getElementById('search-input');
    searchInput.style.display = 'block';  // Garante que o campo fique visível quando tocado no mobile
});

// Evento para fechar sugestões quando clicar fora
document.addEventListener('click', closeSuggestions);

// Exibe sugestões ao clicar na barra de pesquisa
document.getElementById('search-input').addEventListener('click', () => {
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    if (searchInput.value.trim() !== '') fetchSuggestions();
});

// Exibe ou oculta a barra de pesquisa em dispositivos móveis
document.addEventListener("DOMContentLoaded", () => {
    const searchButton = document.getElementById("search-button");
    const searchInput = document.getElementById("search-input");

    searchButton.addEventListener("click", (e) => {
        e.preventDefault(); // Evita o comportamento padrão do botão
        if (window.innerWidth <= 767) {
            searchInput.classList.toggle("active");
            searchInput.style.display = 'block';
            searchInput.focus();
        }
    });
});

// Ajuste do comportamento de fechamento ao clicar fora do campo no mobile
window.addEventListener('touchstart', function(event) {
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');

    // Se o clique for fora do campo de pesquisa e das sugestões, esconde a pesquisa
    if (!searchInput.contains(event.target) && !searchSuggestions.contains(event.target)) {
        searchSuggestions.style.display = 'none';
        searchSuggestions.innerHTML = '';  // Limpa as sugestões
        searchInput.classList.remove('active');  // Fecha o campo de pesquisa no mobile
    }
});

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
function toggleSearchInputVisibility() {
    const searchInput = document.getElementById('search-input');
    if (window.matchMedia("(min-width: 768px)").matches) {
        searchInput.style.display = 'block';  // Sempre visível no desktop
    } else {
        searchInput.style.display = 'none';  // Oculta no mobile
    }
}

// Chama a função inicialmente e sempre que a tela for redimensionada
toggleSearchInputVisibility();
window.addEventListener('resize', toggleSearchInputVisibility);

// Função para atualizar a ordem dos elementos no header, dependendo do tamanho da tela
function updateHeaderOrder() {
    const header = document.querySelector('.header');
    const logo = document.querySelector('.logo-link');
    const searchBar = document.querySelector('.search-bar');
    const navIcons = document.querySelector('.nav-icons');

    if (window.matchMedia("(max-width: 767px)").matches) {
        // Mobile: logo, navicons, search bar
        header.appendChild(logo);
        header.appendChild(navIcons);
        header.appendChild(searchBar);
    } else {
        // Desktop: logo, search bar, navicons
        header.appendChild(logo);
        header.appendChild(searchBar);
        header.appendChild(navIcons);
    }
}

// Inicializa a ordem correta ao carregar e sempre que redimensionar
updateHeaderOrder();
window.addEventListener('resize', updateHeaderOrder);
