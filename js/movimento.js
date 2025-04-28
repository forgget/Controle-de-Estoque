console.log('movimento.js carregado');

// --- Remover movimentações ---
const btnRemoverMovimentacao = document.getElementById('btn-remover-movimentacao');
const checkboxesMov = document.querySelectorAll('.movimentacao-checkbox');
const btnCancelarRemocaoMov = document.getElementById('btn-cancelar-remocao-mov');
const btnApagarMovimentacoes = document.getElementById('btn-apagar-movimentacoes');
const apagarContainerMov = document.getElementById('apagar-container-mov');
let modoRemocaoMov = false;

console.log('Botão remover:', btnRemoverMovimentacao);
console.log('Checkboxes:', checkboxesMov);
console.log('Botão cancelar:', btnCancelarRemocaoMov);
console.log('Botão apagar:', btnApagarMovimentacoes);
console.log('Container apagar:', apagarContainerMov);

if (btnRemoverMovimentacao && checkboxesMov.length > 0 && btnCancelarRemocaoMov && btnApagarMovimentacoes && apagarContainerMov) {
    console.log('Todos os elementos encontrados, adicionando event listeners...');
    btnRemoverMovimentacao.addEventListener('click', function() {
        console.log('Botão remover clicado');
        modoRemocaoMov = true;
        checkboxesMov.forEach(cb => cb.style.display = '');
        btnRemoverMovimentacao.style.display = 'none';
        btnCancelarRemocaoMov.style.display = '';
        apagarContainerMov.style.display = '';
    });
    btnCancelarRemocaoMov.addEventListener('click', function() {
        console.log('Botão cancelar clicado');
        modoRemocaoMov = false;
        checkboxesMov.forEach(cb => cb.style.display = 'none');
        btnRemoverMovimentacao.style.display = '';
        btnCancelarRemocaoMov.style.display = 'none';
        apagarContainerMov.style.display = 'none';
        checkboxesMov.forEach(cb => cb.checked = false);
    });
    btnApagarMovimentacoes.addEventListener('click', function() {
        console.log('Botão apagar clicado');
        const selecionados = Array.from(checkboxesMov).filter(cb => cb.checked).map(cb => cb.value);
        console.log('Movimentações selecionadas:', selecionados);
        if (selecionados.length === 0) {
            alert('Selecione pelo menos uma movimentação para remover.');
            return;
        }
        if (!window.confirm('Tem certeza que deseja remover as movimentações selecionadas?')) return;
        fetch('../php/api/movimento_api.php?action=remover', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: selecionados })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Resposta da API:', data);
            window.location.reload();
        })
        .catch(error => {
            console.error('Erro ao remover movimentações:', error);
            window.location.reload();
        });
    });
} else {
    console.log('Alguns elementos não foram encontrados:');
    console.log('btnRemoverMovimentacao:', !!btnRemoverMovimentacao);
    console.log('checkboxesMov.length:', checkboxesMov.length);
    console.log('btnCancelarRemocaoMov:', !!btnCancelarRemocaoMov);
    console.log('btnApagarMovimentacoes:', !!btnApagarMovimentacoes);
    console.log('apagarContainerMov:', !!apagarContainerMov);
}

// Variáveis globais
let currentPage = 1;
let totalPages = 1;
let selectedProduct = null;

