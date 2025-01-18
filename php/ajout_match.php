<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body>

<?php include 'header.php'; ?>
<?php
    require 'bdd.php'; 
    $db = new BDD();
    $dateMatch = null;
    $nomAdversaires = null;
    $lieuRencontre = null;
    $domicileON = null;
    $avoirGagnerMatchON = null;
    $errorMessage = '';

    $insert = true;

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['idMatch'])) {
        $idMatch = $_GET['idMatch']; 
        $res = $db->getMatch($idMatch);
        $insert = false;
    
        if ($res) {
            if (isset($res['dateMatch'])) {
                // Formater la date en 'YYYY-MM-DDTHH:MM'
                $dateMatch = substr($res['dateMatch'], 0, 16); // Extrait 'YYYY-MM-DD HH:MM'
            }

            // Assigner d'autres valeurs à partir de la réponse de la base de données
            $nomAdversaires = $res['nomAdversaires'];
            $lieuRencontre = $res['lieuRencontre'];
            $domicileON = $res['domicileON'];
        }
    }

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dateMatch = $_POST['dateMatch'];
        $nomAdversaires = $_POST['nomAdversaires'];
        $lieuRencontre = $_POST['lieuRencontre'];
        $domicileON = isset($_POST['domicileON']) ? 1 : 0;

        if (empty($dateMatch) || strlen($dateMatch) < 16) {
            $errorMessage = 'Veuillez fournir une date et une heure valides pour le match.';
        } else {
            if ($insert) {
                // Insertion du match
                $success = $db->insertMatch($dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatchON);
                if ($success) {
                    header("Location: match_futurs.php");
                    exit();
                } else {
                    $errorMessage = 'Erreur lors de l\'insertion du match. Veuillez réessayer.';
                }
            } else {
                // Mise à jour du match
                $success = $db->updateMatch($idMatch, $dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatchON);
                if ($success) {
                    header("Location: match_futurs.php");
                    exit();
                } else {
                    $errorMessage = 'Erreur lors de la mise à jour du match. Veuillez réessayer.';
                }
            }
        }
    }
?>

<form action="ajout_match.php" method="POST">
    <div class="ajoutMatch">
        <?php if ($errorMessage): ?>
            <p style="color:red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        Date du Match* : 
        <input type="datetime-local" name="dateMatch" class="formulaireInsertion" required 
                value="<?php echo htmlspecialchars($dateMatch); ?>"><br/>

        Nom des Adversaires* : 
        <input type="text" name="nomAdversaires" class="formulaireInsertion" required maxlength="50" 
               value="<?php echo htmlspecialchars($nomAdversaires); ?>"><br/>

        Lieu de Rencontre* : 
        <input type="text" name="lieuRencontre" class="formulaireInsertion" required maxlength="250" 
               value="<?php echo htmlspecialchars($lieuRencontre); ?>"><br/>

        Match à domicile : 
        <input type="checkbox" name="domicileON" 
               <?php echo ($domicileON == 1) ? 'checked' : ''; ?>><br/>

        <input type="submit" id="submit" value="VALIDER" class="formulaireInsertion">
        <input type="reset" value="ANNULER" class="formulaireInsertion" onclick="location.href = 'match_futurs.php';">
    </div>
</form>

</body>
</html>
