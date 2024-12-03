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
                    echo "Accès autorisé.";
                    // header('Location: /PhPProject_R3.01/acceuil.php');
                    // exit();
                } else {
                    echo "Accès refusé.";
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
        } else {
            echo "Veuillez remplir tous les champs.";
        }
    ?>
    <div class="loginPannel" >
        <h1 id="loginTitle">LOGIN</h1>
        <form action="index.php" method="POST">
        <div id="loginContainer">
            <input type="text" placeholder="username" name="username" class="fieldLogin" id="userNameId" required>
            <input type="password" placeholder="password" name="password" class="fieldLogin" required>
            <input type="submit" id='submit' value='LOGIN' class="fieldLogin" >
        </div>
        </form>
    </div>     
    </body>
</html>