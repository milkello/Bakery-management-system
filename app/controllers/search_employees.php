<?php
require_once __DIR__ . '/../../config/config.php'; // This should be correct

header('Content-Type: application/json');

try {
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = trim($_GET['search']);
        
        $sql = "SELECT * FROM employees 
                WHERE first_name LIKE :search 
                   OR last_name LIKE :search 
                   OR email LIKE :search 
                   OR position LIKE :search 
                   OR status LIKE :search 
                ORDER BY id DESC";
        
        $stmt = $conn->prepare($sql);
        $searchTerm = "%" . $search . "%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->query("SELECT * FROM employees ORDER BY id DESC");
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($employees);
    
} catch (Exception $e) {
    error_log("Search employees error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>