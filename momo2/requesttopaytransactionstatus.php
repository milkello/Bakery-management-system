<?php
include "config.php";
$reference_id = "ca2a30c8-4803-400c-ba1a-b9a569aac2b8";
$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSMjU2In0.eyJjbGllbnRJZCI6ImNhMmEzMGM4LTQ4MDMtNDAwYy1iYTFhLWI5YTU2OWFhYzJiOCIsImV4cGlyZXMiOiIyMDI1LTEwLTA1VDE2OjIzOjUyLjk5MiIsInNlc3Npb25JZCI6ImY3NzM2OWM5LTk0MDEtNDVhZC1hMDI1LTA5NDRiNTdkNjA2MSJ9.T5I6G3YNBz-XMkofsXJBNEQCb2fz1y7eK2eQjLlTuCZoA5To1run4X0wEmFxqWsseBOc9v2GhS7SSOcQI7vd-G_LYeRp9cQhn2mnlfbjkfGjotOCf8DVqLtgYnXy43Psu4nGJhX9jPlW81M0fwo2rehqiOg062Q3Kwq4NwPJffOkw2ub1LWNvGBnZ-zXm_mkcqj1lhAr-Tl2gAGx3aAMlRqgqdR2DXnonaDlKdjesIYoQz5iApRE3pbzSwZHsV8ynpXtPxwj-0nquW6R4frOIiK3tSj9Qe55QEIJ6tp96LFGf699zG1ssNnXRAKZB1Cs_WvAMzh8Gi2nwz84Kn6tNw"; 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/" . $reference_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); // Ensure GET request
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $access_token,
    "X-Target-Environment: sandbox",
    "Ocp-Apim-Subscription-Key: $secodary_key"
));
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo 'Response status code is : ' . $httpcode . "<br>";
echo 'Response: ' . $response;



