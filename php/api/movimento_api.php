<?php
header('Content-Type: application/json');
require_once('../functions/movimento_functions.php');
require_once('../functions/db_connection.php');
$conn = getConnection();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'load':
        $dateStart = $_GET['dateStart'] ?? '';
        $dateEnd = $_GET['dateEnd'] ?? '';
        $type = $_GET['type'] ?? 'all';
        $product = $_GET['product'] ?? '';
        $page = intval($_GET['page'] ?? 1);
        
        $result = loadMovements($dateStart, $dateEnd, $type, $product, $page);
        echo json_encode($result);
        break;
        
    case 'save':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            exit;
        }
        ob_start();
        $success = saveMovement(
            $data['type'],
            $data['productId'],
            $data['quantity'],
            $data['unitPrice'],
            $data['date'],
            $data['notes'] ?? ''
        );
        $error = ob_get_clean();
        if ($success === true) {
            echo json_encode(['success' => true]);
        } else {
            echo $error;
        }
        break;
        
    case 'search_products':
        $term = $_GET['term'] ?? '';
        $products = searchProducts($term);
        echo json_encode($products);
        break;
        
    case 'remover':
        $ids = json_decode(file_get_contents('php://input'), true)['ids'];
        $ids = array_map('intval', $ids);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "DELETE FROM movimentacoes WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($ids);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Ação inválida']);
        break;
}
?> 