<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Odczytaj dane JSON z żądania
    $json_data = file_get_contents("php://input");
    // Przekształć dane JSON na tablicę PHP
    $data = json_decode($json_data, true);

    $dataRezerwacji = mysqli_real_escape_string($conn, $data['reservation_date']);
    $godzinaRezerwacjiOd = mysqli_real_escape_string($conn, $data['reservation_time_from']);
    $godzinaRezerwacjiDo = mysqli_real_escape_string($conn, $data['reservation_time_to']);
    $imieNazwisko = mysqli_real_escape_string($conn, $data['full_name']);
    $guests_count = mysqli_real_escape_string($conn, $data['guests_count']);
    $userId = mysqli_real_escape_string($conn, $data['user_id']);
    $restaurantId = mysqli_real_escape_string($conn, $data['restaurant_id']);

    $dateTimeOd = "$dataRezerwacji $godzinaRezerwacjiOd:00";
    $dateTimeDo = "$dataRezerwacji $godzinaRezerwacjiDo:00";

    $stmt = $conn->prepare("INSERT INTO reservation (ReservationFrom, ReservationTo, UserID, RestaruantID, PeopleNumber) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $dateTimeOd, $dateTimeDo, $userId, $restaurantId, $guests_count);

    if ($stmt->execute()) {
        $response = array('status' => 'success', 'message' => 'Rezerwacja została pomyślnie zapisana.');
    } else {
        $response = array('status' => 'error', 'message' => 'Błąd podczas zapisywania rezerwacji.');
    }

    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(405);
    echo 'Metoda nieobsługiwana';
}
?>
