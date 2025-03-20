<?php

require_once 'bdd.php';

function processPlayerEvaluation($postData) {
    $db = new BDD();

    if (!isset($postData['idMatch'])) {
        return "<div class='error'>ID de match non spécifié.</div>";
    }

    $idMatch = htmlspecialchars($postData['idMatch']);
    $joueurs = $db->getJoueursNotations($idMatch);

    // Check if players exist for the match
    if (!is_array($joueurs) || empty($joueurs)) {
        return "<div class='error'>Aucun joueur trouvé pour ce match.</div>";
    }

    // Update player ratings
    foreach ($joueurs as $joueur) {
        $numLicence = $joueur['numLicence'];
        $notation = $postData['notation'][$numLicence] ?? null;

        if (isset($notation) && is_numeric($notation) && $notation >= 0 && $notation <= 5) {
            $db->updateEtreSelectionner(
                $numLicence,
                $idMatch,
                $joueur['titulaireON'],
                $joueur['poste'],
                $notation
            );
        }
    }

    header('Location: ./match_passes.php');
    exit();
}
