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
    include 'bdd.php'; 
    $db = new BDD();

    // Variables par défaut
    $idMatch = null;
    $dateMatch = null;
    $nomAdversaires = null;
    $lieuRencontre = null;
    $domicileON = null;
    $avoirGagnerMatchON = null;
    $insert = true; // Par défaut, insérer un nouveau match

    // Récupérer les données si un IDMatch est fourni en GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['idMatch'])) {
        $idMatch = $_GET['idMatch'];
        $res = $db->getMatch($idMatch);

        if ($res) {
            $insert = false; // Si un match est trouvé, on est en mode mise à jour
            $dateMatch = $res['dateMatch'];
            $nomAdversaires = $res['nomAdversaires'];
            $lieuRencontre = $res['lieuRencontre'];
            $domicileON = $res['domicileON'];
            $avoirGagnerMatchON = $res['avoirGagnerMatchON'];
        }
    }

    // Gérer la soumission du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idMatch = $_POST['idMatch'] ?? null;
        $dateMatch = $_POST['dateMatch'];
        $nomAdversaires = $_POST['nomAdversaires'];
        $lieuRencontre = $_POST['lieuRencontre'];
        $domicileON = isset($_POST['domicileON']) ? 1 : 0;
        $avoirGagnerMatchON = isset($_POST['avoirGagnerMatchON']) ? 1 : 0;

        if ($insert) {
            // Insertion d'un nouveau match
            $success = $db->insertMatch($dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatchON);
        } else {
            // Mise à jour d'un match existant
            $success = $db->updateMatch($idMatch, $dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatchON);
        }

        // Afficher un message en fonction du succès ou de l'échec de l'opération
        if (isset($success)) {
            echo $success 
                ? "<div class='success'>Le match a été ajouté/mis à jour avec succès !</div>"
                : "<div class='error'>Une erreur est survenue : " . htmlspecialchars($db->getError()) . "</div>";
        }
    }
?>

<form action="ajout_match.php" method="POST">
    <div id="ajoutMatch">
        ID Match (automatique pour les nouveaux) : 
        <input type="text" name="idMatch" class="formulaireInsertion" readonly 
               value="<?php echo htmlspecialchars($idMatch); ?>"><br/>

        Date du Match* : 
        <input type="date" name="dateMatch" class="formulaireInsertion" required 
               value="<?php echo htmlspecialchars($dateMatch); ?>"><br/>

        Nom des Adversaires* : 
        <input type="text" name="nomAdversaires" class="formulaireInsertion" required maxlength="100" 
               value="<?php echo htmlspecialchars($nomAdversaires); ?>"><br/>

        Lieu de Rencontre* : 
        <input type="text" name="lieuRencontre" class="formulaireInsertion" required maxlength="100" 
               value="<?php echo htmlspecialchars($lieuRencontre); ?>"><br/>

        Match à domicile : 
        <input type="checkbox" name="domicileON" 
               <?php echo ($domicileON == 1) ? 'checked' : ''; ?>><br/>

        Victoire : 
        <input type="checkbox" name="avoirGagnerMatchON" 
               <?php echo ($avoirGagnerMatchON == 1) ? 'checked' : ''; ?>><br/>

        <input type="submit" id="submit" value="VALIDER" class="formulaireInsertion">
        <input type="reset" value="ANNULER" class="formulaireInsertion">
    </div>
</form>

</body>
</html>
