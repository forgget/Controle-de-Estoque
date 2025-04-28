<?php
include('protect.php');
include('conexao.php');
// Buscar nome da loja do usuário logado
$nome_loja = 'Minha Loja';
if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $q = $mysqli->query("SELECT nome_loja FROM usuarios WHERE id = $id_usuario");
    if ($q && $row = $q->fetch_assoc()) {
        $nome_loja = $row['nome_loja'];
    }
}
// Filtro de período
$periodo = $_GET['periodo'] ?? 'month';
$hoje = date('Y-m-d');
$where = '';
if ($periodo === 'day') {
    $where = "WHERE DATE(created_at) = '$hoje'";
} elseif ($periodo === 'month') {
    $mes = date('Y-m');
    $where = "WHERE DATE_FORMAT(created_at, '%Y-%m') = '$mes'";
} elseif ($periodo === 'year') {
    $ano = date('Y');
    $where = "WHERE YEAR(created_at) = '$ano'";
}
$total_produtos_periodo = $mysqli->query("SELECT COUNT(*) as total FROM produtos $where")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($nome_loja); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/main.js" defer></script>
    <script src="../js/dashboard.js" defer></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-container">
                <div class="logo">
                    <svg class="logo-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                    </svg>
                    <span class="logo-text"><?php echo htmlspecialchars($nome_loja); ?></span>
                </div>
            </div>
            <div class="divider"></div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item">
                        <a href="incio.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Tela Inicial</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="dashboard.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="movimento.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            <span>Movimento</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="Produtos.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                            </svg>
                            <span>Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="configuracoes.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 5 15.4a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09A1.65 1.65 0 0 0 16 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 8c.14.31.22.65.22 1v.09A1.65 1.65 0 0 0 21 12c0 .35-.08.69-.22 1z"></path>
                            </svg>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h1>Dashboard</h1>

            <!-- Seletor de Período -->
            <div class="period-selector">
                <form method="get" id="form-periodo">
                    <select id="period-select" class="filter-input" name="periodo" onchange="document.getElementById('form-periodo').submit()">
                        <option value="day" <?php if($periodo=='day') echo 'selected'; ?>>Por Dia</option>
                        <option value="week" <?php if($periodo=='week') echo 'selected'; ?>>Por Semana</option>
                        <option value="month" <?php if($periodo=='month') echo 'selected'; ?>>Por Mês</option>
                        <option value="year" <?php if($periodo=='year') echo 'selected'; ?>>Por Ano</option>
                    </select>
                </form>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="stats-cards">
                <div class="card yellow-border">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title yellow-text">Total de Produtos</h3>
                            <p class="card-value" id="total-products"><?php echo $total_produtos_periodo; ?></p>
                        </div>
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7h-3a2 2 0 0 1-2-2V2"></path>
                                <path d="M9 2v3a2 2 0 0 1-2 2H4"></path>
                                <path d="M3 7v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"></path>
                                <path d="M9 14h6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card blue-border">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title blue-text">Entradas</h3>
                            <p class="card-value" id="period-entries">0</p>
                        </div>
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card green-border">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title green-text">Saídas</h3>
                            <p class="card-value" id="period-exits">0</p>
                        </div>
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Financeiros -->
            <div class="stats-cards">
                <div class="card finance-card">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title">Resumo Financeiro</h3>
                            <div class="finance-values">
                                <div class="finance-item">
                                    <span class="finance-label">Valor Bruto:</span>
                                    <span class="finance-value" id="period-gross">R$ 0,00</span>
                                </div>
                                <div class="finance-item">
                                    <span class="finance-label">Custos:</span>
                                    <span class="finance-value" id="period-costs">R$ 0,00</span>
                                </div>
                                <div class="finance-item">
                                    <span class="finance-label">Lucro:</span>
                                    <span class="finance-value" id="period-profit">R$ 0,00</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                                <path d="M12 18V6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <canvas id="movementsChart"></canvas>
                </div>
                <div class="dashboard-card">
                    <canvas id="mostUsedProductsChart"></canvas>
                </div>
            </div>

            <div class="dashboard-table">
                <table id="dashboardTable">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Estoque</th>
                            <th>Entradas</th>
                            <th>Saídas</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardTableBody">
                        <!-- Dados serão inseridos aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Consulta para estatísticas gerais -->
    <script>
    var statsQuery = "SELECT 
        COUNT(DISTINCT p.id) as totalProducts,
        COUNT(CASE WHEN m.type = 'entry' THEN 1 END) as entries,
        COUNT(CASE WHEN m.type = 'exit' THEN 1 END) as exits,
        COUNT(CASE WHEN m.type = 'sale' THEN 1 END) as sales,
        COALESCE(SUM(CASE WHEN m.type = 'sale' THEN m.quantity * m.price ELSE 0 END), 0) as grossValue,
        COALESCE(SUM(CASE WHEN m.type = 'entry' THEN m.quantity * m.price ELSE 0 END), 0) as costsValue
    FROM products p
    LEFT JOIN movements m ON p.id = m.product_id
    WHERE m.date >= ? AND m.date <= ?";
    </script>
</body>
</html> 