<?php

// Configurações do banco de dados
$usuario = 'root';
$senha = '';
$database = 'login';
$host = 'localhost:3307';

// Configurações de segurança
$mysqli = new mysqli($host, $usuario, $senha, $database);

if ($mysqli->connect_error) {
    die("Falha ao conectar ao banco de dados: " . $mysqli->connect_error);
}

// Configurações adicionais
$mysqli->set_charset("utf8mb4");
$mysqli->query("SET NAMES 'utf8mb4'");
$mysqli->query("SET CHARACTER SET utf8mb4");
$mysqli->query("SET character_set_connection=utf8mb4");

// Função para registrar logs
function registrarLog($usuario_id, $acao, $descricao = '') {
    global $mysqli;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $mysqli->prepare("INSERT INTO logs (usuario_id, acao, descricao, ip) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $acao, $descricao, $ip);
    $stmt->execute();
    $stmt->close();
}

// Função para atualizar último login
function atualizarUltimoLogin($usuario_id) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("UPDATE usuarios SET ultimo_login = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->close();
}

// Função para gerar relatório em Excel
function gerarRelatorioExcel($tipo, $periodo_inicio, $periodo_fim, $filtros = []) {
    global $mysqli;
    
    $usuario_id = $_SESSION['usuario_id'];
    $arquivo_path = "relatorios/" . date('Y-m-d_H-i-s') . "_" . $tipo . ".xlsx";
    
    // Inserir registro do relatório
    $stmt = $mysqli->prepare("INSERT INTO relatorios (usuario_id, tipo, periodo_inicio, periodo_fim, filtros, arquivo_path) VALUES (?, ?, ?, ?, ?, ?)");
    $filtros_json = json_encode($filtros);
    $stmt->bind_param("isssss", $usuario_id, $tipo, $periodo_inicio, $periodo_fim, $filtros_json, $arquivo_path);
    $stmt->execute();
    $relatorio_id = $stmt->insert_id;
    $stmt->close();
    
    // Gerar o arquivo Excel usando a biblioteca PhpSpreadsheet
    require 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar cabeçalhos baseado no tipo de relatório
    switch($tipo) {
        case 'movimentacoes':
            $sheet->setCellValue('A1', 'Data');
            $sheet->setCellValue('B1', 'Produto');
            $sheet->setCellValue('C1', 'Tipo');
            $sheet->setCellValue('D1', 'Quantidade');
            $sheet->setCellValue('E1', 'Valor Unitário');
            $sheet->setCellValue('F1', 'Total');
            $sheet->setCellValue('G1', 'Usuário');
            
            $query = "SELECT m.data_movimentacao, p.nome as produto, m.tipo, m.quantidade, 
                     m.valor_unitario, (m.quantidade * m.valor_unitario) as total, u.nome as usuario
                     FROM movimentacoes m
                     JOIN produtos p ON m.produto_id = p.id
                     JOIN usuarios u ON m.usuario_id = u.id
                     WHERE m.data_movimentacao BETWEEN ? AND ?
                     ORDER BY m.data_movimentacao DESC";
            break;
            
        case 'estoque':
            $sheet->setCellValue('A1', 'Código de Barras');
            $sheet->setCellValue('B1', 'Código Interno');
            $sheet->setCellValue('C1', 'Produto');
            $sheet->setCellValue('D1', 'Categoria');
            $sheet->setCellValue('E1', 'Quantidade');
            $sheet->setCellValue('F1', 'Preço Custo');
            $sheet->setCellValue('G1', 'Preço Venda');
            
            $query = "SELECT p.codigo_barras, p.codigo_interno, p.nome, c.nome as categoria,
                     p.quantidade, p.preco_custo, p.preco_venda
                     FROM produtos p
                     JOIN categorias c ON p.categoria_id = c.id
                     WHERE p.status = 'ativo'
                     ORDER BY p.nome";
            break;
    }
    
    // Executar a query e preencher o Excel
    $stmt = $mysqli->prepare($query);
    if ($tipo == 'movimentacoes') {
        $stmt->bind_param("ss", $periodo_inicio, $periodo_fim);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $col = 1;
        foreach ($data as $value) {
            $sheet->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }
        $row++;
    }
    
    // Salvar o arquivo
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($arquivo_path);
    
    return $arquivo_path;
}

// Função para buscar produto por código de barras
function buscarProdutoPorCodigoBarras($codigo_barras) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("SELECT p.*, c.nome as categoria_nome, f.nome as fornecedor_nome 
                             FROM produtos p
                             LEFT JOIN categorias c ON p.categoria_id = c.id
                             LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
                             WHERE p.codigo_barras = ? AND p.status = 'ativo'");
    $stmt->bind_param("s", $codigo_barras);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Função para registrar movimentação
function registrarMovimentacao($produto_id, $usuario_id, $tipo, $quantidade, $valor_unitario, $motivo, $documento = null, $observacao = '') {
    global $mysqli;
    
    $stmt = $mysqli->prepare("INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, valor_unitario, motivo, documento, observacao) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisidsss", $produto_id, $usuario_id, $tipo, $quantidade, $valor_unitario, $motivo, $documento, $observacao);
    $stmt->execute();
    
    // Atualizar estoque
    if ($tipo == 'entrada') {
        $stmt = $mysqli->prepare("UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?");
    } else {
        $stmt = $mysqli->prepare("UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?");
    }
    $stmt->bind_param("ii", $quantidade, $produto_id);
    $stmt->execute();
    
    return $stmt->insert_id;
}