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
    $errorMessage = '';

    // Récupère tous les joueurs actifs
    $joueurs = $db->getJoueursActif();

    // Vérifie si un match a été sélectionné
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id-match'])) {
            $idMatch = $_POST['id-match'];
        } else {
            $idMatch = $_POST['idMatch'];
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


        // Comptage des titulaires envoyés par le formulaire
        $totalTitulaireCount = 0;
        if (isset($_POST['titulaire']) && is_array($_POST['titulaire'])) {
            foreach ($_POST['titulaire'] as $titulaire) {
                if ($titulaire) {
                    $totalTitulaireCount++;
                }
            }
        }
        // Vérification si le nombre total de titulaires est exactement 6
        if ($totalTitulaireCount !== 6) {
            // Affiche un message d'erreur si le nombre total de titulaires n'est pas 6
            $errorMessage = 'Il faut exactement 6 joueurs titulaires. Veuillez vérifier votre sélection.';
        } else {
            // Traitement des modifications/ajouts/suppressions si le nombre est correct
            foreach ($joueurs as $joueur) {
                $numLicence = $joueur['numLicence'];
            
                if (isset($_POST['select'][$numLicence])) {
                    $titulaireON = isset($_POST['titulaire'][$numLicence]) ? 1 : 0;
                    $poste = $_POST['posteJoueur'][$numLicence] ?? null;
            
                    if (!$poste) {
                        continue;
                    }
                    // Insertion ou mise à jour
                    if (array_key_exists($numLicence, $selected)) {
                        $db->updateEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, null);
                    } else {
                        $db->insertEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, null);
                    }
                } elseif (isset($_POST['id-match']) && array_key_exists($numLicence, $selected)) {
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
    }
?>


<?php if ($errorMessage): ?>
    <div style="color: red; font-weight: bold; text-align: center;">
        <?= $errorMessage ?>
    </div>
<?php endif; ?>
<div id="containerTable">

<form method="POST" action="assigner_joueur.php">
    <!-- Champ caché pour idMatch -->
    <input type="hidden" name="id-match" value="<?= htmlspecialchars($idMatch ?? '') ?>">

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
            echo "<tr><td colspan='7'>Aucun joueur trouvé.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <button type="submit">Assigner les joueurs</button>
    <button type="reset">Réinitialiser</button>
</form>
</div>

</body>
</html>
