<?php
// Garantir timezone correto
date_default_timezone_set('America/Sao_Paulo');

include('../protect.php');
include('../conexao.php');
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$period = $_GET['period'] ?? 'month';

if ($action === 'update') {
    // Definir intervalo de datas conforme o período
    if ($period === 'day') {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $labels = 6;
        $interval = '4 HOUR';
    } elseif ($period === 'week') {
        $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        $labels = 7;
        $interval = '1 DAY';
    } elseif ($period === 'year') {
        $start = date('Y-01-01 00:00:00');
        $end = date('Y-12-31 23:59:59');
        $labels = 12;
        $interval = '1 MONTH';
    } else { // month
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-t 23:59:59');
        $labels = 4;
        $interval = '1 WEEK';
    }

    // Sempre mostrar apenas o total do período em um slot
    $q = $mysqli->query("SELECT COUNT(*) as total FROM movimentacoes WHERE tipo = 'entrada' AND data_movimentacao BETWEEN '$start' AND '$end'");
    $total_entradas = (int)($q->fetch_assoc()['total'] ?? 0);
    $q = $mysqli->query("SELECT COUNT(*) as total FROM movimentacoes WHERE tipo = 'saida' AND data_movimentacao BETWEEN '$start' AND '$end'");
    $total_saidas = (int)($q->fetch_assoc()['total'] ?? 0);
    $q = $mysqli->query("SELECT COUNT(*) as total FROM movimentacoes WHERE tipo = 'venda' AND data_movimentacao BETWEEN '$start' AND '$end'");
    $total_vendas = (int)($q->fetch_assoc()['total'] ?? 0);
    $entradas = [$total_entradas];
    $saidas = [$total_saidas];
    $sales = [$total_vendas];

    // Cálculo financeiro
    $q = $mysqli->query("SELECT SUM(quantidade * valor_unitario) as total FROM movimentacoes WHERE tipo = 'entrada' AND data_movimentacao BETWEEN '$start' AND '$end'");
    $costsValue = (float)($q->fetch_assoc()['total'] ?? 0);
    $q = $mysqli->query("SELECT SUM(quantidade * valor_unitario) as total FROM movimentacoes WHERE tipo = 'saida' AND data_movimentacao BETWEEN '$start' AND '$end'");
    $grossValue = (float)($q->fetch_assoc()['total'] ?? 0);
    $profitValue = $grossValue - $costsValue;

    $stats = [
        'entries' => $total_entradas,
        'exits' => $total_saidas,
        'sales' => $total_vendas,
        'grossValue' => $grossValue,
        'costsValue' => $costsValue,
        'profitValue' => $profitValue
    ];

    // Tabela de produtos
    $tableData = [];
    $q = $mysqli->query("
        SELECT 
            p.nome AS produto,
            p.quantidade as estoque,
            (SELECT COUNT(*) FROM movimentacoes m1 WHERE m1.produto_id = p.id AND m1.tipo = 'entrada' AND m1.data_movimentacao BETWEEN '$start' AND '$end') as entradas,
            (SELECT COUNT(*) FROM movimentacoes m2 WHERE m2.produto_id = p.id AND m2.tipo = 'saida' AND m2.data_movimentacao BETWEEN '$start' AND '$end') as saidas
        FROM produtos p
        ORDER BY p.nome
    ");
    while ($row = $q->fetch_assoc()) {
        $tableData[] = [
            'produto' => $row['produto'],
            'estoque' => (int)$row['estoque'],
            'entradas' => (int)$row['entradas'],
            'saidas' => (int)$row['saidas']
        ];
    }

    // Produtos mais movimentados (total de movimentações)
    $mostUsedProducts = [];
    $q = $mysqli->query("
        SELECT 
            p.nome,
            COUNT(*) as total
        FROM movimentacoes m 
        JOIN produtos p ON m.produto_id = p.id 
        WHERE m.data_movimentacao BETWEEN '$start' AND '$end'
        GROUP BY p.nome 
        ORDER BY total DESC 
        LIMIT 5
    ");
    while ($row = $q->fetch_assoc()) {
        $mostUsedProducts[] = [
            'produto' => $row['nome'],
            'total' => (int)$row['total']
        ];
    }

    $data = [
        'movements' => [
            'entradas' => $entradas,
            'saidas' => $saidas
        ],
        'sales' => $sales,
        'stats' => $stats,
        'tableData' => $tableData,
        'mostUsedProducts' => $mostUsedProducts
    ];

    echo json_encode($data);
}
?> 