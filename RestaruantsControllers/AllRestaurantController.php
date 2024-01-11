<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

// Sprawdź, czy to zapytanie GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Zapytanie SQL do pobrania wszystkich elementów z tabeli
    $sql = "SELECT * FROM restaurants";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Przygotuj tablicę na wyniki
        $items = array();

        // Pobierz wyniki zapytania
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        // Zwróć wyniki jako JSON
        echo json_encode($items);
    } else {
        // Brak wyników
        echo json_encode(array('message' => 'Brak danych'));
    }
} else {
    // Nieprawidłowe żądanie
    http_response_code(405); // Metoda nie jest dozwolona
    echo json_encode(array('error' => 'Nieprawidłowy typ żądania'));
}

// Zamknij połączenie z bazą danych
$conn->close();
?>
