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
    $selected = [];
    
    // Récupère tous les joueurs actifs
    $joueurs = $db->getJoueursActif();


    // Vérifie si un match a été sélectionné
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idMatch'])) {
        $idMatch = $_POST['idMatch'];

        // Recharge les données mises à jour dans $selected
        $result = $db->getJoueursPosteSelect($idMatch);
        $selected = [];
        foreach ($result as $row) {
            if (isset($row['numLicence'], $row['poste'], $row['titulaireON'])) {
                $selected[$row['numLicence']] = [
                    'poste' => $row['poste'],
                    'titulaireON' => $row['titulaireON']
                ];
            }
        }
    
        foreach ($joueurs as $joueur) {
            $numLicence = $joueur['numLicence'];
    
            if (isset($_POST['select'][$numLicence])) {
                $titulaireON = isset($_POST['titulaire'][$numLicence]) ? 1 : 0;
                $poste = $_POST['posteJoueur'][$numLicence] ?? null;
                if (array_key_exists($numLicence, $selected)) {
                    $db->updateEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, null, null);
                } else {
                    $db->insertEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, null, null);
                }
            } elseif (array_key_exists($numLicence, $selected)) {
                $db->deleteEtreSelectionner($numLicence, $idMatch);
            }
        }
    
        // Recharge les données mises à jour dans $selected
        $result = $db->getJoueursPosteSelect($idMatch);
        $selected = [];
        foreach ($result as $row) {
            if (isset($row['numLicence'], $row['poste'], $row['titulaireON'])) {
                $selected[$row['numLicence']] = [
                    'poste' => $row['poste'],
                    'titulaireON' => $row['titulaireON']
                ];
            }
        }
    }
    

?><div id="containerTable">
<form method="POST" action="assigner_joueur.php">
    <!-- Champ caché pour idMatch -->
    <input type="hidden" name="idMatch" value="<?= htmlspecialchars($idMatch) ?>">

    <table>
        <thead>
            <tr>
                <th>Numero de licence</th>
                <th>Nom</th>
                <th>Notation moyenne</th>
                <th>Poste favori</th>
                <th>Selectionner Joueur</th>
                <th>Poste assigne</th>
                <th>Titulaire</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($joueurs) && !empty($joueurs)) {
            foreach ($joueurs as $joueur) {
                $id = htmlspecialchars($joueur['numLicence']);
                $name = htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']);
                $notation = htmlspecialchars($db->getAVGNotationJoueur($id));
                $posteFavoris = htmlspecialchars($db->getPosteFavJoueur($id));

                $select = isset($selected[$id]);
                $posteJoueur = $select ? htmlspecialchars($selected[$id]['poste']) : '';
                $titulaireON = $select ? (bool)$selected[$id]['titulaireON'] : false;

            ?>
            <tr class="collapsible">
                <td><?= $id ?></td>
                <td><?= $name ?></td>
                <td><?= $notation ?></td>
                <td><?= $posteFavoris ?></td>
                <td><input type="checkbox" name="select[<?= $id ?>]" <?= $select ? 'checked' : '' ?>></td>
                <td>
                    <select name="posteJoueur[<?= $id ?>]">
                        <option value="passeur" <?= $posteJoueur === 'passeur' ? 'selected' : '' ?>>Passeur</option>
                        <option value="receptionneur-attaquant" <?= $posteJoueur === 'receptionneur-attaquant' ? 'selected' : '' ?>>Réceptionneur-Attaquant</option>
                        <option value="central" <?= $posteJoueur === 'central' ? 'selected' : '' ?>>Central</option>
                        <option value="pointue" <?= $posteJoueur === 'pointue' ? 'selected' : '' ?>>Pointue</option>
                        <option value="libero" <?= $posteJoueur === 'libero' ? 'selected' : '' ?>>Libero</option>
                    </select>
                </td>
                <td><input type="checkbox" name="titulaire[<?= $id ?>]" <?= $titulaireON ? 'checked' : '' ?>></td>
            </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='7'>Aucun joueur trouve.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <button type="submit">Assigner les joueurs</button>
    <button type="reset">Reinitialiser</button>
</form>
</div>

</body>
</html>
