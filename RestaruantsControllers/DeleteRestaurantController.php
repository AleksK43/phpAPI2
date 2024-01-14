<?php
require_once '../cors.php';
require_once '../databaseconnection.php';

$response = array();

if (isset($_GET['id'])) {
    $restaurantId = $_GET['id'];

    // Zabezpieczenie przed SQL injection
    $query = "DELETE FROM restaurants WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $restaurantId);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Restauracja została pomyślnie usunięta.';
    } else {
        $response['success'] = false;
        $response['message'] = 'Błąd podczas usuwania restauracji.';
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = 'Nieprawidłowe ID restauracji.';
}

echo json_encode($response);
?>