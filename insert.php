<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
    <?php
        if (!empty($_POST['username'])) {
            $login = $_POST['username'];
        }
        if (!empty($_POST['password'])) {
            $pwd = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        if (!empty($login) && !empty($pwd)) { // Vérifiez que les deux variables sont définies
            $host = 'mysql-malekaurel.alwaysdata.net';
            $dbname = 'malekaurel_bdd';
            $username = '386527';
            $password = '9g^aYMeUs#yQKU';

            try {
                $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }

            // Préparation de la requête avec les variables corrigées
            $stmt = $pdo->prepare("CALL insertUtilisateur(:login, :password)");
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pwd, PDO::PARAM_STR);

            try {
                $stmt->execute();
                echo "Utilisateur inséré avec succès.";
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
        } else {
            echo "Veuillez remplir tous les champs.";
        }
    ?>
    <div class="loginPannel" >
    <h1 id="loginPannel">INSERT TEMP</h1>
        <form action="insert.php" method="POST">
        <div id="loginContainer">
            <input type="text" placeholder="username" name="username" class="fieldLogin" id="userNameId" required>
            <input type="password" placeholder="password" name="password" class="fieldLogin" required>
            <input type="submit" id='submit' value='INSERT' class="fieldLogin" >
        </div>
        </form> 
    </div>       
    </body>
</html>