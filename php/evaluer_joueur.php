<!DOCTYPE html>
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

    // Initialisation des variables
    $idMatch = null;
    $joueurs = [];

    // Vérification si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMatch'])) {
        $idMatch = $_POST['idMatch'];
        $joueurs = $db->getJoueursNotations($idMatch);

        // Mise à jour des données des joueurs
        if (is_array($joueurs) && !empty($joueurs)) {
            foreach ($joueurs as $joueur) {
                $numLicence = $joueur['numLicence'];
                
                $notation = $_POST['notation'][$numLicence] ?? null;
                $nbRemplacements = $_POST['nbRemplacement'][$numLicence] ?? null;

                // Mise à jour uniquement si des valeurs sont disponibles
                if (isset($notation, $nbRemplacements)) {
                    $db->updateEtreSelectionner(
                        $numLicence,
                        $idMatch,
                        $joueur['titulaireON'],
                        $joueur['poste'],
                        $notation,
                        $nbRemplacements
                    );
                }
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<div class='error'>ID de match non spécifié.</div>";
    }
?>

<div id="containerTable">
    <form method="POST" action="evaluer_joueur.php">
        <!-- Champ caché pour idMatch -->
        <input type="hidden" name="idMatch" value="<?= htmlspecialchars($idMatch) ?>">

        <table>
            <thead>
                <tr>
                    <th>Numero de licence</th>
                    <th>Nom</th>
                    <th>Poste</th>
                    <th>Titulaire</th>
                    <th>Notation</th>
                    <th>Nombre de remplacements</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $joueurs = $db->getJoueursNotations($idMatch);
            if (is_array($joueurs) && !empty($joueurs)) {
                foreach ($joueurs as $joueur) {
                    $id = htmlspecialchars($joueur['numLicence']);
                    $name = htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']);
                    $poste = htmlspecialchars($joueur['poste']);
                    $titulaire = $joueur['titulaireON'] ? 'Oui' : 'Non';
                    $notation = htmlspecialchars($joueur['notationJoueur'] ?? '');
                    $nbRemplacement = htmlspecialchars($joueur['nbRemplacements'] ?? '');
            ?>
                <tr>
                    <td><?= $id ?></td>
                    <td><?= $name ?></td>
                    <td><?= $poste ?></td>
                    <td><?= $titulaire ?></td>
                    <td><input type="number" name="notation[<?= $id ?>]" value="<?= $notation ?>" min="0" max="10"></td>
                    <td><input type="number" name="nbRemplacement[<?= $id ?>]" value="<?= $nbRemplacement ?>" min="0"></td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='6'>Aucun joueur trouvé.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <button type="submit">Noter les joueurs</button>
        <button type="reset">Réinitialiser</button>
    </form>
</div>

</body>
</html>
