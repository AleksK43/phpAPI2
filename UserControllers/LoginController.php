<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Sprawdź, czy wszystkie wymagane pola są przekazane
    if (isset($data->email, $data->password)) {
        $email = htmlspecialchars(strip_tags($data->email));
        $password = htmlspecialchars(strip_tags($data->password));

        // Zapytanie SQL do pobrania użytkownika o podanym adresie email
        $getUserQuery = $conn->prepare("SELECT * FROM users WHERE Email = ?");
        $getUserQuery->bind_param("s", $email);
        $getUserQuery->execute();
        $userData = $getUserQuery->get_result()->fetch_assoc();

        if (!$userData) {
            http_response_code(401); // Nieautoryzowany
            echo json_encode(array('error' => 'Nieprawidłowy adres email lub hasło'));
            exit();
        }

        // Sprawdź hasło
        if (password_verify($password, $userData['Password'])) {
            http_response_code(200); // OK
            $response = array(
                'message' => 'Pomyślne logowanie',
                'user' => array(
                    'userId' => isset($userData['UserId']) ? $userData['UserId'] : null,
                    'userName' => isset($userData['UserName']) ? $userData['UserName'] : null,
                    'isAdmin' => isset($userData['IsAdmin']) ? $userData['IsAdmin'] : null
                )
            );
            echo json_encode($response);
        } else {
            http_response_code(401); // Nieautoryzowany
            echo json_encode(array('error' => 'Nieprawidłowy adres email lub hasło'));
        }

        $getUserQuery->close();
    } else {
        http_response_code(400); // Żądanie nieprawidłowe
        echo json_encode(array('error' => 'Brak wymaganych danych'));
    }
} else {
    http_response_code(405); // Metoda nie dozwolona
    echo json_encode(array('error' => 'Nieprawidłowy typ żądania'));
}

// Zamknij połączenie z bazą danych
$conn->close();
?>
