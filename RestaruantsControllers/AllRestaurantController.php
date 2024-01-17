<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM restaurants";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $items = array();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode($items);
    } else {
        echo json_encode(array('message' => 'Brak danych'));
    }
} else {
    http_response_code(405); 
    echo json_encode(array('error' => 'Nieprawidłowy typ żądania'));
}

$conn->close();
?>
