<?php
// MTN MoMo callback receiver — logs only real data

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

$logFile = __DIR__ . DIRECTORY_SEPARATOR . 'momocollection.json';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Helper to get headers in all environments
function get_request_headers_safe() {
    if (function_exists('getallheaders')) return getallheaders();
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) === 'HTTP_') {
            $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_',' ',substr($name,5)))));
            $headers[$headerName] = $value;
        } elseif (in_array($name, ['CONTENT_TYPE','CONTENT_LENGTH'])) {
            $headers[$name] = $value;
        }
    }
    return $headers;
}

// Recursive search utility for payload fields
function search_key_recursive($data, $target) {
    if (!is_array($data)) return null;
    foreach ($data as $k => $v) {
        if (strcasecmp($k, $target) === 0) return $v;
        if (is_array($v)) {
            $r = search_key_recursive($v, $target);
            if ($r !== null) return $r;
        }
    }
    return null;
}

// GET diagnostics
if ($method === 'GET') {
    $last = null;
    if (file_exists($logFile)) {
        $lines = array_filter(array_map('trim', file($logFile)), fn($l) => $l !== '');
        $last = count($lines) ? json_decode(end($lines), true) : null;
    }
    echo json_encode([
        'status' => 'ok',
        'message' => 'Callback endpoint reachable',
        'logFile' => $logFile,
        'last_entry' => $last
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Read raw body and headers
$raw = file_get_contents('php://input');
$headers = get_request_headers_safe();
$decoded = json_decode($raw, true);

// Build callback object
$obj = [
    'timestamp' => date('c'),
    'method' => $method,
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
    'headers' => $headers,
    'raw' => $raw,
    'payload' => $decoded === null ? null : $decoded
];

// Extract MTN fields (some may be null if MTN didn’t send)
$payload = $decoded === null ? [] : $decoded;
$fields = [
    'transactionId' => search_key_recursive($payload, 'transactionId'),
    'referenceId' => search_key_recursive($payload, 'referenceId') ?? search_key_recursive($payload, 'reference'),
    'externalId' => search_key_recursive($payload, 'externalId') ?? search_key_recursive($payload, 'external_id'),
    'amount' => search_key_recursive($payload, 'amount'),
    'currency' => search_key_recursive($payload, 'currency'),
    'status' => search_key_recursive($payload, 'status'),
    'payer' => search_key_recursive($payload, 'payer') ?? search_key_recursive($payload, 'partyId') ?? search_key_recursive($payload, 'partyid'),
    'payerMessage' => search_key_recursive($payload, 'payerMessage') ?? search_key_recursive($payload, 'payer_message'),
    'payeeNote' => search_key_recursive($payload, 'payeeNote') ?? search_key_recursive($payload, 'payee_note')
];

// Only log fields that were actually sent
$received_fields = [];
foreach ($fields as $key => $value) {
    if ($value !== null) $received_fields[$key] = $value;
}

$obj['extracted_fields'] = $fields;
$obj['received_fields'] = $received_fields;

// Append to log file
file_put_contents($logFile, json_encode($obj, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

// Return JSON
echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
?>
