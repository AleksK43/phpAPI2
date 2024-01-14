<?php
require_once '../cors.php';
require_once '../databaseconnection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdź, czy wszystkie wymagane pola są ustawione
    if (isset($_POST['id']) && isset($_POST['newName']) && isset($_POST['newAdress'])) {
        $restaurantId = $_POST['id'];
        $newName = $_POST['newName'];
        $newAdress = $_POST['newAdress'];

        $query = "UPDATE restaurants SET RestaurantName = ?, Adress = ? WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $newName, $newAdress, $restaurantId);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Restauracja została pomyślnie zaktualizowana.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Błąd podczas aktualizacji restauracji: ' . $stmt->error;

            // Dodaj log do pliku lub gdziekolwiek chcesz
            error_log('Błąd SQL: ' . $stmt->error);
        }

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = 'Nieprawidłowe dane.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Nieprawidłowa metoda żądania.';
}

// Dodaj log do pliku lub gdziekolwiek chcesz
error_log(json_encode($response));

// Ustaw nagłówek Content-Type na application/json
header('Content-Type: application/json');

echo json_encode($response);
?>
