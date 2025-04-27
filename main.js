document.addEventListener('DOMContentLoaded', function() {
    // Ativar item de menu
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Dados de exemplo para o dashboard
    const dashboardData = {
        totalItems: 42,
        stockQuantity: 1250,
        stockValue: 8750.50,
        avgConsumption: 35,
        stockEntries: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
            data: [120, 190, 150, 210, 180, 200]
        },
        stockConsumption: {
            labels: ['Caneta', 'Caderno', 'Lápis', 'Borracha', 'Grampos', 'Agenda'],
            stockData: [45, 25, 78, 32, 15, 20],
            consumptionData: [60, 30, 65, 25, 10, 15]
        },
        criticalProducts: [
            { code: '001', name: 'Caneta Esferográfica Azul', stock: 5, min: 20, status: 'critical' },
            { code: '042', name: 'Caderno Universitário 100 Folhas', stock: 12, min: 15, status: 'warning' },
            { code: '078', name: 'Lápis HB nº2', stock: 25, min: 20, status: 'ok' },
            { code: '012', name: 'Borracha Branca', stock: 8, min: 15, status: 'critical' },
            { code: '035', name: 'Agenda 2025', stock: 18, min: 20, status: 'warning' }
        ],
        recentActivities: [
            { type: 'entry', title: 'Entrada de Estoque', description: 'Caderno Universitário 100 Folhas x30', time: 'Hoje, 14:35' },
            { type: 'exit', title: 'Saída de Estoque', description: 'Caneta Esferográfica Azul x15', time: 'Hoje, 11:20' },
            { type: 'alert', title: 'Alerta de Estoque', description: 'Estoque baixo: Caderno Universitário', time: 'Ontem, 16:45' },
            { type: 'entry', title: 'Entrada de Estoque', description: 'Lápis HB nº2 x50', time: 'Ontem, 10:10' },
            { type: 'exit', title: 'Saída de Estoque', description: 'Borracha Branca x10', time: '12/04, 15:30' }
        ]
    };

    // Atualizar cards de estatísticas
    updateStatsCards(dashboardData);

    // Atualizar tabela de produtos críticos
    updateCriticalProductsTable(dashboardData.criticalProducts);

    // Atualizar atividades recentes
    updateRecentActivities(dashboardData.recentActivities);

    // Inicializar gráficos
    initializeCharts(dashboardData);

    // Botão de atualizar
    const refreshBtn = document.querySelector('.refresh-btn');
    refreshBtn.addEventListener('click', function() {
        // Simular atualização de dados
        setTimeout(() => {
            alert('Dados atualizados com sucesso!');
            // Aqui você faria uma chamada AJAX para buscar dados atualizados
        }, 500);
    });

    // Função para atualizar os cards de estatísticas
    function updateStatsCards(data) {
        document.querySelector('.stats-cards .card:nth-child(1) .card-value').textContent = data.totalItems;
        document.querySelector('.stats-cards .card:nth-child(2) .card-value').textContent = data.stockQuantity;
        document.querySelector('.stats-cards .card:nth-child(3) .card-value').textContent = `R$ ${data.stockValue.toFixed(2).replace('.', ',')}`;
        document.querySelector('.stats-cards .card:nth-child(4) .card-value').textContent = data.avgConsumption;
    }

    // Função para atualizar a tabela de produtos críticos
    function updateCriticalProductsTable(products) {
        const tbody = document.querySelector('.stock-table tbody');
        tbody.innerHTML = '';

        products.forEach(product => {
            const row = document.createElement('tr');
            if (product.status === 'critical') row.classList.add('critical');
            if (product.status === 'warning') row.classList.add('warning');

            let statusBadge = '';
            if (product.status === 'critical') {
                statusBadge = '<span class="status-badge danger">Crítico</span>';
            } else if (product.status === 'warning') {
                statusBadge = '<span class="status-badge warning">Baixo</span>';
            } else {
                statusBadge = '<span class="status-badge success">Ok</span>';
            }

            row.innerHTML = `
                <td>${product.code}</td>
                <td>${product.name}</td>
                <td>${product.stock}</td>
                <td>${product.min}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="action-btn order-btn">Pedir</button>
                    <button class="action-btn view-btn">Ver</button>
                </td>
            `;

            tbody.appendChild(row);
        });

        // Adicionar eventos aos botões
        document.querySelectorAll('.order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productCode = this.closest('tr').querySelector('td:first-child').textContent;
                alert(`Pedido solicitado para o produto ${productCode}`);
            });
        });

        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                alert(`Visualizando detalhes de ${productName}`);
            });
        });
    }

    // Função para atualizar atividades recentes
    function updateRecentActivities(activities) {
        const container = document.querySelector('.activities-container');
        container.innerHTML = '';

        activities.slice(0, 5).forEach(activity => {
            const card = document.createElement('div');
            card.className = 'activity-card';

            let iconClass = '';
            let iconSvg = '';

            if (activity.type === 'entry') {
                iconClass = 'entry';
                iconSvg = '<path d="M12 5v14M5 12h14"></path>';
            } else if (activity.type === 'exit') {
                iconClass = 'exit';
                iconSvg = '<path d="M5 12h14"></path>';
            } else {
                iconClass = 'alert';
                iconSvg = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>';
            }

            card.innerHTML = `
                <div class="activity-icon ${iconClass}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        ${iconSvg}
                    </svg>
                </div>
                <div class="activity-details">
                    <div class="activity-title">${activity.title}</div>
                    <div class="activity-description">${activity.description}</div>
                    <div class="activity-time">${activity.time}</div>
                </div>
            `;

            container.appendChild(card);
        });
    }

    // Função para inicializar os gráficos
    function initializeCharts(data) {
        // Gráfico de entradas de estoque
        const entriesCtx = document.getElementById('entriesChart').getContext('2d');
        new Chart(entriesCtx, {
            type: 'line',
            data: {
                labels: data.stockEntries.labels,
                datasets: [{
                    label: 'Quantidade de Entradas',
                    data: data.stockEntries.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de consumo vs estoque
        const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
        new Chart(consumptionCtx, {
            type: 'bar',
            data: {
                labels: data.stockConsumption.labels,
                datasets: [
                    {
                        label: 'Estoque Atual',
                        data: data.stockConsumption.stockData,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Consumo Mensal',
                        data: data.stockConsumption.consumptionData,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});