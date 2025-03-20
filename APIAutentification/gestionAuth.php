<?php

function getPasswordDB($username, $conn)
{
    $sql = "SELECT password FROM user WHERE username = '$username'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['password'];
}

function userExist($conn, $username) {
    $sql = "SELECT COUNT(*) FROM user WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['username' => $username]); 
    return $stmt->fetchColumn() > 0;
}
