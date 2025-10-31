<?php
// makes requests on sandbox.momodeveloper.mtn.com and to see the info sent we use requesttopaycallbackurl.php

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read phone and amount from GET (index.php sends only these)
$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$amount = isset($_GET['amount']) ? trim($_GET['amount']) : '';
$currency = isset($_GET['currency']) ? trim($_GET['currency']) : 'EUR';

if ($phone === '' || $amount === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Missing phone or amount']);
  exit;
}

// Obtain access token and reference id by including the existing helper scripts.
// Suppress their echoed output by buffering.
ob_start();
include __DIR__ . '/createaccesstoken.php';
ob_end_clean();

// Ensure we have a reference_id (createaccesstoken/createapikey may set it). Fallback to uuid.
if (!isset($reference_id) || empty($reference_id)) {
  if (file_exists(__DIR__ . '/function.php')) include __DIR__ . '/function.php';
  if (function_exists('generate_uuid')) $reference_id = generate_uuid();
  else $reference_id = bin2hex(random_bytes(12));
}

// external id
$external_id = (string) rand(10000000, 99999999);

// Build request body
$body = [
  'amount' => (string)$amount,
  'currency' => $currency,
  'externalId' => $external_id,
  'payer' => [
    'partyIdType' => 'MSISDN',
    'partyId' => $phone
  ],
  'payerMessage' => 'Payment via sandbox',
  'payeeNote' => 'Thank you'
];

$json_body = json_encode($body);

// Prepare headers; $access_token and $secodary_key should be set by included files
$headers = [
  'Content-Type: application/json',
  'X-Target-Environment: sandbox',
  'X-Reference-Id: ' . $reference_id
];
if (isset($access_token) && $access_token) {
  $headers[] = 'Authorization: Bearer ' . $access_token;
}
if (isset($secodary_key) && $secodary_key) {
  $headers[] = 'Ocp-Apim-Subscription-Key: ' . $secodary_key;
}

$url = 'https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay';

$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => $headers,
  CURLOPT_POSTFIELDS => $json_body,
  CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($curl);
$curlErr = curl_errno($curl) ? curl_error($curl) : null;
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Try decode response
$decoded_response = json_decode($response, true);

// Only return the three values requested by the UI
// Return the reference, external, access token, and HTTP code
$out = [
  'reference_id' => $reference_id,
  'external_id' => $external_id,
  'access_token' => isset($access_token) ? $access_token : null,
  'http_code' => isset($httpcode) ? $httpcode : null
];

echo json_encode($out);
exit;
?>
exit;
?>
