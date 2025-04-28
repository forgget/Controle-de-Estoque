<?php
include('protect.php');

// Buscar total de produtos diretamente do banco
include('conexao.php');
$total_produtos = $mysqli->query("SELECT COUNT(*) as total FROM produtos")->fetch_assoc()['total'];

// Buscar total de produtos cadastrados hoje
$hoje = date('Y-m-d');
$total_produtos_hoje = $mysqli->query("SELECT COUNT(*) as total FROM produtos WHERE DATE(created_at) = '$hoje'")->fetch_assoc()['total'];

// Vendas hoje (saídas)
$sql = "SELECT 
    IFNULL(SUM(m.quantidade * m.valor_unitario), 0) as total_vendas
FROM movimentacoes m 
WHERE m.tipo = 'saida' 
AND DATE(m.data_movimentacao) = CURRENT_DATE()";
$result = $mysqli->query($sql);
$vendas_hoje = floatval($result->fetch_assoc()['total_vendas']);

// Entradas hoje
$sql = "SELECT IFNULL(SUM(m.quantidade * m.valor_unitario), 0) as total 
FROM movimentacoes m 
WHERE m.tipo = 'entrada' 
AND DATE(m.data_movimentacao) = CURRENT_DATE()";
$result = $mysqli->query($sql);
$entradas_hoje = floatval($result->fetch_assoc()['total']);

// Lucro hoje (vendas - entradas)
$lucro_hoje = $vendas_hoje - $entradas_hoje;

// Produtos diferentes que saíram
$sql = "SELECT COUNT(DISTINCT m.produto_id) as total 
FROM movimentacoes m 
WHERE m.tipo = 'saida' 
AND DATE(m.data_movimentacao) = CURRENT_DATE()";
$result = $mysqli->query($sql);
$produtos_sairam_hoje = intval($result->fetch_assoc()['total']);

// Total de itens que saíram
$sql = "SELECT IFNULL(SUM(m.quantidade), 0) as total 
FROM movimentacoes m 
WHERE m.tipo = 'saida' 
AND DATE(m.data_movimentacao) = CURRENT_DATE()";
$result = $mysqli->query($sql);
$itens_sairam_hoje = intval($result->fetch_assoc()['total']);

// Buscar nome da loja do usuário logado
$nome_loja = 'Minha Loja';
if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $q = $mysqli->query("SELECT nome_loja FROM usuarios WHERE id = $id_usuario");
    if ($q && $row = $q->fetch_assoc()) {
        $nome_loja = $row['nome_loja'];
    }
}

