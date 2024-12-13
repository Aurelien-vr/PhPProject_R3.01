<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
        <?php
            session_start();
            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                header('Location: login.php');
                exit;
            }
        ?>
        <header>
            <nav>
                <div class="nav"><a href="">ACCEUIL</a></div>
                <div class="nav"><a href="joueurs.php">JOUEURS</a></div>
                <div class="navs">
                    <a href="match_futurs.php">MATCHS</a>
                    <div class="submenu">
                        <div><a href="match_passes.php">PASSÉS</a></div>
                        <div><a href="match_futurs.php">FUTURS</a></div>
                    </div>
                </div>
                <div class="nav"><a href="deconnection.php">SE DECONNECTER</a></div>
            </nav>
        </header>
    </body>
</html>