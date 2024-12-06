<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
    <?php
        // http://localhost/PhPProject_R3.01/index.php
        // https://phpmyadmin.alwaysdata.com/phpmyadmin/index.php?route=/&route=%2F&lang=en
        
    $message = ''; // Initialiser la variable message

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $log = $_POST['username'];
            $pwd = $_POST['password'];

            $host = 'mysql-malekaurel.alwaysdata.net';
            $dbname = 'malekaurel_bdd';
            $username = '386527';
            $password = '9g^aYMeUs#yQKU';

            try {
                // Connexion à la base de données
                $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("SELECT motDePasse FROM Utilisateurs WHERE logins = :login");
                $stmt->bindParam(':login', $log, PDO::PARAM_STR);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result && password_verify($pwd, $result['motDePasse'])) {
                    $message = "Accès autorisé.";
                    // Redirection vers une autre page si nécessaire
                    // header('Location: /PhPProject_R3.01/acceuil.php');
                    // exit();
                } else {
                    $message = "Accès refusé.";
                }
            } catch (PDOException $e) {
                // Message spécifique en cas d'échec de connexion à la base de données
                if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                    $message = "Connexion à la base de données échouée. Pensez à utiliser un réseau Wi-Fi sans restrictions.";
                } else {
                    $message = "Erreur lors de l'identification : " . $e->getMessage();
                }
            }
        } else {
            $message = "Veuillez remplir tous les champs.";
        }
    }
    ?>
    <div class="loginPannel" >
        <h1 id="loginTitle">LOGIN</h1>
        <form action="index.php" method="POST">
        <div id="loginContainer">
            <input type="text" placeholder="username" name="username" class="fieldLogin" id="userNameId" required>
            <input type="password" placeholder="password" name="password" class="fieldLogin" required>
            <div id="messageErreur">  <?php echo htmlspecialchars($message); ?> </div>
            <input type="submit" id='submit' value='LOGIN' class="fieldLogin" >
        </div>
        </form>
    </div>     
    </body>
</html>