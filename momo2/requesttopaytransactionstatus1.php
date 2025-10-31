<?php
// ...existing code...
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read ref from query
$reference_id = $_GET['ref'] ?? null;
if (!$reference_id) {
    echo json_encode(['error' => 'Missing ref parameter. Use ?ref=<reference_id> (use X-Reference-Id from momocollection.json).']);
    exit;
}

// Load config
if (file_exists(__DIR__ . '/config.php')) include __DIR__ . '/config.php';

// Obtain fresh access token by including createaccesstoken.php (silence its output)
$access_token = null;
if (file_exists(__DIR__ . '/createaccesstoken.php')) {
    ob_start();
    include __DIR__ . '/createaccesstoken.php';
    ob_end_clean();
}

// Diagnostics if token missing
if (empty($access_token)) {
    echo json_encode([
        'error' => 'No access token obtained',
        'hint' => 'Check createaccesstoken.php and config.php (secodary_key, api user/apikey).',
        'secodary_key_present' => isset($secodary_key) && !empty($secodary_key)
    ], JSON_PRETTY_PRINT);
    exit;
}

// Call MTN status endpoint
$ch = curl_init();
$url = "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/" . urlencode($reference_id);
$headers = [
    "Authorization: Bearer $access_token",
    "X-Target-Environment: sandbox",
    "Ocp-Apim-Subscription-Key: " . ($secodary_key ?? '')
];
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_errno($ch) ? curl_error($ch) : null;
curl_close($ch);

// Try decode JSON response
$parsed = json_decode($response, true);

// If 401, include some token diagnostics
$token_diag = null;
if ($httpcode === 401) {
    $token_diag = [
        'token_length' => strlen($access_token),
        'token_preview' => substr($access_token, 0, 20) . '...',
    ];
}

// Return structured debug JSON
echo json_encode([
    'http_code' => $httpcode,
    'curl_error' => $curlErr,
    'request_url' => $url,
    'request_headers' => $headers,
    'response_raw' => $response,
    'response_parsed' => $parsed === null ? null : $parsed,
    'token_diag' => $token_diag
], JSON_PRETTY_PRINT);
?>
// ...existing code...