// Elementos do DOM
const modal = document.getElementById('movement-modal');
const modalTitle = document.getElementById('modal-title');
const movementForm = document.getElementById('movement-form');
const newEntryBtn = document.getElementById('new-entry');
const newExitBtn = document.getElementById('new-exit');
const closeModalBtn = document.querySelector('.close-modal');
const cancelBtn = document.querySelector('.cancel-btn');
const productSearchInput = document.getElementById('modal-product');
const productSearchResults = document.getElementById('product-search-results');

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Ativar item de menu
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Criar campo oculto para tipo de movimentação
    const modalType = document.createElement('input');
    modalType.type = 'hidden';
    modalType.id = 'modal-type';
    movementForm.appendChild(modalType);
    
    // Abrir modal para nova entrada
    newEntryBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Nova Entrada';
        modalType.value = 'entrada';
        resetForm();
        productSearchInput.removeAttribute('readonly');
        productSearchInput.classList.remove('readonly');
        modal.style.display = 'block';
        document.getElementById('modal-date').valueAsDate = new Date();
        productSearchInput.focus();
    });
    
    // Abrir modal para nova saída
    newExitBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Nova Saída';
        modalType.value = 'saida';
        resetForm();
        productSearchInput.removeAttribute('readonly');
        productSearchInput.classList.remove('readonly');
        modal.style.display = 'block';
        document.getElementById('modal-date').valueAsDate = new Date();
        productSearchInput.focus();
    });
    
    // Fechar modal
    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
    
    // Formatação de valores monetários
    const priceInput = document.getElementById('modal-price');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value === '') {
            e.target.value = '';
            return;
        }
        
        value = (parseInt(value) / 100).toFixed(2);
        e.target.value = `R$ ${value.replace('.', ',')}`;
    });
    
    // Busca de produtos
    productSearchInput.addEventListener('input', function(e) {
        const term = e.target.value;
        if (term.length < 2) {
            searchProducts('');
            return;
        }
        searchProducts(term);
    });
    
    // Mostrar todos os produtos ao focar no campo de busca
    productSearchInput.addEventListener('focus', function(e) {
        if (productSearchInput.value.length < 2) {
            searchProducts('');
        }
    });
    
    // Esconder resultados ao clicar fora
    productSearchInput.addEventListener('blur', function(e) {
        setTimeout(() => {
            productSearchResults.innerHTML = '';
            productSearchResults.classList.remove('active');
        }, 200);
    });
    
    // Controle de filtros
    const applyFilterBtn = document.getElementById('apply-filter');
    const clearFilterBtn = document.getElementById('clear-filter');
    
    applyFilterBtn.addEventListener('click', applyFilters);
    clearFilterBtn.addEventListener('click', function() {
        document.getElementById('date-start').value = '';
        document.getElementById('date-end').value = '';
        document.getElementById('movement-type').value = 'all';
        document.getElementById('product').value = '';
        applyFilters();
    });
    
    // Paginação
    const pageNumbers = document.querySelectorAll('.page-number');
    pageNumbers.forEach(button => {
        button.addEventListener('click', function() {
            pageNumbers.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentPage = parseInt(this.textContent);
            applyFilters();
        });
    });
    
    // Botões de navegação
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    prevBtn.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
            applyFilters();
        }
    });
    
    nextBtn.addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
            applyFilters();
        }
    });
    
    // Envio do formulário
    movementForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!selectedProduct) {
            alert('Selecione um produto!');
            return;
        }
        
        const quantity = document.getElementById('modal-quantity').value;
        const price = document.getElementById('modal-price').value;
        const date = document.getElementById('modal-date').value;
        const notes = document.getElementById('modal-notes').value;
        
        if (!quantity || !price || !date) {
            alert('Preencha todos os campos obrigatórios!');
            return;
        }
        
        saveMovement(modalType.value, selectedProduct.id, quantity, price, date, notes);
    });
    
    // Inicializar datas nos filtros
    const today = new Date();
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(today.getDate() - 7);
    
    document.getElementById('date-start').value = formatDate(oneWeekAgo);
    document.getElementById('date-end').value = formatDate(today);
    
    // Carregar movimentações
    loadMovements();

    // --- Seleção automática do produto pelo parâmetro produto_id na URL ---
    const urlParams = new URLSearchParams(window.location.search);
    const produtoIdUrl = urlParams.get('produto_id');
    const typeUrl = urlParams.get('type');
    if (typeUrl === 'entry' && produtoIdUrl) {
        // Abrir o modal de entrada
        setTimeout(() => {
            modalTitle.textContent = 'Nova Entrada';
            document.getElementById('modal-type').value = 'entrada';
            resetForm();
            modal.style.display = 'block';
            document.getElementById('modal-date').valueAsDate = new Date();
            // Buscar o produto pelo ID e preencher o campo
            fetch(`../php/api_produto.php?id=${produtoIdUrl}`)
                .then(res => res.json())
                .then(produto => {
                    if (produto && produto.nome) {
                        productSearchInput.value = produto.nome;
                        selectedProduct = { id: produto.id, nome: produto.nome };
                        // Opcional: bloquear edição do campo
                        productSearchInput.setAttribute('readonly', 'readonly');
                        productSearchInput.classList.add('readonly');
                    }
                });
        }, 300);
    }
});

