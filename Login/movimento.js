document.addEventListener('DOMContentLoaded', function() {
    // Ativar item de menu
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Controle do modal
    const modal = document.getElementById('movement-modal');
    const newEntryBtn = document.getElementById('new-entry');
    const newExitBtn = document.getElementById('new-exit');
    const closeModal = document.querySelector('.close-modal');
    const cancelBtn = document.querySelector('.cancel-btn');
    const modalTitle = document.getElementById('modal-title');
    const movementForm = document.getElementById('movement-form');
    
    // Criar campo oculto para tipo de movimentação
    const modalType = document.createElement('input');
    modalType.type = 'hidden';
    modalType.id = 'modal-type';
    movementForm.appendChild(modalType);
    
    // Abrir modal para nova entrada
    newEntryBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Nova Entrada';
        modalType.value = 'entry';
        resetForm();
        modal.style.display = 'block';
        document.getElementById('modal-date').valueAsDate = new Date();
        document.getElementById('modal-product').focus();
    });
    
    // Abrir modal para nova saída
    newExitBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Nova Saída';
        modalType.value = 'exit';
        resetForm();
        modal.style.display = 'block';
        document.getElementById('modal-date').valueAsDate = new Date();
        document.getElementById('modal-product').focus();
    });
    
    // Fechar modal
    closeModal.addEventListener('click', function() {
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
            applyFilters();
        });
    });
    
    // Botões de navegação
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    prevBtn.addEventListener('click', function() {
        const activePage = document.querySelector('.page-number.active');
        const prevPage = activePage.previousElementSibling;
        
        if (prevPage && prevPage.classList.contains('page-number')) {
            activePage.classList.remove('active');
            prevPage.classList.add('active');
            applyFilters();
        }
    });
    
    nextBtn.addEventListener('click', function() {
        const activePage = document.querySelector('.page-number.active');
        const nextPage = activePage.nextElementSibling;
        
        if (nextPage && nextPage.classList.contains('page-number')) {
            activePage.classList.remove('active');
            nextPage.classList.add('active');
            applyFilters();
        }
    });
    
    // Envio do formulário
    movementForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const product = document.getElementById('modal-product').value;
        const quantity = document.getElementById('modal-quantity').value;
        const price = document.getElementById('modal-price').value;
        const date = document.getElementById('modal-date').value;
        
        if (!product || !quantity || !price || !date) {
            alert('Preencha todos os campos obrigatórios!');
            return;
        }
        
        addNewMovement(modalType.value, product, quantity, price, date);
        alert('Movimentação registrada com sucesso!');
        modal.style.display = 'none';
    });
    
    // Inicializar datas nos filtros
    const today = new Date();
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(today.getDate() - 7);
    
    document.getElementById('date-start').value = formatDate(oneWeekAgo);
    document.getElementById('date-end').value = formatDate(today);
    
    // Aplicar filtros inicialmente
    applyFilters();
});

// Funções auxiliares
function resetForm() {
    document.getElementById('modal-product').value = '';
    document.getElementById('modal-quantity').value = '';
    document.getElementById('modal-price').value = '';
    document.getElementById('modal-notes').value = '';
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function applyFilters() {
    const dateStart = document.getElementById('date-start').value;
    const dateEnd = document.getElementById('date-end').value;
    const movementType = document.getElementById('movement-type').value;
    const productFilter = document.getElementById('product').value.toLowerCase();
    
    const rows = document.querySelectorAll('#movements-list tr');
    
    rows.forEach(row => {
        const rowDate = row.cells[0].textContent;
        const rowType = row.cells[3].textContent.trim();
        const rowProduct = row.cells[2].textContent.toLowerCase();
        
        const [day, month, year] = rowDate.split('/');
        const formattedRowDate = `${year}-${month}-${day}`;
        
        const dateMatch = (!dateStart || formattedRowDate >= dateStart) && 
                         (!dateEnd || formattedRowDate <= dateEnd);
        const typeMatch = movementType === 'all' || 
                         (movementType === 'entry' && rowType === 'Entrada') || 
                         (movementType === 'exit' && rowType === 'Saída');
        const productMatch = !productFilter || rowProduct.includes(productFilter);
        
        row.style.display = (dateMatch && typeMatch && productMatch) ? '' : 'none';
    });
}

function addNewMovement(type, product, quantity, price, date) {
    const movementsList = document.getElementById('movements-list');
    
    const [year, month, day] = date.split('-');
    const formattedDate = `${day}/${month}/${year}`;
    
    const codes = {
        'Caderno Universitário 100 Folhas': '042',
        'Caneta Esferográfica Azul': '001',
        'Lápis HB nº2': '078',
        'Borracha Branca': '012',
        'Caixa de Grampos 26/6': '023',
        'Agenda 2025': '035'
    };
    
    const code = codes[product] || '000';
    const responsible = 'Fabrisio';
    const totalValue = (quantity * parseFloat(price.replace('R$ ', '').replace(',', '.'))).toFixed(2);
    const formattedTotal = `R$ ${totalValue.replace('.', ',')}`;
    
    const newRow = document.createElement('tr');
    newRow.className = type === 'entry' ? 'entry-row' : 'exit-row';
    
    newRow.innerHTML = `
        <td>${formattedDate}</td>
        <td>${code}</td>
        <td>${product}</td>
        <td><span class="status-badge ${type === 'entry' ? 'success' : 'danger'}">${type === 'entry' ? 'Entrada' : 'Saída'}</span></td>
        <td>${quantity}</td>
        <td>${price}</td>
        <td>${formattedTotal}</td>
        <td>${responsible}</td>
        <td><button class="action-btn view-btn">Ver</button></td>
    `;
    
    movementsList.insertBefore(newRow, movementsList.firstChild);
    
    newRow.querySelector('.view-btn').addEventListener('click', function() {
        alert(`Detalhes:\nProduto: ${product}\nQuantidade: ${quantity}\nValor Unitário: ${price}\nTotal: ${formattedTotal}\nData: ${formattedDate}\nResponsável: ${responsible}`);
    });
}