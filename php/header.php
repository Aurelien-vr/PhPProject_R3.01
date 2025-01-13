<!-- header.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}
?>
<header>
    <nav>
        <div class="nav"><a href="acceuil.php">ACCUEIL</a></div>
        <div class="nav"><a href="joueurs.php">JOUEURS</a></div>
        <div class="navs">
            <a href="match_futurs.php">MATCHS</a>
            <div class="submenu">
                <div><a href="match_passes.php">PASSÉS</a></div>
                <div><a href="match_futurs.php">FUTURS</a></div>
                <div><a href="ajout_match.php">NOUVEAU</a></div>
            </div>
        </div>
        <div class="nav"><a href="deconnection.php">SE DECONNECTER</a></div>
    </nav>
</header>