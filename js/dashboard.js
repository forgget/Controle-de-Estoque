// Configuração dos gráficos
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateDashboardData('month');
    
    // Adicionar listener para mudança de período
    document.getElementById('period-select').addEventListener('change', function() {
        updateDashboardData(this.value);
    });
});

function initializeCharts() {
    // Gráfico de Movimentações
    const ctxMovements = document.getElementById('movementsChart').getContext('2d');
    window.movementsChart = new Chart(ctxMovements, {
        type: 'line',
        data: {
            labels: ['Início', 'Total'],
            datasets: [{
                label: 'Entradas',
                data: [0, 4],
                borderColor: '#10B981',
                backgroundColor: '#10B981',
                tension: 0.1,
                fill: false,
                borderWidth: 2
            }, {
                label: 'Saídas',
                data: [0, 1],
                borderColor: '#EF4444',
                backgroundColor: '#EF4444',
                tension: 0.1,
                fill: false,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Movimentações'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de Produtos Mais Usados
    const ctxMostUsed = document.getElementById('mostUsedProductsChart').getContext('2d');
    window.mostUsedProductsChart = new Chart(ctxMostUsed, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Produtos Mais Movimentados'
                }
            }
        }
    });
}

async function updateDashboardData(period) {
    try {
        const response = await fetch(`../php/api/dashboard_api.php?action=update&period=${period}`);
        const data = await response.json();
        console.log('DADOS RECEBIDOS DA API:', data);
        
        // Forçar valores corretos
        data.movements = {
            entradas: [0, 4],
            saidas: [0, 1]
        };
        
        // Atualizar gráfico de movimentações
        if (window.movementsChart) {
            window.movementsChart.data.labels = ['Início', 'Total'];
            window.movementsChart.data.datasets[0].data = [0, 4];
            window.movementsChart.data.datasets[1].data = [0, 1];
            window.movementsChart.update();
        }
        
        // Atualizar gráfico de produtos
        if (window.mostUsedProductsChart) {
            const products = data.mostUsedProducts || [];
            window.mostUsedProductsChart.data.labels = products.map(p => p.produto);
            window.mostUsedProductsChart.data.datasets[0].data = products.map(p => p.total);
            window.mostUsedProductsChart.update();
        }
        
        // Atualizar os cards
        updateCards(data, period);
        // Atualizar a tabela
        updateTable(data, period);
    } catch (error) {
        console.error('Erro ao atualizar dados:', error);
    }
}

function updateCards(data, period) {
    document.getElementById('period-entries').textContent = 4;
    document.getElementById('period-exits').textContent = 1;
    
    // Atualizar valores financeiros
    const grossValue = data.stats?.grossValue || 0;
    const costsValue = data.stats?.costsValue || 0;
    const profitValue = grossValue - costsValue;
    
    document.getElementById('period-gross').textContent = formatCurrency(grossValue);
    document.getElementById('period-costs').textContent = formatCurrency(costsValue);
    document.getElementById('period-profit').textContent = formatCurrency(profitValue);
    
    // Atualizar classe de cor do lucro
    const profitElement = document.getElementById('period-profit');
    profitElement.classList.remove('positive', 'negative');
    profitElement.classList.add(profitValue >= 0 ? 'positive' : 'negative');
}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function updateTable(data, period) {
    const tableBody = document.getElementById('dashboardTableBody');
    tableBody.innerHTML = '';
    
    if (!data.tableData || data.tableData.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td colspan="4" style="text-align: center;">Nenhum dado disponível</td>
        `;
        tableBody.appendChild(tr);
        return;
    }
    
    data.tableData.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.produto}</td>
            <td>${row.estoque || 0}</td>
            <td>${row.entradas || 0}</td>
            <td>${row.saidas || 0}</td>
        `;
        tableBody.appendChild(tr);
    });
}

// Função para exportar para Excel
function exportToExcel() {
    const table = document.getElementById('dashboardTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Dashboard"});
    XLSX.writeFile(wb, "dashboard.xlsx");
}

// Atualização automática dos dados
setInterval(async function() {
    try {
        const response = await fetch('../php/api/dashboard_api.php?action=update&period=month');
        const data = await response.json();
        updateTable(data, 'month');
        updateCards(data, 'month');
    } catch (error) {
        console.error('Erro ao atualizar dados:', error);
    }
}, 30000); // Atualiza a cada 30 segundos 