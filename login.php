<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body class="login-page">
    <?php
        // Initialize variables
        $message = ''; // Initialiser la variable message
        $log = '';  // Initialiser le login

        // Handle success message from redirect
        if (isset($_GET['message']) && $_GET['message'] == 'success') {
            $message = "Accès autorisé.";
        }

        // Process login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate login credentials
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                $log = $_POST['username'];
                $pwd = $_POST['password'];

                // Database connection and verification
                require 'php/bdd.php';

                $BDD = new BDD();

                if($BDD->getError()=='success'){

                    $result = $BDD->getPWD($log);

                    if ($result!=null && password_verify($pwd, $result['motDePasse'])) {
                        session_start();
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login'] = $log;
                        
                        // Redirection après connexion réussie
                        $currentUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $newUrl = str_replace('login.php', 'php/acceuil.php?message=success', $currentUrl);
                        header('Location: ' . $newUrl);
                        exit();
                    } else {
                        $message = "Accès refusé." . $BDD->getError();
                    }
                } else{
                    $message = $BDD->getError();
                }
            } else {
                $message = "Veuillez remplir tous les champs.";
            }
        }
    ?>
    <!-- Login form container -->
    <div class="loginPannel">
        <h1 id="loginTitle">LOGIN</h1>
        <form action="login.php" method="POST">
            <div id="loginContainer">
                <input type="text" placeholder="username" value="<?php echo htmlspecialchars($log); ?>"  name="username" class="fieldLogin" id="userNameId" required>
                <input type="password" placeholder="password" name="password" class="fieldLogin" required>
                <input type="submit" id="submit" value="LOGIN" class="fieldLogin">
                <div id="messageErreur" class="error">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
        </form>
    </div>     
    </body>
</html>
