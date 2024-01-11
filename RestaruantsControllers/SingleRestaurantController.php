<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

// Sprawdź, czy to zapytanie GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Sprawdź, czy przekazano parametr ID
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Zapytanie SQL do pobrania konkretnego rekordu z tabeli
        $sql = "SELECT * FROM restaurants WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Pobierz wynik zapytania
            $row = $result->fetch_assoc();

            // Zwróć wynik jako JSON
            echo json_encode($row);
        } else {
            // Brak wyników dla danego ID
            echo json_encode(array('message' => 'Brak danych dla podanego ID'));
        }
    } else {
        // Brak przekazanego parametru ID
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'Brak przekazanego parametru ID'));
    }
} else {
    // Nieprawidłowe żądanie
    http_response_code(405); // Metoda nie jest dozwolona
    echo json_encode(array('error' => 'Nieprawidłowy typ żądania'));
}

// Zamknij połączenie z bazą danych
$conn->close();
?>
