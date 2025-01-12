<?php
require_once 'bdd.php'; // Use require_once to ensure it's only included once

function insertSampleMatches() {
    $db = new BDD();
    $currentDate = date('Y-m-d');
    $matches = [
        ['2023-10-01', 'Team A', 'Stadium A', 1],
        ['2023-09-15', 'Team B', 'Stadium B', 0],
        ['2023-11-20', 'Team C', 'Stadium C', 1],
        ['2023-08-05', 'Team D', 'Stadium D', 0],
        ['2023-12-10', 'Team E', 'Stadium E', 1],
        ['2023-07-25', 'Team F', 'Stadium F', 0],
        ['2023-10-30', 'Team G', 'Stadium G', 1],
        ['2023-06-18', 'Team H', 'Stadium H', 0],
        ['2023-11-05', 'Team I', 'Stadium I', 1],
        ['2023-05-22', 'Team J', 'Stadium J', 0],
        ['2024-01-15', 'Team K', 'Stadium K', 1],
        ['2024-02-20', 'Team L', 'Stadium L', 0],
        ['2024-03-25', 'Team M', 'Stadium M', 1],
        ['2024-04-30', 'Team N', 'Stadium N', 0],
        ['2024-05-10', 'Team O', 'Stadium O', 1],
        ['2025-12-02', 'Team P', 'Stadium P', 1],
        ['2025-12-15', 'Team Q', 'Stadium Q', 0],
        ['2026-01-10', 'Team R', 'Stadium R', 1],
        ['2026-02-20', 'Team S', 'Stadium S', 0],
        ['2026-03-25', 'Team T', 'Stadium T', 1],
        ['2026-04-30', 'Team U', 'Stadium U', 0],
        ['2026-05-10', 'Team V', 'Stadium V', 1],
        ['2026-06-15', 'Team W', 'Stadium W', 0],
        ['2026-07-20', 'Team X', 'Stadium X', 1],
        ['2026-08-25', 'Team Y', 'Stadium Y', 0]
    ];

    foreach ($matches as $match) {
        $dateMatch = $match[0];
        $nomAdversaires = $match[1];
        $lieuRencontre = $match[2];
        $domicileON = $match[3];
        $avoirGagnerMatch = null;

        if ($dateMatch < $currentDate) {
            $avoirGagnerMatch = rand(0, 1); // Randomly set win or loss for past matches
        } else {
            $avoirGagnerMatch = null; // Explicitly set to null for future matches
        }

        $result = $db->insertMatch($dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatch);
        if ($result) {
            echo "Match inserted successfully.<br>";
        } else {
            echo "Failed to insert match: " . $db->getError() . "<br>";
        }
    }
}

// Insert sample matches
insertSampleMatches();
?>