// Funções auxiliares
function resetForm() {
    productSearchInput.value = '';
    productSearchResults.innerHTML = '';
    document.getElementById('modal-quantity').value = '';
    document.getElementById('modal-price').value = '';
    document.getElementById('modal-notes').value = '';
    selectedProduct = null;
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Funções de API
async function loadMovements(dateStart = '', dateEnd = '', type = 'all', product = '') {
    try {
        const response = await fetch(`../php/api/movimento_api.php?action=load&dateStart=${dateStart}&dateEnd=${dateEnd}&type=${type}&product=${product}&page=${currentPage}`);
        const data = await response.json();
        
        const movementsList = document.getElementById('movements-list');
        movementsList.innerHTML = '';
        
        if (data.movements && data.movements.length > 0) {
            data.movements.forEach(movement => {
                const row = document.createElement('tr');
                row.className = movement.tipo === 'entrada' ? 'entry-row' : 'exit-row';
                
                const [date, time] = movement.data_movimentacao.split(' ');
                const [year, month, day] = date.split('-');
                const formattedDateTime = time ? `${day}/${month}/${year} ${time}` : `${day}/${month}/${year}`;
                
                row.setAttribute('data-custo', Number(movement.custo_unitario));
                row.innerHTML = `
                    <td><input type="checkbox" class="movimentacao-checkbox" style="display:none;" name="movimentacoes[]" value="${movement.id}"></td>
                    <td>${formattedDateTime}</td>
                    <td>${movement.produto_codigo}</td>
                    <td>${movement.produto_nome}</td>
                    <td><span class="status-badge ${movement.tipo === 'entrada' ? 'success' : 'danger'}">${movement.tipo === 'entrada' ? 'Entrada' : 'Saída'}</span></td>
                    <td>${Number(movement.quantidade)} ${movement.unidade_medida}</td>
                    <td>${Number(movement.valor_unitario)}</td>
                    <td>${Number(movement.quantidade) * Number(movement.valor_unitario)}</td>
                    <td>${movement.usuario_nome}</td>
                    <td><button class='action-btn view-btn'>Ver</button></td>
                `;
                
                row.querySelector('.view-btn').addEventListener('click', function() {
                    alert(`Detalhes:\nProduto: ${movement.produto_nome}\nQuantidade: ${movement.quantidade} ${movement.unidade_medida}\nValor Unitário: ${movement.valor_unitario}\nTotal: ${movement.quantidade * movement.valor_unitario}\nData: ${formattedDateTime}\nResponsável: ${movement.usuario_nome}\nObservações: ${movement.observacao || 'Nenhuma'}`);
                });
                
                movementsList.appendChild(row);
            });
        } else {
            movementsList.innerHTML = `
                <tr>
                    <td colspan="10" style="text-align: center;">Nenhuma movimentação encontrada</td>
                </tr>
            `;
        }
        
        // Atualizar paginação
        updatePagination();
        
        // Re-inicializar o botão de remover após carregar as movimentações
        initRemoverMovimentacoes();
    } catch (error) {
        console.error('Erro ao carregar movimentações:', error);
    }
}

// Função para inicializar o botão de remover movimentações
function initRemoverMovimentacoes() {
    const btnRemoverMovimentacao = document.getElementById('btn-remover-movimentacao');
    const checkboxesMov = document.querySelectorAll('.movimentacao-checkbox');
    const btnCancelarRemocaoMov = document.getElementById('btn-cancelar-remocao-mov');
    const btnApagarMovimentacoes = document.getElementById('btn-apagar-movimentacoes');
    const apagarContainerMov = document.getElementById('apagar-container-mov');
    const selectAllCheckbox = document.getElementById('select-all-mov');
    let modoRemocaoMov = false;

    console.log('Botão remover:', btnRemoverMovimentacao);
    console.log('Checkboxes:', checkboxesMov);
    console.log('Botão cancelar:', btnCancelarRemocaoMov);
    console.log('Botão apagar:', btnApagarMovimentacoes);
    console.log('Container apagar:', apagarContainerMov);
    console.log('Checkbox selecionar todos:', selectAllCheckbox);

    if (btnRemoverMovimentacao && btnCancelarRemocaoMov && btnApagarMovimentacoes && apagarContainerMov && selectAllCheckbox) {
        console.log('Todos os elementos encontrados, adicionando event listeners...');
        btnRemoverMovimentacao.addEventListener('click', function() {
            console.log('Botão remover clicado');
            modoRemocaoMov = true;
            checkboxesMov.forEach(cb => cb.style.display = '');
            selectAllCheckbox.style.display = '';
            btnRemoverMovimentacao.style.display = 'none';
            btnCancelarRemocaoMov.style.display = '';
            apagarContainerMov.style.display = '';
        });
        btnCancelarRemocaoMov.addEventListener('click', function() {
            console.log('Botão cancelar clicado');
            modoRemocaoMov = false;
            checkboxesMov.forEach(cb => cb.style.display = 'none');
            selectAllCheckbox.style.display = 'none';
            btnRemoverMovimentacao.style.display = '';
            btnCancelarRemocaoMov.style.display = 'none';
            apagarContainerMov.style.display = 'none';
            checkboxesMov.forEach(cb => cb.checked = false);
            selectAllCheckbox.checked = false;
        });
        selectAllCheckbox.addEventListener('change', function() {
            console.log('Checkbox selecionar todos alterado:', this.checked);
            checkboxesMov.forEach(cb => cb.checked = this.checked);
        });
        btnApagarMovimentacoes.addEventListener('click', function() {
            console.log('Botão apagar clicado');
            const selecionados = Array.from(checkboxesMov).filter(cb => cb.checked).map(cb => cb.value);
            console.log('Movimentações selecionadas:', selecionados);
            if (selecionados.length === 0) {
                alert('Selecione pelo menos uma movimentação para remover.');
                return;
            }
            if (!window.confirm('Tem certeza que deseja remover as movimentações selecionadas?')) return;
            fetch('../php/api/movimento_api.php?action=remover', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids: selecionados })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Resposta da API:', data);
                window.location.reload();
            })
            .catch(error => {
                console.error('Erro ao remover movimentações:', error);
                window.location.reload();
            });
        });
    } else {
        console.log('Alguns elementos não foram encontrados:');
        console.log('btnRemoverMovimentacao:', !!btnRemoverMovimentacao);
        console.log('checkboxesMov.length:', checkboxesMov.length);
        console.log('btnCancelarRemocaoMov:', !!btnCancelarRemocaoMov);
        console.log('btnApagarMovimentacoes:', !!btnApagarMovimentacoes);
        console.log('apagarContainerMov:', !!apagarContainerMov);
        console.log('selectAllCheckbox:', !!selectAllCheckbox);
    }
}

