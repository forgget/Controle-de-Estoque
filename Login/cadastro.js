document.addEventListener('DOMContentLoaded', function() {
    // Dados de produtos de exemplo
    const productData = [
        { id: '001', name: 'Caneta Esferográfica Azul', category: 'Papelaria', price: 1.50, stock: 5, minStock: 20, status: 'critical' },
        { id: '002', name: 'Caneta Esferográfica Preta', category: 'Papelaria', price: 1.50, stock: 25, minStock: 20, status: 'ok' },
        { id: '003', name: 'Caneta Esferográfica Vermelha', category: 'Papelaria', price: 1.50, stock: 18, minStock: 15, status: 'ok' },
        { id: '004', name: 'Lápis HB nº2', category: 'Papelaria', price: 0.75, stock: 25, minStock: 20, status: 'ok' },
        { id: '005', name: 'Borracha Branca', category: 'Papelaria', price: 1.20, stock: 8, minStock: 15, status: 'critical' },
        { id: '006', name: 'Caderno Universitário 100 Folhas', category: 'Escolar', price: 15.90, stock: 12, minStock: 15, status: 'warning' },
        { id: '007', name: 'Caderno Universitário 200 Folhas', category: 'Escolar', price: 25.90, stock: 20, minStock: 10, status: 'ok' },
        { id: '008', name: 'Caderno Brochura 48 Folhas', category: 'Escolar', price: 5.50, stock: 30, minStock: 20, status: 'ok' },
        { id: '009', name: 'Grampeador de Mesa', category: 'Escritório', price: 18.90, stock: 7, minStock: 10, status: 'warning' },
        { id: '010', name: 'Grampos 26/6 (Caixa com 5000)', category: 'Escritório', price: 5.80, stock: 15, minStock: 10, status: 'ok' },
        { id: '011', name: 'Pasta AZ Lombo Largo', category: 'Escritório', price: 14.50, stock: 3, minStock: 8, status: 'critical' },
        { id: '012', name: 'Agenda 2025', category: 'Papelaria', price: 28.90, stock: 18, minStock: 20, status: 'warning' },
        { id: '013', name: 'Marca Texto Amarelo', category: 'Papelaria', price: 2.50, stock: 35, minStock: 20, status: 'ok' },
        { id: '014', name: 'Marca Texto Verde', category: 'Papelaria', price: 2.50, stock: 24, minStock: 20, status: 'ok' },
        { id: '015', name: 'Papel Sulfite A4 (Pacote 500 Folhas)', category: 'Papelaria', price: 23.90, stock: 10, minStock: 15, status: 'warning' }
    ];
    
    // Configurações de paginação
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredProducts = [...productData];
    
    // Elementos DOM
    const productsTableBody = document.getElementById('productsTableBody');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const pageNumbers = document.getElementById('pageNumbers');
    const selectAllCheckbox = document.getElementById('selectAllProducts');
    const exportExcelButton = document.getElementById('exportExcelButton');
    const newProductButton = document.getElementById('newProductButton');
    
    // Inicialização
    renderProductsTable();
    setupEventListeners();
    updatePaginationControls();
    
    // Configurar listeners de eventos
    function setupEventListeners() {
        // Pesquisa e Filtros
        searchButton.addEventListener('click', applyFilters);
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
        
        categoryFilter.addEventListener('change', applyFilters);
        stockFilter.addEventListener('change', applyFilters);
        
        // Paginação
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderProductsTable();
                updatePaginationControls();
            }
        });
        
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderProductsTable();
                updatePaginationControls();
            }
        });
        
        // Selecionar todos os produtos
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Exportar para Excel
        exportExcelButton.addEventListener('click', exportToExcel);
        
        // Novo Produto
        newProductButton.addEventListener('click', () => {
            window.location.href = 'novo-produto.html';
        });
    }
    
    // Aplicar filtros na tabela
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value.toLowerCase();
        const stockValue = stockFilter.value;
        
        filteredProducts = productData.filter(product => {
            // Filtro de pesquisa
            const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                product.id.toLowerCase().includes(searchTerm);
            
            // Filtro de categoria
            const matchesCategory = categoryValue === '' || 
                                   product.category.toLowerCase() === categoryValue;
            
            // Filtro de estoque
            let matchesStock = true;
            if (stockValue === 'baixo') {
                matchesStock = product.status === 'critical' || product.status === 'warning';
            } else if (stockValue === 'ok') {
                matchesStock = product.status === 'ok';
            } else if (stockValue === 'alto') {
                matchesStock = product.stock > product.minStock * 1.5;
            }
            
            return matchesSearch && matchesCategory && matchesStock;
        });
        
        // Resetar para a primeira página quando os filtros mudam
        currentPage = 1;
        renderProductsTable();
        updatePaginationControls();
    }
    
    // Renderizar tabela de produtos com paginação
    function renderProductsTable() {
        productsTableBody.innerHTML = '';
        
        // Calcular índices para paginação
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, filteredProducts.length);
        
        // Produtos da página atual
        const currentProducts = filteredProducts.slice(startIndex, endIndex);
        
        // Gerar linhas da tabela
        currentProducts.forEach(product => {
            const row = document.createElement('tr');
            
            // Definir classe baseada no status do estoque
            if (product.status === 'critical') {
                row.classList.add('critical');
            } else if (product.status === 'warning') {
                row.classList.add('warning');
            }
            
            // Determinar o texto e a classe do status
            let statusBadge = '';
            if (product.status === 'critical') {
                statusBadge = '<span class="status-badge danger">Crítico</span>';
            } else if (product.status === 'warning') {
                statusBadge = '<span class="status-badge warning">Baixo</span>';
            } else {
                statusBadge = '<span class="status-badge success">Ok</span>';
            }
            
            // Conteúdo da linha
            row.innerHTML = `
                <td><input type="checkbox" class="product-checkbox" data-id="${product.id}"></td>
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>${product.category}</td>
                <td>R$ ${product.price.toFixed(2).replace('.', ',')}</td>
                <td>${product.stock}</td>
                <td>${product.minStock}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="table-action-btn view-btn" title="Visualizar" data-id="${product.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                    <button class="table-action-btn edit-btn" title="Editar" data-id="${product.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="table-action-btn delete-btn" title="Excluir" data-id="${product.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>
            `;
            
            productsTableBody.appendChild(row);
        });
        
        // Adicionar eventos aos botões de ação
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                viewProduct(productId);
            });
        });
        
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                editProduct(productId);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                deleteProduct(productId);
            });
        });
    }
    
    // Atualizar controles de paginação
    function updatePaginationControls() {
        const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
        
        // Atualizar botões anterior/próximo
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        
        // Atualizar números de página
        pageNumbers.innerHTML = '';
        
        // Mostrar até 5 páginas na paginação
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageNumber = document.createElement('span');
            pageNumber.textContent = i;
            if (i === currentPage) {
                pageNumber.classList.add('current-page');
            }
            
            pageNumber.addEventListener('click', () => {
                currentPage = i;
                renderProductsTable();
                updatePaginationControls();
            });
            
            pageNumbers.appendChild(pageNumber);
        }
    }
    
    // Visualizar produto
    function viewProduct(productId) {
        const product = productData.find(p => p.id === productId);
        if (product) {
            alert(`Visualizando produto:\n\nID: ${product.id}\nNome: ${product.name}\nCategoria: ${product.category}\nPreço: R$ ${product.price.toFixed(2)}\nEstoque: ${product.stock}\nEstoque Mínimo: ${product.minStock}`);
        }
    }
    
    // Editar produto
    function editProduct(productId) {
        const product = productData.find(p => p.id === productId);
        if (product) {
            alert(`Editando produto: ${product.name}`);
            // Redirecionar para a página de edição com o ID do produto
            // window.location.href = `editar-produto.html?id=${productId}`;
        }
    }
    
    // Excluir produto
    function deleteProduct(productId) {
        if (confirm('Tem certeza que deseja excluir este produto?')) {
            const index = productData.findIndex(p => p.id === productId);
            if (index !== -1) {
                productData.splice(index, 1);
                applyFilters(); // Reaplicar filtros para atualizar a tabela
                alert('Produto excluído com sucesso!');
            }
        }
    }
    
    // Exportar para Excel
    function exportToExcel() {
        if (filteredProducts.length === 0) {
            alert('Nenhum produto para exportar!');
            return;
        }
        
        // Preparar dados para exportação
        const exportData = filteredProducts.map(product => ({
            'Código': product.id,
            'Nome do Produto': product.name,
            'Categoria': product.category,
            'Preço': product.price,
            'Em Estoque': product.stock,
            'Estoque Mínimo': product.minStock,
            'Status': product.status === 'critical' ? 'Crítico' : 
                     product.status === 'warning' ? 'Baixo' : 'Ok'
        }));
        
        // Criar planilha
        const worksheet = XLSX.utils.json_to_sheet(exportData);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Produtos');
        
        // Gerar arquivo Excel
        const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
        const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        
        // Baixar arquivo
        const fileName = `produtos_${new Date().toISOString().slice(0, 10)}.xlsx`;
        saveAs(blob, fileName);
    }
});