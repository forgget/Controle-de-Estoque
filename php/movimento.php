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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimentações - <?php echo htmlspecialchars($nome_loja); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/movimento.css">
    <link rel="stylesheet" href="../css/cadastro.css">
    <script src="../js/movimento.js" defer></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
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
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item active">
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
            <div class="welcome-section">
                <h1>Movimentações de Estoque</h1>
            </div>
            
            <!-- Filtros -->
            <div class="filters-section">
                <div class="section-header">
                    <h3>Filtros</h3>
                </div>
                <div class="filters-container">
                    <div class="filter-group">
                        <label for="date-start">Data Inicial</label>
                        <input type="date" id="date-start" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <label for="date-end">Data Final</label>
                        <input type="date" id="date-end" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <label for="movement-type">Tipo</label>
                        <select id="movement-type" class="filter-input">
                            <option value="all">Todos</option>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="product">Produto</label>
                        <input type="text" id="product" class="filter-input" placeholder="Filtrar por produto">
                    </div>
                    <div class="filter-buttons">
                        <button id="apply-filter" class="button blue-bg">Aplicar</button>
                        <button id="clear-filter" class="button gray-bg">Limpar</button>
                        <button class="export-btn" onclick="exportToExcel()" title="Exportar para Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Movimentações -->
            <div class="movements-section">
                <div class="section-header">
                    <h3>Movimentações</h3>
                    <div class="action-buttons" id="remover-actions-mov">
                        <button class="button green-bg" id="new-entry">Nova Entrada</button>
                        <button class="button red-bg" id="new-exit">Nova Saída</button>
                        <button class="red-button" id="btn-remover-movimentacao">
                            <span style="font-size: 1.5rem; font-weight: bold; line-height: 1;">x</span>
                            Remover
                        </button>
                        <button class="blue-button" id="btn-cancelar-remocao-mov" style="display:none;">
                            Cancelar
                        </button>
                    </div>
                </div>
                <div id="apagar-container-mov" style="display:none; margin-bottom: 10px;">
                    <button type="button" class="red-button" id="btn-apagar-movimentacoes" style="padding: 6px 16px; font-size: 13px;">APAGAR</button>
                </div>
                <div class="table-container">
                    <table id="movements-table" class="movements-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-mov" style="display:none;"></th>
                                <th>Data</th>
                                <th>Código</th>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Valor Unit.</th>
                                <th>Valor Total</th>
                                <th>Responsável</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="movements-list">
                            <!-- Os dados serão carregados dinamicamente do banco de dados -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="pagination">
                    <button class="pagination-btn prev-btn">Anterior</button>
                    <div class="page-numbers">
                        <button class="page-number active">1</button>
                    </div>
                    <button class="pagination-btn next-btn">Próximo</button>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Nova Entrada/Saída -->
    <div id="movement-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Nova Entrada</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="movement-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal-product">Produto</label>
                            <div class="search-container">
                                <input type="text" id="modal-product" class="form-input" placeholder="Buscar produto..." autocomplete="off">
                                <div id="product-search-results" class="search-results"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="modal-quantity">Quantidade</label>
                            <input type="number" id="modal-quantity" class="form-input" min="1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal-price">Valor Unitário</label>
                            <input type="text" id="modal-price" class="form-input" placeholder="R$ 0,00">
                        </div>
                        <div class="form-group">
                            <label for="modal-date">Data</label>
                            <input type="date" id="modal-date" class="form-input">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="modal-notes">Observações</label>
                            <textarea id="modal-notes" class="form-input" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="button gray-bg cancel-btn">Cancelar</button>
                        <button type="submit" class="button blue-bg">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Abrir modal de entrada ou saída conforme o parâmetro type na URL
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    if (type === 'entry' || type === 'exit') {
        var modal = document.getElementById('movement-modal');
        var title = document.getElementById('modal-title');
        if (modal && title) {
            modal.style.display = 'block';
            title.textContent = (type === 'entry') ? 'Nova Entrada' : 'Nova Saída';
        }
    }
    // Fechar modal ao clicar no botão de fechar
    var closeBtn = document.querySelector('#movement-modal .close-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            document.getElementById('movement-modal').style.display = 'none';
        });
    }
});
</script>
</html> 