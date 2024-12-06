<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body>
<?php
$message = ''; // Initialiser la variable pour les messages
$successClass = 'error'; // Par défaut, la classe CSS est pour les erreurs

// Variables pour conserver les valeurs saisies
$loginValue = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
$passwordValue = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
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

                    $loginValue = '';
                    $passwordValue = '';
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
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
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
                   value="<?php echo $loginValue; ?>" 
                   required>
            <input type="password" 
                   placeholder="password (8-60 caractères)" 
                   name="password" 
                   class="fieldLogin" 
                   id="passwordId" 
                   value="<?php echo $passwordValue; ?>" 
                   required>
            <input type="submit" id='submit' value='INSERT' class="fieldLogin">
            <div id="messageErreur" class="<?php echo $successClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    </form>
</div>
</body>
</html>