async function saveMovement(type, productId, quantity, price, date, notes) {
    try {
        const response = await fetch('../php/api/movimento_api.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type,
                productId,
                quantity,
                unitPrice: parseFloat(price.replace('R$ ', '').replace(',', '.')),
                date,
                notes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            modal.style.display = 'none';
            alert('Movimentação registrada com sucesso!');
            loadMovements();
        } else {
            alert('Erro ao salvar movimentação. Tente novamente.');
        }
    } catch (error) {
        console.error('Erro ao salvar movimentação:', error);
        alert('Erro ao salvar movimentação. Tente novamente.');
    }
}

async function searchProducts(term) {
    try {
        const response = await fetch(`../php/api/movimento_api.php?action=search_products&term=${term}`);
        const products = await response.json();
        
        productSearchResults.innerHTML = '';
        if (products.length > 0) {
            productSearchResults.classList.add('active');
            products.forEach(product => {
                const div = document.createElement('div');
                div.className = 'product-item';
                div.innerHTML = `
                    <span class="product-code">${product.codigo_interno}</span>
                    <span class="product-name">${product.nome}</span>
                    <span class="product-stock">Estoque: ${product.quantidade} ${product.unidade_medida}</span>
                `;
                div.addEventListener('click', function() {
                    selectedProduct = product;
                    productSearchInput.value = `${product.codigo_interno} - ${product.nome}`;
                    productSearchResults.innerHTML = '';
                    productSearchResults.classList.remove('active');
                });
                productSearchResults.appendChild(div);
            });
        } else {
            productSearchResults.classList.remove('active');
        }
    } catch (error) {
        console.error('Erro ao buscar produtos:', error);
        productSearchResults.classList.remove('active');
    }
}

