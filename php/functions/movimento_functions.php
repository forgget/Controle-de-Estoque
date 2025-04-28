<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('db_connection.php');
$conn = getConnection();

// Função para carregar movimentações
function loadMovements($dateStart = '', $dateEnd = '', $type = 'all', $product = '', $page = 1, $perPage = 10) {
    global $conn;
    
    $where = [];
    $params = [];
    
    if ($dateStart) {
        $where[] = "m.data_movimentacao >= :dateStart";
        $params[':dateStart'] = $dateStart;
    }
    
    if ($dateEnd) {
        $where[] = "m.data_movimentacao <= :dateEnd";
        $params[':dateEnd'] = $dateEnd;
    }
    
    if ($type !== 'all') {
        $where[] = "m.tipo = :type";
        $params[':type'] = $type;
    }
    
    if ($product) {
        $where[] = "p.nome LIKE :product";
        $params[':product'] = "%$product%";
    }
    
    $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";
    
    // Contar total de registros
    $countSql = "SELECT COUNT(*) as total 
                 FROM movimentacoes m 
                 JOIN produtos p ON m.produto_id = p.id 
                 JOIN usuarios u ON m.usuario_id = u.id 
                 $whereClause";
    
    $stmt = $conn->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Calcular paginação
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    
    // Buscar movimentações
    $sql = "SELECT m.*, p.nome as produto_nome, p.codigo_interno as produto_codigo, 
                   u.nome as usuario_nome, p.unidade_medida
            FROM movimentacoes m 
            JOIN produtos p ON m.produto_id = p.id 
            JOIN usuarios u ON m.usuario_id = u.id 
            $whereClause 
            ORDER BY m.data_movimentacao DESC 
            LIMIT :offset, :perPage";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Para cada movimentação de saída, buscar o custo da última entrada
    foreach ($movements as &$mov) {
        if ($mov['tipo'] === 'saida') {
            // Buscar o valor_unitario da última entrada antes da data da saída
            $sqlCusto = "SELECT valor_unitario FROM movimentacoes WHERE produto_id = :produto_id AND tipo = 'entrada' AND data_movimentacao <= :data_saida ORDER BY data_movimentacao DESC, id DESC LIMIT 1";
            $stmtCusto = $conn->prepare($sqlCusto);
            $stmtCusto->execute([
                ':produto_id' => $mov['produto_id'],
                ':data_saida' => $mov['data_movimentacao']
            ]);
            $custo = $stmtCusto->fetch(PDO::FETCH_ASSOC);
            $mov['custo_unitario'] = $custo ? $custo['valor_unitario'] : 0;
        } else {
            // Para entradas, o custo é o próprio valor_unitario
            $mov['custo_unitario'] = $mov['valor_unitario'];
        }
    }
    
    return [
        'movements' => $movements,
        'total' => $total,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ];
}

// Função para salvar nova movimentação
function saveMovement($type, $productId, $quantity, $unitPrice, $date, $notes = '') {
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado. Faça login novamente.']);
        exit;
    }
    global $conn;
    
    try {
        $conn->beginTransaction();
        
        // Obter ID do usuário logado
        $userId = $_SESSION['usuario_id'];
        
        // Usar data e hora atual do servidor
        $dataHoraAtual = date('Y-m-d H:i:s');
        
        // Inserir movimentação
        $sql = "INSERT INTO movimentacoes (
                    produto_id, usuario_id, tipo, quantidade, valor_unitario, 
                    motivo, data_movimentacao, observacao
                ) VALUES (
                    :productId, :userId, :type, :quantity, :unitPrice,
                    :motivo, :date, :notes
                )";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':productId' => $productId,
            ':userId' => $userId,
            ':type' => $type,
            ':quantity' => $quantity,
            ':unitPrice' => $unitPrice,
            ':motivo' => 'ajuste', // Motivo padrão para ajustes manuais
            ':date' => $dataHoraAtual,
            ':notes' => $notes
        ]);
        
        // Atualizar estoque
        $sql = "UPDATE produtos 
                SET quantidade = quantidade " . ($type === 'entrada' ? '+' : '-') . " :quantity 
                WHERE id = :productId";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':quantity' => $quantity,
            ':productId' => $productId
        ]);
        
        $conn->commit();
        return true;
    } catch(PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Função para buscar produtos
function searchProducts($term) {
    global $conn;
    
    $sql = "SELECT id, codigo_interno, nome, quantidade, unidade_medida 
            FROM produtos 
            WHERE (nome LIKE :term OR codigo_interno LIKE :term)
            ORDER BY nome 
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':term' => "%$term%"]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?> 