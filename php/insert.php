<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body class="insert-page">
<?php
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }

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

            require 'bdd.php';
            $BDD = new BDD();
            if($BDD->getError()=='success'){

                if ($BDD->getPWD($login) == null) { 
                    $password = password_hash($password, PASSWORD_BCRYPT);
                    $BDD->insertUtilisateur($login, $password);
                    $message = "Utilisateur $login inséré avec succès.";
                    $successClass = 'success';
                } else {
                    $message = "Le login \"$login\" est déjà utilisé. Veuillez en choisir un autre.";
                }
                

            } else{
                $message = $BDD->getError();
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
    <h1 id="loginTitle">INSERT</h1>
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
