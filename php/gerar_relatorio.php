<?php
require_once 'conexao.php';

function gerarRelatorioCSV($tipo, $periodo_inicio, $periodo_fim) {
    global $mysqli;
    
    $usuario_id = $_SESSION['usuario_id'];
    $arquivo_path = "relatorios/" . date('Y-m-d_H-i-s') . "_" . $tipo . ".csv";
    
    // Abrir arquivo para escrita
    $file = fopen($arquivo_path, 'w');
    
    // Configurar cabeçalhos baseado no tipo de relatório
    switch($tipo) {
        case 'movimentacoes':
            fputcsv($file, ['Data', 'Produto', 'Tipo', 'Quantidade', 'Valor Unitário', 'Total', 'Usuário']);
            
            $query = "SELECT m.data_movimentacao, p.nome as produto, m.tipo, m.quantidade, 
                     m.valor_unitario, (m.quantidade * m.valor_unitario) as total, u.nome as usuario
                     FROM movimentacoes m
                     JOIN produtos p ON m.produto_id = p.id
                     JOIN usuarios u ON m.usuario_id = u.id
                     WHERE m.data_movimentacao BETWEEN ? AND ?
                     ORDER BY m.data_movimentacao DESC";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $periodo_inicio, $periodo_fim);
            break;
            
        case 'estoque':
            fputcsv($file, ['Código de Barras', 'Código Interno', 'Produto', 'Categoria', 'Quantidade', 'Preço Custo', 'Preço Venda']);
            
            $query = "SELECT p.codigo_barras, p.codigo_interno, p.nome, c.nome as categoria,
                     p.quantidade, p.preco_custo, p.preco_venda
                     FROM produtos p
                     JOIN categorias c ON p.categoria_id = c.id
                     WHERE p.status = 'ativo'
                     ORDER BY p.nome";
            
            $stmt = $mysqli->prepare($query);
            break;
    }
    
    // Executar a query e escrever os dados
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, $row);
    }
    
    // Fechar arquivo
    fclose($file);
    
    return $arquivo_path;
}

// Exemplo de uso
if (isset($_GET['tipo']) && isset($_GET['inicio']) && isset($_GET['fim'])) {
    $arquivo = gerarRelatorioCSV($_GET['tipo'], $_GET['inicio'], $_GET['fim']);
    echo "Relatório gerado com sucesso: " . $arquivo;
}
?> 