// Buscar produtos com estoque próximo ou abaixo do mínimo
$produtos_alerta = [];
$q = $mysqli->query("SELECT id, nome, quantidade, estoque_minimo FROM produtos WHERE estoque_minimo > 0");
while ($p = $q->fetch_assoc()) {
    if ($p['quantidade'] < $p['estoque_minimo']) {
        $p['alerta'] = 'baixo';
        $produtos_alerta[] = $p;
    } elseif ($p['quantidade'] <= ($p['estoque_minimo'] + max(1, round($p['estoque_minimo'] * 0.2)))) {
        $p['alerta'] = 'proximo';
        $produtos_alerta[] = $p;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela Inicial - <?php echo htmlspecialchars($nome_loja); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <script src="../js/main.js" defer></script>
    <script src="../js/incio.js" defer></script>
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
                    <li class="nav-item active">
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
            <?php
            // Exibir mensagem de boas-vindas personalizada
            $nome_usuario = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'usuário';
            ?>
            <h1>Bem-vindo, <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
            
            <!-- Resumo do Dia -->
            <div class="stats-cards">
                <div class="card finance-card">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title">Resumo do Dia</h3>
                            <div class="finance-values">
                                <div class="finance-item">
                                    <span class="finance-label">Vendas Hoje:</span>
                                    <span class="finance-value positive">R$ <?php echo number_format($vendas_hoje, 2, ',', '.'); ?></span>
                                </div>
                                <div class="finance-item">
                                    <span class="finance-label">Entradas Hoje:</span>
                                    <span class="finance-value">R$ <?php echo number_format($entradas_hoje, 2, ',', '.'); ?></span>
                                </div>
                                <div class="finance-item">
                                    <span class="finance-label">Lucro do Dia:</span>
                                    <span class="finance-value <?php echo $lucro_hoje >= 0 ? 'positive' : 'negative'; ?>">
                                        R$ <?php echo number_format($lucro_hoje, 2, ',', '.'); ?>
                                    </span>
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

                <!-- Card de Produtos que Entraram -->
                <div class="card">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title">Produtos Cadastrados</h3>
                            <div class="product-values">
                                <div class="product-item">
                                    <span class="product-label">Total:</span>
                                    <span class="product-value" id="total-products-inicio"><?php echo $total_produtos; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Card de Produtos que Saíram -->
                <div class="card">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title">Produtos que Saíram</h3>
                            <div class="product-values">
                                <div class="product-item">
                                    <span class="product-label">Quantidade:</span>
                                    <span class="product-value" id="today-exits-count"><?php echo $produtos_sairam_hoje; ?></span>
                                </div>
                                <div class="product-item">
                                    <span class="product-label">Itens:</span>
                                    <span class="product-value" id="today-exits-items"><?php echo $itens_sairam_hoje; ?></span>
                                </div>
                            </div>
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

            <!-- ALERTA DE ESTOQUE MÍNIMO -->
            <?php if (count($produtos_alerta) > 0): ?>
            <div class="stats-cards">
                <div class="card" style="background:#fffbe6; border-left: 6px solid #facc15; margin-bottom: 16px;">
                    <div class="card-content">
                        <div class="card-info">
                            <h3 class="card-title" style="color:#b45309;">Alerta de Estoque Mínimo</h3>
                            <ul style="list-style:none; padding:0; margin:0;">
                                <?php foreach($produtos_alerta as $p): ?>
                                    <li style="display:flex; align-items:center; margin-bottom:8px; background:<?= $p['alerta']==='baixo' ? '#fee2e2' : '#fef9c3' ?>; border-radius:8px; padding:8px 12px;">
                                        <span style="flex:1; color:<?= $p['alerta']==='baixo' ? '#b91c1c' : '#b45309' ?>; font-weight:600;">
                                            <?= htmlspecialchars($p['nome']) ?>
                                            <small style="font-weight:400; color:#666;">(Estoque: <?= $p['quantidade'] ?> / Mínimo: <?= $p['estoque_minimo'] ?>)</small>
                                        </span>
                                        <a href="movimento.php?type=entry&produto_id=<?= $p['id'] ?>" class="button blue-bg" style="margin-left:12px;">Adicionar Entrada</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#facc15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="36" height="36"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Atalhos Rápidos -->
            <div class="shortcuts-section">
                <h2>Atalhos Rápidos</h2>
                <div class="shortcuts-grid">
                    <div class="shortcut-card">
                        <div class="shortcut-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                                <path d="M12 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <a href="movimento.php?type=entry" class="shortcut-button">Nova Entrada</a>
                        <p class="shortcut-text">Registrar entrada de produtos</p>
                    </div>
                    <div class="shortcut-card">
                        <div class="shortcut-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                                <path d="M5 12l7-7 7 7"></path>
                            </svg>
                        </div>
                        <a href="movimento.php?type=exit" class="shortcut-button">Nova Saída</a>
                        <p class="shortcut-text">Registrar saída de produtos</p>
                    </div>
                    <div class="shortcut-card">
                        <div class="shortcut-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="4" width="16" height="16" rx="2"/>
                                <line x1="12" y1="8" x2="12" y2="16"/>
                                <line x1="8" y1="12" x2="16" y2="12"/>
                            </svg>
                        </div>
                        <a href="Produtos.php?novo=1" class="shortcut-button">Cadastrar novo produto</a>
                        <p class="shortcut-text">Adicionar um novo produto ao estoque</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>