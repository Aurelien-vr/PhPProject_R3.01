<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body>
<?php
    $message = '';
    $successClass = 'error'; 
    $login = '';

    // Vérifier si la page a été appelée après un POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = $_POST['username'];
        $password = $_POST['password'];

        // Vérification des tailles
        if (strlen($login) < 5 || strlen($login) > 50) {
            $message = "Le login doit avoir entre 5 et 50 caractères.";
        } elseif (strlen($password) < 8 || strlen($password) > 60) {
            $message = "Le mot de passe doit avoir entre 8 et 60 caractères.";
        } else {
            // Connexion à la base de données
            $host = 'mysql-malekaurel.alwaysdata.net';
            $dbname = 'malekaurel_bdd';
            $username = '386527';
            $dbPassword = '9g^aYMeUs#yQKU';

            try {
                $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $dbPassword);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Vérifier si le login existe déjà
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM Utilisateurs WHERE logins = :login");
                $stmt->bindParam(':login', $login, PDO::PARAM_STR);
                $stmt->execute();

                $loginExists = $stmt->fetchColumn();

                if ($loginExists) {
                    $message = "Le login \"$login\" est déjà utilisé. Veuillez en choisir un autre.";
                } else {
                    // Insérer l'utilisateur si le login est unique
                    $pwd = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("CALL insertUtilisateur(:login, :password)");
                    $stmt->bindParam(':login', $login, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $pwd, PDO::PARAM_STR);

                    $stmt->execute();
                    $message = "Utilisateur $login inséré avec succès.";
                    $successClass = 'success';

                    // Redirection pour éviter la soumission multiple de données lors du rafraîchissement
                    header('Location: insert.php?message=success');
                    exit; // Terminer l'exécution après la redirection
                }
            } catch (PDOException $e) {
                // Message spécifique en cas d'échec de connexion à la base de données
                if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                    $message = "Connexion à la base de données échouée. Pensez à utiliser un réseau Wi-Fi sans restrictions.";
                } else {
                    $message = "Erreur lors de l'insertion : " . $e->getMessage();
                }
            }
        }
    }

    // Vérifier s'il y a un message passé en GET via la redirection
    if (isset($_GET['message']) && $_GET['message'] == 'success') {
        $message = "Utilisateur inséré avec succès!";
        $successClass = 'success';
    }
?>

<div class="loginPannel">
    <h1 id="loginPannel">INSERT</h1>
    <form action="insert.php" method="POST">
        <div id="loginContainer">
            <input type="text" 
                   placeholder="username (5-50 caractères)" 
                   name="username" 
                   class="fieldLogin" 
                   id="userNameId" 
                   value="<?php echo htmlspecialchars($login); ?>" 
                   required>
            <input type="password" 
                   placeholder="password (8-60 caractères)" 
                   name="password" 
                   class="fieldLogin" 
                   id="passwordId" 
                   required>
            <input type="submit" id="submit" value="INSERT" class="fieldLogin">
            <div id="messageErreur" class="<?php echo $successClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    </form>
</div>

</body>
</html>
