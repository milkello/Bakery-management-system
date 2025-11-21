<?php
require_once __DIR__ . '/../../config/config.php';

// Get parameters
$export_type = $_GET['type'] ?? 'sales';
$date_from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['to'] ?? date('Y-m-d');
$user_id = $_GET['user_id'] ?? null;

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $export_type . '_export_' . $date_from . '_to_' . $date_to . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Helper function to write CSV data
function writeCSV($output, $headers, $data) {
    // Write headers
    fputcsv($output, $headers);
    
    // Write data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
}

// Generate CSV based on type
switch ($export_type) {
    case 'sales':
        $stmt = $conn->prepare("
            SELECT s.id, p.name AS product_name, s.qty, s.unit_price, s.total_price, 
                   COALESCE(c.name, 'Walk-in') AS customer_name, s.customer_type, 
                   s.payment_method, u.username AS sold_by, s.created_at
            FROM sales s 
            JOIN products p ON s.product_id = p.id 
            LEFT JOIN users u ON s.created_by = u.id
            LEFT JOIN customers c ON s.customer_id = c.id
            WHERE DATE(s.created_at) BETWEEN ? AND ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$date_from, $date_to]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Product Name', 'Quantity', 'Unit Price', 'Total Price', 'Customer Name', 'Customer Type', 'Payment Method', 'Sold By', 'Date'];
        
        $csvData = [];
        foreach ($data as $row) {
            $csvData[] = [
                $row['id'],
                $row['product_name'],
                $row['qty'],
                $row['unit_price'],
                $row['total_price'],
                $row['customer_name'],
                $row['customer_type'],
                $row['payment_method'],
                $row['sold_by'] ?? 'N/A',
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;

    case 'production':
        $stmt = $conn->prepare("
            SELECT pr.id, p.name AS product_name, pr.quantity_produced, 
                   pr.raw_materials_used, u.username AS produced_by, pr.created_at
            FROM production pr
            JOIN products p ON pr.product_id = p.id
            LEFT JOIN users u ON pr.created_by = u.id
            WHERE DATE(pr.created_at) BETWEEN ? AND ?
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute([$date_from, $date_to]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Product Name', 'Quantity Produced', 'Raw Materials Used', 'Produced By', 'Date'];
        
        $csvData = [];
        foreach ($data as $row) {
            $csvData[] = [
                $row['id'],
                $row['product_name'],
                $row['quantity_produced'],
                $row['raw_materials_used'],
                $row['produced_by'] ?? 'N/A',
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;

    case 'products':
        $stmt = $conn->query("SELECT * FROM products ORDER BY name");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Name', 'SKU', 'Description', 'Price', 'Stock', 'Category', 'Created At'];
        
        $csvData = [];
        foreach ($data as $row) {
            $csvData[] = [
                $row['id'],
                $row['name'],
                $row['sku'],
                $row['description'] ?? '',
                $row['price'],
                $row['stock'],
                $row['category'] ?? '',
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;

    case 'customers':
        $stmt = $conn->query("
            SELECT c.*, u.username as added_by_name
            FROM customers c
            LEFT JOIN users u ON c.created_by = u.id
            ORDER BY c.created_at DESC
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Name', 'Email', 'Phone', 'Address', 'Customer Type', 'Added By', 'Created At'];
        
        $csvData = [];
        foreach ($data as $row) {
            $csvData[] = [
                $row['id'],
                $row['name'],
                $row['email'] ?? '',
                $row['phone'] ?? '',
                $row['address'] ?? '',
                $row['customer_type'],
                $row['added_by_name'] ?? 'N/A',
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;

    case 'raw_materials':
        $stmt = $conn->query("SELECT * FROM raw_materials ORDER BY name");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Name', 'Stock Quantity', 'Unit', 'Unit Cost', 'Reorder Level', 'Supplier', 'Created At'];
        
        $csvData = [];
        foreach ($data as $row) {
            $csvData[] = [
                $row['id'],
                $row['name'],
                $row['stock_quantity'],
                $row['unit'],
                $row['unit_cost'],
                $row['reorder_level'] ?? 0,
                $row['supplier'] ?? '',
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;

    case 'employees':
        try {
            $stmt = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Department', 'Salary', 'Created At'];
            
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['first_name'] ?? '',
                    $row['last_name'] ?? '',
                    $row['email'] ?? '',
                    $row['phone'] ?? '',
                    $row['role'] ?? '',
                    $row['department'] ?? '',
                    $row['salary'] ?? '',
                    $row['created_at']
                ];
            }
            
            writeCSV($output, $headers, $csvData);
        } catch (Exception $e) {
            // Fallback to users table if employees table doesn't exist
            $stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['ID', 'Username', 'Email', 'Role', 'Created At'];
            
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['username'],
                    $row['email'] ?? '',
                    $row['role'],
                    $row['created_at']
                ];
            }
            
            writeCSV($output, $headers, $csvData);
        }
        break;

    case 'schedules':
        $stmt = $conn->prepare("
            SELECT s.*, e.first_name, e.last_name 
            FROM schedules s 
            LEFT JOIN employees e ON s.assigned_to = e.id 
            WHERE DATE(s.start_time) BETWEEN ? AND ? 
            ORDER BY s.start_time ASC
        ");
        $stmt->execute([$date_from, $date_to]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Title', 'Description', 'Schedule Type', 'Start Time', 'End Time', 'Assigned To', 'Status', 'Created At'];
        
        $csvData = [];
        foreach ($data as $row) {
            $assigned_name = $row['first_name'] ? $row['first_name'] . ' ' . $row['last_name'] : 'Unassigned';
            $csvData[] = [
                $row['id'],
                $row['title'],
                $row['description'] ?? '',
                $row['schedule_type'],
                $row['start_time'],
                $row['end_time'],
                $assigned_name,
                $row['status'],
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;

    case 'employee_production':
        if ($user_id) {
            $stmt = $conn->prepare("
                SELECT pr.*, p.name AS product_name
                FROM production pr
                JOIN products p ON pr.product_id = p.id
                WHERE pr.created_by = ? AND DATE(pr.created_at) BETWEEN ? AND ?
                ORDER BY pr.created_at DESC
            ");
            $stmt->execute([$user_id, $date_from, $date_to]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['ID', 'Product Name', 'Quantity Produced', 'Raw Materials Used', 'Date'];
            
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['product_name'],
                    $row['quantity_produced'],
                    $row['raw_materials_used'],
                    $row['created_at']
                ];
            }
            
            writeCSV($output, $headers, $csvData);
        }
        break;

    case 'employee_sales':
        if ($user_id) {
            $stmt = $conn->prepare("
                SELECT s.*, p.name AS product_name, c.name as customer_name
                FROM sales s
                JOIN products p ON s.product_id = p.id
                LEFT JOIN customers c ON s.customer_id = c.id
                WHERE (s.sold_by = ? OR s.created_by = ?) AND DATE(s.created_at) BETWEEN ? AND ?
                ORDER BY s.created_at DESC
            ");
            $stmt->execute([$user_id, $user_id, $date_from, $date_to]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $headers = ['ID', 'Product Name', 'Quantity', 'Unit Price', 'Total Price', 'Customer Name', 'Customer Type', 'Payment Method', 'Date'];
            
            $csvData = [];
            foreach ($data as $row) {
                $customer_display = !empty($row['customer_name']) ? $row['customer_name'] : 'Walk-in';
                $csvData[] = [
                    $row['id'],
                    $row['product_name'],
                    $row['qty'],
                    $row['unit_price'],
                    $row['total_price'],
                    $customer_display,
                    $row['customer_type'],
                    $row['payment_method'],
                    $row['created_at']
                ];
            }
            
            writeCSV($output, $headers, $csvData);
        }
        break;

    default:
        // Default to sales export
        $stmt = $conn->prepare("
            SELECT s.id, p.name AS product_name, s.qty, s.unit_price, s.total_price, 
                   COALESCE(c.name, 'Walk-in') AS customer_name, s.customer_type, 
                   s.payment_method, u.username AS sold_by, s.created_at
            FROM sales s 
            JOIN products p ON s.product_id = p.id 
            LEFT JOIN users u ON s.created_by = u.id
            LEFT JOIN customers c ON s.customer_id = c.id
            WHERE DATE(s.created_at) BETWEEN ? AND ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$date_from, $date_to]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $headers = ['ID', 'Product Name', 'Quantity', 'Unit Price', 'Total Price', 'Customer Name', 'Customer Type', 'Payment Method', 'Sold By', 'Date'];
        
        $csvData = [];
        foreach ($data as $row) {
            $csvData[] = [
                $row['id'],
                $row['product_name'],
                $row['qty'],
                $row['unit_price'],
                $row['total_price'],
                $row['customer_name'],
                $row['customer_type'],
                $row['payment_method'],
                $row['sold_by'] ?? 'N/A',
                $row['created_at']
            ];
        }
        
        writeCSV($output, $headers, $csvData);
        break;
}

fclose($output);
exit;