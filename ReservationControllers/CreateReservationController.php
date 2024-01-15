<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Odbierz dane z formularza
    $dataRezerwacji = $_POST['dataRezerwacji'];
    $godzinaRezerwacji = $_POST['godzinaRezerwacji'];
    $imieNazwisko = $_POST['imieNazwisko'];
    $email = $_POST['email'];

    // Tutaj umieść kod do zapisywania rezerwacji w bazie danych lub innych niezbędnych operacji

    $response = array('status' => 'success', 'message' => 'Rezerwacja została pomyślnie zapisana.');

    // Zwróć odpowiedź w formacie JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(405);
    echo 'Metoda nieobsługiwana';
}

?>
