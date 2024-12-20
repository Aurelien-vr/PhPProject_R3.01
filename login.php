<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
    <?php
        // http://localhost/PhPProject_R3.01/login.php
        // https://phpmyadmin.alwaysdata.com/phpmyadmin/index.php?route=/&route=%2F&lang=en
        $message = ''; // Initialiser la variable message
        $log = '';  // Initialiser le login

        // Vérifier si un message de succès a été passé après une redirection
        if (isset($_GET['message']) && $_GET['message'] == 'success') {
            $message = "Accès autorisé.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                $log = $_POST['username'];
                $pwd = $_POST['password'];

                require 'bdd.php';
                $BDD = new BDD();
                $error = $BDD->getError();
                if($error=='success'){
                    $result = $BDD->getPWD($log);
                    if ($result && password_verify($pwd, $result['motDePasse'])) {
                        session_start();
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login'] = $log;

                        // Redirection après connexion réussie
                        header('Location: /PhPProject_R3.01/php/acceuil.php?message=success');
                        exit();
                    } else {
                        $message = "Accès refusé.";
                    }
                } elseif (strpos($error, 'SQLSTATE') !== false) {
                    $message = "Connexion à la base de données échouée. Pensez à utiliser un réseau Wi-Fi sans restrictions.";
                } else {
                    $message = "Erreur lors de l'identification : " . $error;
                }
            } else {
                $message = "Veuillez remplir tous les champs.";
            }
        }
    ?>
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
