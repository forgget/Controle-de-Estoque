<?php
include('protect.php');
include('conexao.php');

// Buscar categorias
$categorias_query = "SELECT id, nome FROM categorias ORDER BY nome";
$categorias_result = $mysqli->query($categorias_query);

// Buscar produtos
$where_conditions = [];
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where_conditions[] = "p.nome LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
    $types .= "s";
}

if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $where_conditions[] = "p.categoria_id = ?";
    $params[] = $_GET['categoria'];
    $types .= "i";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT p.*, c.nome as categoria_nome 
          FROM produtos p 
          LEFT JOIN categorias c ON p.categoria_id = c.id 
          $where_clause 
          ORDER BY p.nome";

$stmt = $mysqli->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

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
    <title>Produtos - <?php echo htmlspecialchars($nome_loja); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/cadastro.css">
    <script src="../js/main.js" defer></script>
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
                    <li class="nav-item">
                        <a href="movimento.php" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            <span>Movimento</span>
                        </a>
                    </li>
                    <li class="nav-item active">
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
            <div class="products-header">
                <h1>Produtos</h1>
                <div class="action-buttons" id="remover-actions">
                    <button class="green-button" id="btn-novo-produto">
                        <span style="font-size: 1.5rem; font-weight: bold; line-height: 1;">+</span>
                        Novo Produto
                    </button>
                    <button class="red-button" id="btn-remover-produto">
                        <span style="font-size: 1.5rem; font-weight: bold; line-height: 1;">x</span>
                        Remover
                    </button>
                    <button class="blue-button" id="btn-cancelar-remocao" style="display:none;">
                        Cancelar
                    </button>
                </div>
            </div>
            <!-- Filtros -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Pesquisar por nome..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                    <div class="filter-group">
                        <select name="categoria">
                            <option value="">Todas as categorias</option>
                            <?php while($categoria = $categorias_result->fetch_assoc()): ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="filter-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        Filtrar
                    </button>
                </form>
            </div>
            <!-- Lista de Produtos -->
            <div class="products-list">
                <form id="form-produtos" method="post">
                <div id="apagar-container" style="display:none; margin-bottom: 10px;">
                    <button type="button" class="red-button" id="btn-apagar-produtos" style="padding: 6px 16px; font-size: 13px;">APAGAR</button>
                </div>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all" style="display:none;"></th>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Estoque Mínimo</th>
                            <th>Preço Custo</th>
                            <th>Preço Venda</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($produto = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" class="produto-checkbox" style="display:none;" name="produtos[]" value="<?php echo $produto['id']; ?>"></td>
                                <td><?php echo htmlspecialchars($produto['codigo_interno']); ?></td>
                                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['categoria_nome']); ?></td>
                                <td><?php echo number_format($produto['quantidade'], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($produto['estoque_minimo'], 0, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($produto['preco_custo'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?></td>
                                <td style="text-align:center;">
                                    <button type="button" class="table-action-btn edit-btn" title="Editar" data-id="<?php echo $produto['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 5 15.4a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09A1.65 1.65 0 0 0 16 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 8c.14.31.22.65.22 1v.09A1.65 1.65 0 0 0 21 12c0 .35-.08.69-.22 1z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </form>
            </div>
            <!-- Modal de cadastro/edição de produto (estrutura básica, pode ser melhorada depois) -->
            <div id="modal-produto" class="modal" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modal-titulo">Novo Produto</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-modal-produto">
                            <!-- Campos do produto -->
                            <div class="form-group">
                                <label for="produto-nome">Nome</label>
                                <input type="text" id="produto-nome" name="nome" required>
                            </div>
                            <div class="form-group">
                                <label for="produto-categoria">Categoria</label>
                                <select id="produto-categoria" name="categoria_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    <?php 
                                    // Buscar categorias novamente para o modal
                                    $categorias_modal = $mysqli->query("SELECT id, nome FROM categorias ORDER BY nome");
                                    while($cat = $categorias_modal->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nome']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="produto-codigo">Código Interno</label>
                                <input type="text" id="produto-codigo" name="codigo_interno" required>
                            </div>
                            <div class="form-group">
                                <label for="produto-quantidade">Quantidade</label>
                                <input type="number" id="produto-quantidade" name="quantidade" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="produto-estoque-minimo">Estoque Mínimo</label>
                                <input type="number" id="produto-estoque-minimo" name="estoque_minimo" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="produto-preco-custo">Preço de Custo</label>
                                <input type="text" id="produto-preco-custo" name="preco_custo" required>
                            </div>
                            <div class="form-group">
                                <label for="produto-preco-venda">Preço de Venda</label>
                                <input type="text" id="produto-preco-venda" name="preco_venda" required>
                            </div>
                            <div class="form-group">
                                <label for="produto-descricao">Descrição</label>
                                <textarea id="produto-descricao" name="descricao" rows="2" style="resize: vertical; border-radius: 0.75rem; background: #f1f5f9; border: 1px solid #e5e7eb; font-size: 1rem; color: #22223b;"></textarea>
                            </div>
                            <div class="form-buttons">
                                <button type="button" class="button gray-bg cancel-btn">Cancelar</button>
                                <button type="submit" class="button blue-bg">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir modal de cadastro se houver ?novo=1 na URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('novo') === '1') {
            var modal = document.getElementById('modal-produto');
            if (modal) {
                modal.style.display = 'block';
            }
        }
        // Fechar modal ao clicar no botão de fechar
        var closeBtn = document.querySelector('#modal-produto .close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                document.getElementById('modal-produto').style.display = 'none';
            });
        }
    });
    </script>
</body>
</html> 