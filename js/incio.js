document.addEventListener('DOMContentLoaded', function() {
    // Carregar dados iniciais
    loadInitialData();
    
    // Atualizar dados a cada 5 minutos
    setInterval(loadInitialData, 300000);
});

async function loadInitialData() {
    try {
        const response = await fetch('../php/api/incio_api.php');
        const data = await response.json();
        
        updateTodaySummary(data.todaySummary);
        updateProductCounts(data.productCounts);
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
    }
}

function updateTodaySummary(data) {
    document.getElementById('today-sales').textContent = formatCurrency(data.sales);
    document.getElementById('today-entries').textContent = formatCurrency(data.entries);
    document.getElementById('today-profit').textContent = formatCurrency(data.profit);
    
    const profitElement = document.getElementById('today-profit');
    profitElement.classList.remove('positive', 'negative');
    profitElement.classList.add(data.profit >= 0 ? 'positive' : 'negative');
}

function updateProductCounts(data) {
    document.getElementById('today-entries-count').textContent = data.entries.count;
    document.getElementById('today-entries-items').textContent = data.entries.items;
    document.getElementById('today-exits-count').textContent = data.exits.count;
    document.getElementById('today-exits-items').textContent = data.exits.items;
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
} 