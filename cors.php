<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT"); 
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true"); // Dodaj ten nagłówek, jeśli używasz uwierzytelniania z poziomu przeglądarki
header('Content-Type: application/json');

// Sprawdź, czy to zapytanie OPTIONS (przed zapytaniem POST lub DELETE)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
