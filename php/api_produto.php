<?php
include('conexao.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remover produtos
    if (isset($_POST['remover']) || (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false)) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!empty($data['ids']) && is_array($data['ids'])) {
            $ids = array_map('intval', $data['ids']);
            $ids_str = implode(',', $ids);
            $ok = $mysqli->query("DELETE FROM produtos WHERE id IN ($ids_str)");
            echo json_encode(['success' => $ok]);
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'IDs inválidos']);
        exit;
    }

    $nome = $_POST['nome'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? '';
    $codigo_interno = $_POST['codigo_interno'] ?? '';
    $quantidade = $_POST['quantidade'] ?? 0;
    $estoque_minimo = $_POST['estoque_minimo'] ?? 0;
    $preco_custo = str_replace(['R$', ' ', '.'], '', $_POST['preco_custo'] ?? '');
    $preco_custo = str_replace(',', '.', $preco_custo);
    $preco_venda = str_replace(['R$', ' ', '.'], '', $_POST['preco_venda'] ?? '');
    $preco_venda = str_replace(',', '.', $preco_venda);
    $descricao = $_POST['descricao'] ?? '';
    $id = $_POST['id'] ?? null; // ID do produto para atualização

    if ($nome && $categoria_id && $codigo_interno) {
        if ($id) { // Atualizar produto existente
            $stmt = $mysqli->prepare("UPDATE produtos SET nome = ?, categoria_id = ?, codigo_interno = ?, quantidade = ?, estoque_minimo = ?, preco_custo = ?, preco_venda = ?, descricao = ? WHERE id = ?");
            $stmt->bind_param('sisiddssi', $nome, $categoria_id, $codigo_interno, $quantidade, $estoque_minimo, $preco_custo, $preco_venda, $descricao, $id);
        } else { // Inserir novo produto
            $stmt = $mysqli->prepare("INSERT INTO produtos (nome, categoria_id, codigo_interno, quantidade, estoque_minimo, preco_custo, preco_venda, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sisiddss', $nome, $categoria_id, $codigo_interno, $quantidade, $estoque_minimo, $preco_custo, $preco_venda, $descricao);
        }
        
        $ok = $stmt->execute();
        if ($ok) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    }
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Busque o produto no banco de dados
    $stmt = $mysqli->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produto = $result->fetch_assoc();
    echo json_encode($produto);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Método inválido']); 