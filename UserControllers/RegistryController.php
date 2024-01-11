<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

// Sprawdź, czy to zapytanie POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents("php://input");
    $data = json_decode($jsonInput);

    if ($data === null) {
        http_response_code(400);
        echo json_encode(array('error' => 'Błędne dane JSON'));
        exit();
    }

    if (isset($data->name, $data->surname, $data->email, $data->password)) {
        $username = strtolower(htmlspecialchars(strip_tags($data->name . '.' . $data->surname)));
        $firstName = htmlspecialchars(strip_tags($data->name));
        $lastName = htmlspecialchars(strip_tags($data->surname));
        $email = htmlspecialchars(strip_tags($data->email));
        $password = htmlspecialchars(strip_tags($data->password));

        // Sprawdź unikalność adresu email
        $checkEmailQuery = $conn->prepare("SELECT * FROM users WHERE Email = ?");
        $checkEmailQuery->bind_param("s", $email);
        $checkEmailQuery->execute();
        $checkEmailResult = $checkEmailQuery->get_result();

        if ($checkEmailResult->num_rows > 0) {
            http_response_code(400); 
            echo json_encode(array('error' => 'Adres email już istnieje'));
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $createDate = date("Y-m-d");
        $isAdmin = 0; // Domyślnie ustawione na 0 (false)

        // Zapytanie SQL do dodania nowego użytkownika
        $insertUserQuery = $conn->prepare("INSERT INTO users (UserName, Name, Surname, CreateDate, IsAdmin, Email, Password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertUserQuery->bind_param("ssssiss", $username, $firstName, $lastName, $createDate, $isAdmin, $email, $hashedPassword);

        if ($insertUserQuery->execute()) {
            http_response_code(201); // Kod odpowiedzi - Utworzono
            echo json_encode(array('message' => 'Użytkownik został utworzony pomyślnie'));
        } else {
            http_response_code(500); // Kod odpowiedzi - Błąd serwera
            echo json_encode(array('error' => 'Błąd podczas tworzenia użytkownika: ' . $conn->error));
        }

        $insertUserQuery->close();
    } else {
        http_response_code(400); // Kod odpowiedzi - Żądanie nieprawidłowe
        echo json_encode(array('error' => 'Brak wymaganych danych'));
    }
} else {
    http_response_code(405); // Kod odpowiedzi - Metoda nie dozwolona
    echo json_encode(array('error' => 'Nieprawidłowy typ żądania'));
}

// Zamknij połączenie z bazą danych
$conn->close();
?>
