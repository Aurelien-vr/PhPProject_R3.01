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