function updatePagination() {
    const pageNumbers = document.querySelector('.page-numbers');
    pageNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.className = `page-number ${i === currentPage ? 'active' : ''}`;
        button.textContent = i;
        
        button.addEventListener('click', function() {
            currentPage = i;
            updatePagination();
            applyFilters();
        });
        
        pageNumbers.appendChild(button);
    }
    
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

function applyFilters() {
    const dateStart = document.getElementById('date-start').value;
    const dateEnd = document.getElementById('date-end').value;
    const movementType = document.getElementById('movement-type').value;
    const productFilter = document.getElementById('product').value;
    
    loadMovements(dateStart, dateEnd, movementType, productFilter);
}

// Função para exportar para Excel
function exportToExcel() {
    const table = document.getElementById('movements-table');
    const rows = Array.from(table.querySelectorAll('tr'));

    // Criar array com os dados, ignorando a última coluna (Ações)
    const data = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('td, th'));
        // Remove a última coluna (Ações) se existir
        if (cells.length > 8) {
            cells.pop();
        }
        return cells.map(cell => cell.textContent.trim());
    });

    let totalCustoEntradas = 0;
    let totalLucroBruto = 0;
    let totalLucroLiquido = 0;

    function toNumber(str) {
        if (!str) return 0;
        return parseFloat(str.replace(/R\$|\s|\./g, '').replace(',', '.')) || 0;
    }
    function extractQuantidade(str) {
        if (!str) return 0;
        const match = str.match(/\d+[\.,]?\d*/);
        if (match) {
            return parseFloat(match[0].replace(',', '.')) || 0;
        }
        return 0;
    }

    // Calcular totais
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const dataRow = data[i];
        const tipo = dataRow[4] && dataRow[4].toLowerCase().includes('entrada') ? 'entrada' : 'saida';
        const quantidade = extractQuantidade(dataRow[5] || '0');
        const valorUnitario = toNumber(dataRow[6] || '0');
        const valorTotal = toNumber(dataRow[7] || '0');
        const custoUnitario = toNumber(row.getAttribute('data-custo') || '0');

        if (tipo === 'entrada') {
            totalCustoEntradas += valorTotal; // Soma o valor total das entradas
        } else if (tipo === 'saida') {
            totalLucroBruto += valorTotal; // Soma o valor total das saídas (receita bruta)
        }
    }

    // Calcular lucro líquido (receita bruta - custo total)
    totalLucroLiquido = totalLucroBruto - totalCustoEntradas;

    // Adicionar linhas de totais
    data.push(['', '', '', '', '', '', '', '', '']);
    data.push(['TOTAL CUSTO (ENTRADAS)', '', '', '', '', '', '', '', `R$ ${totalCustoEntradas.toFixed(2).replace('.', ',')}`]);
    data.push(['TOTAL RECEITA BRUTA (SAÍDAS)', '', '', '', '', '', '', '', `R$ ${totalLucroBruto.toFixed(2).replace('.', ',')}`]);
    data.push(['TOTAL LUCRO LÍQUIDO', '', '', '', '', '', '', '', `R$ ${totalLucroLiquido.toFixed(2).replace('.', ',')}`]);

    const dateStart = document.getElementById('date-start').value;
    const dateEnd = document.getElementById('date-end').value;
    let fileName = 'movimentacoes.xlsx';
    if (dateStart && dateEnd) {
        const start = new Date(dateStart);
        const end = new Date(dateEnd);
        const isFullMonth =
            start.getDate() === 1 &&
            end.getDate() === new Date(end.getFullYear(), end.getMonth() + 1, 0).getDate() &&
            start.getMonth() === end.getMonth() &&
            start.getFullYear() === end.getFullYear();
        if (isFullMonth) {
            const meses = [
                'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
            ];
            const nomeMes = meses[start.getMonth()];
            const ano = start.getFullYear();
            fileName = `movimentacoes(${nomeMes}-${ano}).xlsx`;
        } else {
            fileName = `movimentacoes(${dateStart}_a_${dateEnd}).xlsx`;
        }
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    const colWidths = data[0].map((_, i) => ({
        wch: Math.max(...data.map(row => (row[i] || '').length)) + 2
    }));
    ws['!cols'] = colWidths;
    XLSX.utils.book_append_sheet(wb, ws, 'Movimentações');
    XLSX.writeFile(wb, fileName);
} 