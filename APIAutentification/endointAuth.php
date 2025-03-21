<?php // phpcs:ignore Generic.Files.LineEndings.InvalidEOLChar
require_once "jwt_utils.php"; // phpcs:ignore PEAR.Commenting.FileComment.Missing
require_once "dbConnection.php";
require_once "gestionAuth.php";

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {

case 'POST':
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'];
    $password = $input['password']; // Plain text password

    $conn = connectionDB();
        
    if (!userExist($conn, $username)) {  // Corrected function usage
        http_response_code(401);
        echo json_encode(['message' => 'User does not exist']);
        exit;
    }

    $stored_password = getPasswordDB($username, $conn);
    disconectDB($conn);

    if ($stored_password && password_verify($password."feur", $stored_password)) {
        $secret = "your_secret_key";
        $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'username' => $username,
            'exp' => time() + (60 * 60)
        ];
        $jwt = generate_jwt($headers, $payload, $secret);

        http_response_code(200);
        echo json_encode(['message' => 'Login successful', 'token' => $jwt]);
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