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
        if (isset($_POST['delete_match_id']) && !empty($_POST['delete_match_id'])) {
            $deleteMatchId = $_POST['delete_match_id']; 
            
            $db->deleteMatch($deleteMatchId);
        }
    }
    echo $db->getError();

    header('Location: ./match_futurs.php');
    exit();
    ?>
    </body>
</html>