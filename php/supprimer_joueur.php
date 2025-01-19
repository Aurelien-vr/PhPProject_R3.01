<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
    <?php
    require_once 'bdd.php';
    $db = new BDD();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_player_id']) && !empty($_POST['delete_player_id'])) {
            $deletePlayerId = $_POST['delete_player_id'];
            if (!$db->hasPlayedPastMatch($deletePlayerId)) {
                $db->deleteJoueur($deletePlayerId);
            }
        }
    }

    header('Location: ./joueurs.php');
    exit();
    ?>
    </body>
</html>