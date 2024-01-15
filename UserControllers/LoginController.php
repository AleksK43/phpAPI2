<?php
require_once '../databaseconnection.php';
require_once '../cors.php';

// Ustawienie maksymalnego czasu trwania sesji na 30 minut
ini_set('session.gc_maxlifetime', 1800);
session_set_cookie_params(1800);

// Inicjalizacja sesji, ale tylko jeśli nie istnieje jeszcze
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Sprawdź, czy wszystkie wymagane pola są przekazane
    if (isset($data->email, $data->password)) {
        $email = htmlspecialchars($data->email);
        $password = $data->password;

        $getUserQuery = $conn->prepare("SELECT * FROM users WHERE Email = ?");
        $getUserQuery->bind_param("s", $email);
        $getUserQuery->execute();

        try {
            $userData = $getUserQuery->get_result()->fetch_assoc();

            if (!$userData) {
                throw new Exception('Nieprawidłowy adres email lub hasło');
            }

            // Sprawdź hasło
            if (password_verify($password, $userData['Password'])) {
                session_regenerate_id(true); // Regeneracja ID sesji po zalogowaniu

                $_SESSION['user'] = array(
                    'ID' => $userData['ID'] ?? null,
                    'userName' => $userData['UserName'] ?? null,
                    'isAdmin' => $userData['IsAdmin'] ?? null
                );

                // Dodaj nagłówek Content-Type do odpowiedzi
                header('Content-Type: application/json');

                http_response_code(200); // OK
                $response = array(
                    'message' => 'Pomyślne logowanie',
                    'user' => $_SESSION['user']
                );
                echo json_encode($response);
                exit;
            } else {
                throw new Exception('Nieprawidłowy adres email lub hasło');
            }
        } catch (Exception $e) {
            http_response_code(401); // Nieautoryzowany
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }

        $getUserQuery->close();
    } else {
        http_response_code(400); // Żądanie nieprawidłowe
        echo json_encode(array('error' => 'Brak wymaganych danych'));
        exit; 
    }
} else {
    http_response_code(405); // Metoda nie dozwolona
    echo json_encode(array('error' => 'Nieprawidłowy typ żądania'));
    exit; 
}

// Zamknij połączenie z bazą danych
$conn->close();
?>
