<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// This endpoint obtains an access token by including your existing createaccesstoken.php
// which sets $access_token on success.
try {
    ob_start();
    include __DIR__ . '/createaccesstoken.php';
    ob_end_clean();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

if (isset($access_token) && $access_token) {
    echo json_encode(['access_token' => $access_token]);
} else {
    echo json_encode(['error' => 'Failed to obtain access token']);
}

?>
