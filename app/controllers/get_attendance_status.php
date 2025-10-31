<?php
require_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');

try {
    // Expect employee_ids as comma-separated list in GET param
    $idsParam = $_GET['employee_ids'] ?? '';
    $ids = array_filter(array_map('intval', explode(',', $idsParam)));

    if (empty($ids)) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    // Build placeholders for prepared statement
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT employee_id, status FROM attendance WHERE date = CURDATE() AND employee_id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($ids);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $map = [];
    foreach ($rows as $r) {
        $map[intval($r['employee_id'])] = $r['status'];
    }

    echo json_encode(['success' => true, 'data' => $map]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
