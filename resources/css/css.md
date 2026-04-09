<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>GlassForge | Gestão de Produtos</title>
  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
  <!-- Font Awesome 6 (gratuito para ícones) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Tailwind CSS v3 CDN (utilitários) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Sobrescreve configurações do Tailwind para não conflitar com classes customizadas -->
  <style>
    /* RESET & CONFIGURAÇÕES GLOBAIS DE PERFORMANCE */
    *, *::before, *::after {
      box-sizing: border-box;
    }

    /* =============================================
       CUSTOM GLASSMORPHISM + TEMA (BASE DO USUÁRIO)
       OTIMIZADO: MARGENS CORRIGIDAS, NAVBAR FIXA,
       ANIMAÇÕES SUTIS, PERFORMANCE ALTA
       ============================================= */
    
    /* Importação do Tailwind (simbolica, já incluso via CDN, mas mantemos a estrutura do usuário) */
    /* As diretivas @import não funcionam no CSS inline, mas o Tailwind CDN já fornece utilidades.
       Mantemos as classes personalizadas abaixo para garantir glassmorphism premium. */
    
    /* TEMA ESCURO COM BACKGROUND DINÂMICO PARA DESTACAR O VIDRO */
    html, body {
      background: #020617 !important; /* fallback da cor original */
      margin: 0;
      padding: 0;
      min-height: 100vh;
      overflow-x: hidden;
      font-family: 'Inter', sans-serif;
      -webkit-font-smoothing: antialiased;
      position: relative;
    }

    /* CAMADA DE FUNDO CRIATIVA: GRADIENTE + TEXTURA SUTIL + EFEITO AURORA
       Isso faz o glassmorphism realmente saltar aos olhos! */
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 20% 30%, rgba(79, 70, 229, 0.25) 0%, rgba(2, 6, 23, 0.85) 70%),
        repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, rgba(255,255,255,0.02) 2px, transparent 2px, transparent 8px);
      z-index: -2;
      pointer-events: none;
    }

    /* Efeito de grão sutil (melhora a percepção do blur) */
    body::after {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJmIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iLjUiIG51bU9jdGF2ZXM9IjMiLz48L2ZpbHRlcj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWx0ZXI9InVybCgjZikiIG9wYWNpdHk9IjAuMDYiLz48L3N2Zz4=');
      background-repeat: repeat;
      opacity: 0.2;
      pointer-events: none;
      z-index: -1;
    }

    /* ===== CLASSES GLASSMORPHISM REFORÇADAS (estilo definido pelo usuário, porém mais visível) ===== */
    .z-glass {
      background-color: rgba(15, 23, 42, 0.8) !important; /* mais opaco para melhor leitura, porém translúcido */
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      box-shadow: 0 12px 35px -8px rgba(0, 0, 0, 0.5);
    }

    .z-card {
      background-color: rgba(15, 23, 42, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.18);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border-radius: 1.5rem;
      transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
      box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
    }

    .z-card:hover {
      background-color: rgba(30, 41, 59, 0.85);
      border-color: rgba(129, 140, 248, 0.6);
      transform: translateY(-4px);
      box-shadow: 0 28px 40px -16px rgba(0, 0, 0, 0.7);
    }

    /* Botões principais refinados (tamanhos proporcionais) */
    .z-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      padding: 0.7rem 1.6rem;
      border-radius: 2rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      font-size: 0.7rem;
      color: white;
      background: linear-gradient(105deg, #4f46e5 0%, #6366f1 100%);
      box-shadow: 0 8px 18px -6px rgba(79, 70, 229, 0.4);
      transition: all 0.25s ease;
      cursor: pointer;
      border: none;
    }

    .z-btn:hover {
      filter: brightness(1.08);
      transform: translateY(-2px);
      box-shadow: 0 12px 22px -6px rgba(79, 70, 229, 0.6);
    }

    .z-btn-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.7rem 1.5rem;
      border-radius: 2rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      font-size: 0.7rem;
      color: #e2e8f0;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.25s ease;
      cursor: pointer;
      backdrop-filter: blur(4px);
    }

    .z-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(129, 140, 248, 0.7);
      transform: translateY(-2px);
      color: white;
    }

    /* Inputs elegantes e compactos */
    .z-input {
      width: 100%;
      background-color: rgba(2, 6, 23, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 1rem;
      padding: 0.85rem 1.2rem;
      color: #f1f5f9;
      font-size: 0.9rem;
      outline: none;
      transition: all 0.3s;
      backdrop-filter: blur(4px);
    }

    .z-input:focus {
      background-color: rgba(15, 23, 42, 0.95);
      border-color: #818cf8;
      box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.2);
    }

    /* NAVBAR CORRIGIDA: posição fixa no topo, sem margens desnecessárias, centralizada */
    .z-nav {
      position: fixed;
      top: 1.2rem;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1000;
      padding: 0.65rem 1.8rem;
      border-radius: 3rem;
      border: 1px solid rgba(255, 255, 255, 0.2);
      background-color: rgba(2, 6, 23, 0.75);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      display: flex;
      align-items: center;
      gap: 2rem;
      box-shadow: 0 12px 28px -12px rgba(0, 0, 0, 0.6);
      width: auto;
      max-width: 90vw;
    }

    .z-link {
      font-size: 0.7rem;
      font-weight: 700;
      color: #cbd5e6;
      text-transform: uppercase;
      letter-spacing: 0.08rem;
      transition: all 0.2s;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
    }

    .z-link:hover, .z-link.active {
      color: #a5b4fc;
      text-shadow: 0 0 5px rgba(165, 180, 252, 0.5);
    }

    /* Container principal - MARGEM TOPO CORRIGIDA: respeita navbar sem espaços enormes */
    .z-portal {
      min-height: 100vh;
      padding-top: 6rem;    /* Ajuste fino: antes 4.5rem gerava folga, agora navbar fica bem integrada */
      padding-bottom: 4rem;
      padding-left: 1.8rem;
      padding-right: 1.8rem;
      max-width: 80rem;     /* mais largo mas elegante */
      margin-left: auto;
      margin-right: auto;
      display: flex;
      flex-direction: column;
      gap: 2.5rem;
      position: relative;
      z-index: 10;
    }

    /* Ajustes responsivos */
    @media (max-width: 768px) {
      .z-nav {
        gap: 1rem;
        padding: 0.5rem 1.2rem;
        top: 0.8rem;
      }
      .z-link {
        font-size: 0.6rem;
        letter-spacing: 0.05rem;
      }
      .z-portal {
        padding-top: 5rem;
        padding-left: 1rem;
        padding-right: 1rem;
      }
      .z-btn, .z-btn-secondary {
        padding: 0.55rem 1.2rem;
        font-size: 0.65rem;
      }
    }

    /* animações de entrada suave */
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(12px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .view-container {
      animation: fadeSlideUp 0.5s ease-out forwards;
    }

    /* grade de produtos bonita e responsiva */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.8rem;
      margin-top: 0.5rem;
    }

    /* badge de preço */
    .price-tag {
      font-weight: 800;
      background: linear-gradient(135deg, #c084fc, #818cf8);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      font-size: 1.4rem;
    }
  </style>
</head>
<body>

  <!-- NAVBAR ESTÁTICA COM ROTAS FUNCIONAIS (sem refresh) -->
  <nav class="z-nav">
    <button class="z-link" data-nav="list"><i class="fas fa-cube mr-1"></i> Produtos</button>
    <button class="z-link" data-nav="add"><i class="fas fa-plus-circle mr-1"></i> Adicionar</button>
    <button class="z-link" data-nav="edit"><i class="fas fa-pen-alt mr-1"></i> Editar</button>
  </nav>

  <!-- CONTAINER PRINCIPAL: ONDE AS VIEWS SERÃO RENDERIZADAS -->
  <main class="z-portal" id="app-root">
    <!-- Conteúdo dinâmico via JS (produtos, formulários) -->
    <div id="dynamic-view" class="view-container"></div>
  </main>

  <script>
    // ============================================================
    // GERENCIAMENTO DE PRODUTOS (SIMULAÇÃO BACKEND LOCAL)
    // ALTA PERFORMANCE, GLASSMORPHISM TOTAL, FUNCIONALIDADE COMPLETA
    // ============================================================
    
    // Dados iniciais para demonstração (produtos de exemplo)
    let products = [
      { id: '1', name: 'Headset Aurora', price: '299,90', description: 'Áudio imersivo com RGB e cancelamento de ruído híbrido.' },
      { id: '2', name: 'Teclado Mecânico Nova', price: '459,00', description: 'Switch magnético, teclas hot-swap, vidro fosco.' },
      { id: '3', name: 'Mouse Ultra X', price: '189,90', description: 'Sensor óptico 26K, 6 botões programáveis, design ergonômico.' }
    ];
    
    let nextId = 4;
    
    // Helper: salvar no localStorage (persistência opcional)
    function saveToLocal() {
      localStorage.setItem('glass_products', JSON.stringify(products));
    }
    
    function loadFromLocal() {
      const stored = localStorage.getItem('glass_products');
      if (stored) {
        products = JSON.parse(stored);
        // Atualiza o nextId baseado no maior id numérico
        const maxId = products.reduce((max, p) => {
          const numId = parseInt(p.id, 10);
          return isNaN(numId) ? max : Math.max(max, numId);
        }, 0);
        nextId = maxId + 1;
      } else {
        // se não existir, salva os dados iniciais
        saveToLocal();
      }
    }
    loadFromLocal();
    
    // Funções auxiliares de UI
    const viewContainer = document.getElementById('dynamic-view');
    
    // ========== VIEW: LISTAGEM DE PRODUTOS (PÁGINA PRINCIPAL) ==========
    function renderProductList() {
      if (!viewContainer) return;
      if (products.length === 0) {
        viewContainer.innerHTML = `
          <div class="z-card p-10 text-center flex flex-col items-center gap-4" style="backdrop-filter: blur(20px);">
            <i class="fas fa-box-open text-5xl text-indigo-300/70"></i>
            <h3 class="text-xl font-bold text-white">Nenhum produto cadastrado</h3>
            <p class="text-slate-300 max-w-md">Clique em "Adicionar" para começar a construir seu catálogo.</p>
            <button class="z-btn mt-2" id="empty-add-btn"><i class="fas fa-plus"></i> Adicionar produto</button>
          </div>
        `;
        const emptyBtn = document.getElementById('empty-add-btn');
        if (emptyBtn) emptyBtn.addEventListener('click', () => setActiveView('add'));
        return;
      }
      
      const productsHTML = products.map(prod => `
        <div class="z-card p-5 flex flex-col h-full transition-all duration-300">
          <div class="flex justify-between items-start">
            <h3 class="text-xl font-bold text-white tracking-tight">${escapeHtml(prod.name)}</h3>
            <span class="price-tag text-2xl">R$ ${prod.price}</span>
          </div>
          <p class="text-slate-300 text-sm mt-3 leading-relaxed flex-grow">${escapeHtml(prod.description)}</p>
          <div class="flex gap-3 mt-6">
            <button class="z-btn-secondary text-xs w-full edit-product-btn" data-id="${prod.id}"><i class="fas fa-edit"></i> Editar</button>
            <button class="z-btn-secondary text-xs w-full delete-product-btn" data-id="${prod.id}" style="background: rgba(220,38,38,0.2); border-color: rgba(220,38,38,0.3);"><i class="fas fa-trash-alt"></i> Excluir</button>
          </div>
        </div>
      `).join('');
      
      viewContainer.innerHTML = `
        <div class="flex justify-between items-center flex-wrap gap-4 mb-2">
          <div>
            <h2 class="text-2xl font-black bg-gradient-to-r from-indigo-300 to-white bg-clip-text text-transparent">Catalógo Premium</h2>
            <p class="text-slate-400 text-sm mt-1">Produtos com acabamento glassmorphism</p>
          </div>
          <button class="z-btn" id="floating-add-btn"><i class="fas fa-plus"></i> Novo produto</button>
        </div>
        <div class="products-grid">
          ${productsHTML}
        </div>
      `;
      
      // Event listeners para botões dinâmicos
      document.querySelectorAll('.edit-product-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = btn.getAttribute('data-id');
          if (id) setActiveView('edit', id);
        });
      });
      document.querySelectorAll('.delete-product-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = btn.getAttribute('data-id');
          if (id && confirm('Tem certeza que deseja excluir este produto?')) {
            deleteProductById(id);
          }
        });
      });
      const addBtn = document.getElementById('floating-add-btn');
      if (addBtn) addBtn.addEventListener('click', () => setActiveView('add'));
    }
    
    // ========== VIEW: ADICIONAR PRODUTO (PÁGINA ESTÁTICA) ==========
    function renderAddProduct() {
      viewContainer.innerHTML = `
        <div class="z-card max-w-2xl mx-auto w-full p-6 md:p-8">
          <div class="flex items-center gap-3 mb-6 border-b border-white/10 pb-4">
            <i class="fas fa-plus-circle text-indigo-400 text-2xl"></i>
            <h2 class="text-2xl font-bold text-white">Novo Produto</h2>
          </div>
          <form id="add-product-form" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">Nome do produto *</label>
              <input type="text" id="prod-name" class="z-input" placeholder="Ex: Smartwatch Glass" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">Preço (R$)</label>
              <input type="text" id="prod-price" class="z-input" placeholder="199,90" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">Descrição</label>
              <textarea id="prod-desc" rows="3" class="z-input" placeholder="Detalhes do produto..."></textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
              <button type="submit" class="z-btn flex-1"><i class="fas fa-save"></i> Salvar produto</button>
              <button type="button" id="cancel-add" class="z-btn-secondary flex-1"><i class="fas fa-times"></i> Cancelar</button>
            </div>
          </form>
        </div>
      `;
      
      const form = document.getElementById('add-product-form');
      const cancelBtn = document.getElementById('cancel-add');
      
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('prod-name').value.trim();
        const price = document.getElementById('prod-price').value.trim();
        const description = document.getElementById('prod-desc').value.trim();
        if (!name || !price) {
          alert('Preencha nome e preço do produto.');
          return;
        }
        const newProduct = {
          id: String(nextId++),
          name: name,
          price: price,
          description: description || 'Sem descrição adicional.'
        };
        products.push(newProduct);
        saveToLocal();
        alert(`Produto "${name}" adicionado com sucesso!`);
        setActiveView('list');
      });
      
      cancelBtn.addEventListener('click', () => setActiveView('list'));
    }
    
    // ========== VIEW: EDITAR PRODUTO (PÁGINA ESTÁTICA COM DADOS PREENCHIDOS) ==========
    function renderEditProduct(productId) {
      const product = products.find(p => p.id === productId);
      if (!product) {
        alert('Produto não encontrado. Redirecionando para lista.');
        setActiveView('list');
        return;
      }
      
      viewContainer.innerHTML = `
        <div class="z-card max-w-2xl mx-auto w-full p-6 md:p-8">
          <div class="flex items-center gap-3 mb-6 border-b border-white/10 pb-4">
            <i class="fas fa-pen-fancy text-indigo-400 text-2xl"></i>
            <h2 class="text-2xl font-bold text-white">Editar Produto</h2>
          </div>
          <form id="edit-product-form" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">Nome do produto *</label>
              <input type="text" id="edit-name" class="z-input" value="${escapeHtml(product.name)}" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">Preço (R$)</label>
              <input type="text" id="edit-price" class="z-input" value="${escapeHtml(product.price)}" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1">Descrição</label>
              <textarea id="edit-desc" rows="3" class="z-input">${escapeHtml(product.description)}</textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 pt-2">
              <button type="submit" class="z-btn flex-1"><i class="fas fa-check-circle"></i> Atualizar</button>
              <button type="button" id="cancel-edit" class="z-btn-secondary flex-1"><i class="fas fa-arrow-left"></i> Voltar</button>
            </div>
          </form>
        </div>
      `;
      
      const form = document.getElementById('edit-product-form');
      const cancelBtn = document.getElementById('cancel-edit');
      
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const newName = document.getElementById('edit-name').value.trim();
        const newPrice = document.getElementById('edit-price').value.trim();
        const newDesc = document.getElementById('edit-desc').value.trim();
        if (!newName || !newPrice) {
          alert('Nome e preço são obrigatórios.');
          return;
        }
        product.name = newName;
        product.price = newPrice;
        product.description = newDesc || 'Sem descrição adicional.';
        saveToLocal();
        alert('Produto atualizado com sucesso!');
        setActiveView('list');
      });
      
      cancelBtn.addEventListener('click', () => setActiveView('list'));
    }
    
    // Deletar produto e atualizar lista
    function deleteProductById(id) {
      const index = products.findIndex(p => p.id === id);
      if (index !== -1) {
        products.splice(index, 1);
        saveToLocal();
        // Se a view atual é list, atualiza; senão, volta para list
        if (currentView === 'list') {
          renderProductList();
        } else {
          setActiveView('list');
        }
      } else {
        alert('Erro ao excluir.');
      }
    }
    
    // Escapar HTML para segurança
    function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
      }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
        return c;
      });
    }
    
    // CONTROLE DE ROTAS ATIVAS (NAVBAR FUNCIONAL)
    let currentView = 'list';     // 'list', 'add', 'edit'
    let currentEditId = null;
    
    function setActiveView(view, editId = null) {
      currentView = view;
      if (view === 'list') {
        renderProductList();
        updateNavActive('list');
      } else if (view === 'add') {
        renderAddProduct();
        updateNavActive('add');
      } else if (view === 'edit') {
        if (editId) {
          currentEditId = editId;
          renderEditProduct(editId);
        } else {
          // Se não passou ID, tenta pegar da URL ou redireciona para lista
          if (products.length === 0) {
            alert('Nenhum produto disponível para editar. Adicione um primeiro.');
            setActiveView('add');
            return;
          }
          // opção: pega o primeiro produto como fallback ou pergunta
          const firstId = products[0]?.id;
          if (firstId) {
            currentEditId = firstId;
            renderEditProduct(firstId);
          } else {
            setActiveView('list');
          }
        }
        updateNavActive('edit');
      }
    }
    
    function updateNavActive(active) {
      document.querySelectorAll('.z-link').forEach(link => {
        const navVal = link.getAttribute('data-nav');
        if (navVal === active) {
          link.classList.add('active');
          link.style.color = '#c7d2fe';
        } else {
          link.classList.remove('active');
          link.style.color = '';
        }
      });
    }
    
    // Eventos da navbar (roteamento sem refresh)
    function bindNavEvents() {
      const navListBtn = document.querySelector('[data-nav="list"]');
      const navAddBtn = document.querySelector('[data-nav="add"]');
      const navEditBtn = document.querySelector('[data-nav="edit"]');
      
      if (navListBtn) navListBtn.addEventListener('click', () => setActiveView('list'));
      if (navAddBtn) navAddBtn.addEventListener('click', () => setActiveView('add'));
      if (navEditBtn) {
        navEditBtn.addEventListener('click', () => {
          if (products.length === 0) {
            alert('Não há produtos cadastrados. Crie um produto antes de editar.');
            setActiveView('add');
            return;
          }
          // Se está na rota edit, mas sem ID, pede pro usuário escolher produto (pode ser mais prático)
          // Criamos um seletor rápido? Melhor: redirecionar para lista e clicar em editar em algum card.
          // Porém, seguindo lógica: ao clicar em "Editar" na navbar, se houver produtos, exibe lista com destaque?
          // Vamos implementar: Exibe a lista de produtos com um modal simples? Mas mais clean: redirecionar para list
          // e mostrar um tooltip? mas para melhor UX: redirecionar para listagem e o próprio usuário escolhe.
          alert('Clique em "Editar" no card do produto desejado na página de listagem.');
          setActiveView('list');
        });
      }
    }
    
    // Inicialização
    function init() {
      bindNavEvents();
      setActiveView('list');
    }
    
    init();
  </script>
</body>
</html>