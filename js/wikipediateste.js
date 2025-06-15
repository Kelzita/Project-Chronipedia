
const configOpenBtn = document.getElementById('config-open');
const configPanel = document.getElementById('config-panel');
const configCloseBtn = document.getElementById('config-close');
const overlay = document.getElementById('overlay');
const selectTheme = document.getElementById('select-theme');
const body = document.body;

// Função para abrir painel
function openConfig() {
  configPanel.classList.add('open');
  overlay.classList.add('active');
  configPanel.setAttribute('aria-hidden', 'false');
  // Foco para acessibilidade
  configCloseBtn.focus();
}

// Função para fechar painel
function closeConfig() {
  configPanel.classList.remove('open');
  overlay.classList.remove('active');
  configPanel.setAttribute('aria-hidden', 'true');
  configOpenBtn.focus();
}

// Abrir painel ao clicar na engrenagem
configOpenBtn.addEventListener('click', openConfig);
configOpenBtn.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' || e.key === ' ') {
    openConfig();
    e.preventDefault();
  }
});

// Fechar painel ao clicar no X
configCloseBtn.addEventListener('click', closeConfig);

// Fechar painel ao clicar no overlay
overlay.addEventListener('click', closeConfig);

// Fechar painel com ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && configPanel.classList.contains('open')) {
    closeConfig();
  }
});

// Carregar tema salvo no armazenamento local
const savedTheme = localStorage.getItem('tema') || 'tema-escuro';
body.classList.add(savedTheme);
selectTheme.value = savedTheme;

// Alterar tema e salvar preferência
selectTheme.addEventListener('change', () => {
  // Remove todos os temas
  body.classList.remove('tema-escuro', 'tema-claro', 'tema-ouro', 'tema-marinho');
  // Adiciona o novo tema
  body.classList.add(selectTheme.value);
  // Salva localmente
  localStorage.setItem('tema', selectTheme.value);
});
