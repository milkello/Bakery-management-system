<?php
// Simple endpoint to initiate a collection (request to pay).
// Expects JSON POST: { phone, amount, currency, externalId }

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body']);
    exit;
}

$phone = trim($data['phone'] ?? '');
$amount = trim($data['amount'] ?? '');
$currency = $data['currency'] ?? 'XAF';
$externalId = $data['externalId'] ?? '';

if ($phone === '' || $amount === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing phone or amount']);
    exit;
}

// Normalize phone: remove spaces
$phone = preg_replace('/\s+/', '', $phone);

// Try to call your existing MTN flow if config exists.
// This project has: createapiuser.php, createapikey.php, createaccesstoken.php, requestpay.php
// We'll attempt to include and run the request flow, but to keep this endpoint safe for quick testing
// we'll use the existing files in a controlled manner.

$log = [];
try {
    // Load config (secondary key)
    if (file_exists(__DIR__ . '/config.php')) {
        include __DIR__ . '/config.php';
    }

    // Prepare reference id used by other scripts
    if (!isset($reference_id)) {
        // generate a uuid compatible with function.php
        if (file_exists(__DIR__ . '/function.php')) {
            include __DIR__ . '/function.php';
            $reference_id = generate_uuid();
        } else {
            $reference_id = bin2hex(random_bytes(12));
        }
    }

    // Create a temporary copy of request parameters to be used by requestpay.php
    // requestpay.php expects $phone, $amount, $currency variables when included.
    $phone_for_request = $phone;
    $amount_for_request = $amount;
    $currency_for_request = $currency;

    // To avoid modifying the original requestpay.php, we'll create a minimal wrapper
    // that uses createaccesstoken.php and then performs a requesttopay using cURL below.

    // Check if minimal config is present (secondary key) and required createaccesstoken files exist
    $can_call_mtn = file_exists(__DIR__ . '/createaccesstoken.php') && isset($secodary_key);

    if ($can_call_mtn) {
        // Create API user & apikey & access token sequence by calling the existing scripts
        // Note: those scripts echo output and are not designed for programmatic reuse; instead
        // we'll directly implement the requesttopay call here using $secodary_key and a fresh
        // Basic Authorization value derived from api user and apikey flow.

        // If createapikey.php outputs $apikey after including createapiuser.php, we can try to
        // include it and capture $apikey. To keep it safe, include in isolated scope.
        $apikey = null;
        if (file_exists(__DIR__ . '/createapikey.php')) {
            // capture any echoes and include the file which sets $apikey
            ob_start();
            include __DIR__ . '/createapikey.php';
            ob_end_clean();
            if (isset($apikey) && $apikey) {
                $log[] = 'Found apikey via createapikey.php';
            }
        }

        // If apikey not produced, skip trying to call MTN and return a mock response
        if (empty($apikey)) {
            $log[] = 'apikey not available - returning mock response (see createapikey.php workflow)';
            throw new Exception('No apikey');
        }

        // Create access token by calling createaccesstoken.php (it sets $access_token)
        if (file_exists(__DIR__ . '/createaccesstoken.php')) {
            ob_start();
            include __DIR__ . '/createaccesstoken.php';
            ob_end_clean();
        }

        if (empty($access_token)) {
            $log[] = 'access_token not obtained';
            throw new Exception('No access token');
        }

        // Now perform requesttopay
        $request_url = 'https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay';
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-Reference-Id: ' . $reference_id,
            'X-Target-Environment: sandbox',
            'Content-Type: application/json',
            'Ocp-Apim-Subscription-Key: ' . $secodary_key
        ];

        $external_id_val = $externalId !== '' ? $externalId : (string) rand(10000000, 99999999);
        $body = [
            'amount' => (string)$amount,
            'currency' => $currency,
            'externalId' => $external_id_val,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $phone
            ],
            'payerMessage' => 'Payment request',
            'payeeNote' => 'Thank you'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($body)
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $err = curl_error($curl);
            curl_close($curl);
            http_response_code(502);
            echo json_encode(['error' => 'cURL error: ' . $err, 'log' => $log]);
            exit;
        }

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpcode == 202) {
            echo json_encode(['status' => 'accepted', 'reference' => $reference_id, 'message' => 'Request accepted', 'log' => $log]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'MTN responded with status ' . $httpcode, 'response' => $response, 'log' => $log]);
            exit;
        }

    } else {
        // If we can't call MTN (missing apikey/access token), return a mock response and log the attempt
        $mockRef = bin2hex(random_bytes(12));
        $log[] = 'MTN flow not available; returning mock response';
        echo json_encode([
            'status' => 'mock',
            'message' => 'Mock payment created (no MTN credentials configured)',
            'referenceId' => $mockRef,
            'phone' => $phone,
            'amount' => $amount,
            'currency' => $currency,
            'externalId' => $externalId,
            'log' => $log
        ]);
        exit;
    }

} catch (Exception $e) {
    // Fallback mock response preserving error info
    $mockRef = bin2hex(random_bytes(12));
    http_response_code(200);
    echo json_encode([
        'status' => 'mock',
        'message' => 'Fallback mock response (error during MTN flow)',
        'error' => $e->getMessage(),
        'referenceId' => $mockRef,
        'phone' => $phone,
        'amount' => $amount,
        'currency' => $currency,
        'externalId' => $externalId,
        'log' => $log
    ]);
    exit;
}

?>
