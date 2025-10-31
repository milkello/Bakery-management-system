<?php
include "config.php";
$reference_id = "f2d87196-2ba8-43e1-9aa8-88754c6a4b5f";
$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSMjU2In0.eyJjbGllbnRJZCI6ImViZGNmNzg1LWViNzctNDJhYy1iMGYxLTUyNjcwNWE0MGJmNyIsImV4cGlyZXMiOiIyMDI1LTEwLTA1VDE2OjE0OjA3LjUyMyIsInNlc3Npb25JZCI6ImU3ODNkY2I4LWZlZTItNDM3MC04MjcxLTQ0YzI1ZmE1M2ZlMCJ9.JowBZxD7yKICk6_X9Re6_UpDMO0m-HRChCxu-h_pWqQyddMeGOgoL5dgs36ExSnqqD0xD_0kkkYHf2sRo7c5IuwDh9goUaAHAlYR-TTVma34G3K9dj_0jr4-E5RTkcaZjVUI8ej_A72J2bVFNG34osgOIvcBWwW_cuUw_qdQ83uNdfuflt7ythucytG0x7Ue87kXmjLRU0aJQ80DoXR70HdBZyiH-ZV1gKUcM8UbiK2LsfagPcrQUbqU205JDK4-bKuU3tUrVKIsgiZ4q-4VYS5Hkox6q3FhUffrbPv9Tl7Ts040hpmK3egSX_Xf65DxJbogQfyy6nYMInyNfDOO6w"; 
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



