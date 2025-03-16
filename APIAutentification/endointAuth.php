<?php
require_once "jwt_utils.php";
require_once "dbConnection.php";
require_once "gestionAuth.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'];
        $hashed_password = $input['password']; // Assuming the password is already hashed

        $conn = connectionDB();
        $stored_password = getPasswordDB($username, $conn);
        disconectDB($conn);

        if ($stored_password && $hashed_password === $stored_password) {
            http_response_code(200);
            echo json_encode(['message' => 'Login successful']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
