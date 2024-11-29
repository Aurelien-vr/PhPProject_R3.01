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

        $host = 'mysql-malekaurel.alwaysdata.net'; // Nom de l'hôte
        $dbname = 'malekaurel_bdd';           // Nom de la base de données
        $username = '386527';                     // Nom d'utilisateur MySQL
        $password = '9g^aYMeUs#yQKU';         // Mot de passe MySQL
        
        try {
            // Tentative de connexion à la base de données
            $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
        } catch (PDOException $e) {
            // Si la connexion échoue, affiche l'erreur
            echo "Erreur de connexion : Changer de réseau pour cous connecter à la base de données";
        }
        

        if(false){
            header('Location: /PhPProject_R3.01/acceuil.php');
            exit();
        }
    ?>
    <div class="loginPannel" >
        <h1 id="loginTitle">LOGIN</h1>
        <form action="verification.php" method="POST">
        <div id="loginContainer">
            <input type="text" placeholder="username" name="username" class="fieldLogin" id="userNameId" required>
            <input type="password" placeholder="password" name="password" class="fieldLogin" required>
            <input type="submit" id='submit' value='LOGIN' class="fieldLogin" >
        </div>
        </form>
    </div>        
    </body>
</html>