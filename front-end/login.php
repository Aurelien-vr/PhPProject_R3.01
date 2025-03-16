<html lang="">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>



<body class="login-page">
<div class="loginPannel">
    <h1 id="loginTitle">LOGIN</h1>
    <form action="login.php" method="POST">
        <div id="loginContainer">
            <input type="text" placeholder="username" value="" name="username"
                   class="fieldLogin" id="userNameId" required>
            <input type="password" placeholder="password" name="password" class="fieldLogin" required>
            <input type="submit" id="submit" value="LOGIN" class="fieldLogin">
            <div id="messageErreur" class="error"></div>
        </div>
    </form>
</div>
</body>
</html>

<?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Hash the password
        $hashed_password = hash_hmac('sha256', $password, 'FYHGFCVBNJKIUYHGFVBN?KLKJUHYT5TYH');
        echo "<script>console.log('$hashed_password');</script>";
    }