<?php
header('Content-Type: application/json');
include('../conexao.php');

// Buscar valores reais do resumo do dia
$hoje_inicio = date('Y-m-d 00:00:00');
$hoje_fim = date('Y-m-d 23:59:59');

// Valor total de vendas (saídas) e custo
$q = $mysqli->query("SELECT 
    SUM(m.quantidade * m.valor_unitario) as total_vendas,
    SUM(m.quantidade * p.preco_custo) as total_custo
FROM movimentacoes m 
JOIN produtos p ON m.produto_id = p.id 
WHERE m.tipo = 'saida' 
AND m.data_movimentacao BETWEEN '$hoje_inicio' AND '$hoje_fim'");
$row = $q->fetch_assoc();
$vendas_hoje = (float)($row['total_vendas'] ?? 0);
$custo_vendas = (float)($row['total_custo'] ?? 0);

// Valor total de entradas
$q = $mysqli->query("SELECT 
    SUM(m.quantidade * m.valor_unitario) as total_entradas
FROM movimentacoes m 
WHERE m.tipo = 'entrada' 
AND m.data_movimentacao BETWEEN '$hoje_inicio' AND '$hoje_fim'");
$entradas_hoje = (float)($q->fetch_assoc()['total_entradas'] ?? 0);

// Lucro do dia (vendas - custo das vendas)
$lucro_hoje = $vendas_hoje - $custo_vendas;

// Quantidade de produtos que saíram
$q = $mysqli->query("SELECT 
    COUNT(DISTINCT m.produto_id) as total_produtos,
    SUM(m.quantidade) as total_itens
FROM movimentacoes m 
WHERE m.tipo = 'saida' 
AND m.data_movimentacao BETWEEN '$hoje_inicio' AND '$hoje_fim'");
$row = $q->fetch_assoc();
$produtos_sairam_hoje = (int)($row['total_produtos'] ?? 0);
$itens_sairam_hoje = (int)($row['total_itens'] ?? 0);

// Quantidade de produtos que entraram
$q = $mysqli->query("SELECT 
    COUNT(DISTINCT m.produto_id) as total_produtos,
    SUM(m.quantidade) as total_itens
FROM movimentacoes m 
WHERE m.tipo = 'entrada' 
AND m.data_movimentacao BETWEEN '$hoje_inicio' AND '$hoje_fim'");
$row = $q->fetch_assoc();
$produtos_entraram_hoje = (int)($row['total_produtos'] ?? 0);
$itens_entraram_hoje = (int)($row['total_itens'] ?? 0);

$response = [
    'todaySummary' => [
        'sales' => $vendas_hoje,
        'entries' => $entradas_hoje,
        'profit' => $lucro_hoje
    ],
    'productCounts' => [
        'entries' => [
            'count' => $produtos_entraram_hoje,
            'items' => $itens_entraram_hoje
        ],
        'exits' => [
            'count' => $produtos_sairam_hoje,
            'items' => $itens_sairam_hoje
        ]
    ]
];

echo json_encode($response); 