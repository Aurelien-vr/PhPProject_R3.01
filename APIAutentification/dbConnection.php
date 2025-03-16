<?php

    function connectionDB()
    {
        $host = "localhost";
        $bd = "R401";
        $userName = "admin";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$bd", $userName);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $conn;
    }



    function getPasswordDB($userName, $conn)
    {
        $sql = "SELECT password FROM users WHERE username = '$userName'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['password'];
    }

    function disconectDB($conn){
        $conn = null;
    }