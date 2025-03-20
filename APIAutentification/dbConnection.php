<?php // phpcs:ignore Generic.Files.LineEndings.InvalidEOLChar

function connectionDB() // phpcs:ignore PEAR.Commenting.FunctionComment.Missing
{
    $host = "localhost";
    $bd = "r401";
    $userName = "root";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$bd", $userName);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    return $conn;
}

function disconectDB($conn) // phpcs:ignore PEAR.Commenting.FunctionComment.Missing
{
    $conn = null;
}
