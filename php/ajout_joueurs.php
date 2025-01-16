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
    $numLicence = null;
    $nom = null;
    $prenom = null;
    $dateNaissance = null;
    $taille = null;
    $poids = null;
    $statutJoueur = null;
    $commentaire = null;
    $update = false;

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['numLicence'])) {
        $numLicence = $_GET['numLicence']; 
        $res = $db->getJoueur($numLicence);
        $insert = true;
    
        if ($res) {
            if (isset($res['nom'])) {
                $res = [$res];
            }
    
            // Parcourir les résultats
            foreach ($res as $joueur) {
                $nom = $joueur['nom'];
                $prenom = $joueur['prenom'];
                $dateNaissance = $joueur['dateNaissance'];
                $taille = $joueur['taille'];
                $poids = $joueur['poids'];
                $statutJoueur = $joueur['statutJoueur'];
                $commentaire = $joueur['commentaire'];
            }
        }
    }

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numLicence = $_POST['numLicence'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $dateNaissance = $_POST['dateNaissance'];
        $taille = !empty($_POST['taille']) ? $_POST['taille'] : null;
        $poids = !empty($_POST['poids']) ? $_POST['poids'] : null;
        $statutJoueur = $_POST['statutJoueur'];
        $commentaire = !empty($_POST['commentaire']) ? $_POST['commentaire'] : null;
    
        if ($insert) {
            // Vérifier si le numéro de licence existe déjà
            $existingJoueur = $db->getJoueur($numLicence);
            if ($existingJoueur) {
                echo "<div class='error'>Ce numéro de licence est déjà utilisé par le joueur " .
                    htmlspecialchars($existingJoueur[0]['nom']) . " " .
                    htmlspecialchars($existingJoueur[0]['prenom']) . ".</div>";
            } else {
                $success = $db->insertJoueur($numLicence, $nom, $prenom, $dateNaissance, $taille, $poids, $statutJoueur, $commentaire);
            }
        } else {
            $success = $db->updateJoueur($numLicence, $nom, $prenom, $dateNaissance, $taille, $poids, $statutJoueur, $commentaire);
        }
    
        if (isset($success)) {
            echo $success ? "<div class='success'>Le joueur a été ajouté/mis à jour avec succès !</div>"
                          : "<div class='error'>Une erreur est survenue : " . htmlspecialchars($db->getError()) . "</div>";
        }
    }
?>
<form action="ajout_joueurs.php" method="POST">
    <div id="ajoutJoueur">
        Numéro de Licence* : 
        <input type="text" name="numLicence" class="formulaireInsertion" required maxlength="50" 
               value="<?php echo htmlspecialchars($numLicence); ?>"><br/>
        
        Nom* : 
        <input type="text" name="nom" class="formulaireInsertion" required maxlength="50" 
               value="<?php echo htmlspecialchars($nom); ?>"><br/>
        
        Prénom* : 
        <input type="text" name="prenom" class="formulaireInsertion" required maxlength="50" 
               value="<?php echo htmlspecialchars($prenom); ?>"><br/>
        
        Date de Naissance* : 
        <input type="date" name="dateNaissance" class="formulaireInsertion" required 
               value="<?php echo htmlspecialchars($dateNaissance); ?>"><br/>
        
        Taille (en cm) : 
        <input type="number" placeholder="ex : 180" name="taille" class="formulaireInsertion" min="0" max="999" 
               value="<?php echo htmlspecialchars($taille); ?>"><br/>
        
        Poids : 
        <input type="number" placeholder="ex : 80.00" name="poids" class="formulaireInsertion" min="0" max="999.99" step="0.01" 
               value="<?php echo htmlspecialchars($poids); ?>"><br/>
        
        Statut actuel de Joueur : 
        <select name="statutJoueur">
            <option value="Actif" <?php echo ($statutJoueur === 'Actif') ? 'selected' : ''; ?>>Actif</option>
            <option value="Blessé" <?php echo ($statutJoueur === 'Blessé') ? 'selected' : ''; ?>>Blessé</option>
            <option value="Suspendu" <?php echo ($statutJoueur === 'Suspendu') ? 'selected' : ''; ?>>Suspendu</option>
            <option value="Absent" <?php echo ($statutJoueur === 'Absent') ? 'selected' : ''; ?>>Absent</option>
        </select><br/>
        
        Commentaire : 
        <input type="text" name="commentaire" class="formulaireInsertion" maxlength="250" 
               value="<?php echo htmlspecialchars($commentaire); ?>"><br/>
        
        <input type="submit" id="submit" value="VALIDER" class="formulaireInsertion">
        <input type="reset" value="ANNULER" class="formulaireInsertion">
    </div>
</form>


</body>
</html>
