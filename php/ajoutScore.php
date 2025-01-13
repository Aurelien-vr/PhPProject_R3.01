<?php
require_once 'bdd.php';
$db = new BDD();

function isValidVolleyballScore($scoreEquipe, $scoreAdversaire, $tieBreak) {
    if ($scoreEquipe < 0 || $scoreAdversaire < 0) {
        return false;
    }

    if ($tieBreak) {
        $winningScore = 15;
    } else {
        $winningScore = 25;
    }
    
    if (($scoreEquipe >= $winningScore || $scoreAdversaire >= $winningScore) &&
        abs($scoreEquipe - $scoreAdversaire) >= 2) {
        return true;
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $idMatch = $_POST['match_id'];
    $match = $db->getMatch($idMatch);
    
    // Debugging: print POST data
    echo "<script>console.log('POST Data: ', " . json_encode($_POST) . ");</script>";

    if (isset($_POST['sets']) && is_array($_POST['sets'])) {
        // Debugging: print sets data
        echo "<script>console.log('Sets Data: ', " . json_encode($_POST['sets']) . ");</script>";

        foreach ($_POST['sets'] as $set) {
            $scoreEquipe = $set['scoreEquipe'];
            $scoreAdversaire = $set['scoreAdversaire'];
            $tieBreak = isset($set['tieBreak']) ? 1 : 0;

            if (isValidVolleyballScore($scoreEquipe, $scoreAdversaire, $tieBreak)) {
                $db->insertSet($scoreEquipe, $scoreAdversaire, $tieBreak, $idMatch);
            } else {
                echo "<script>alert('Invalid score for set');</script>";
            }
        }
    }

    $allSets = $db->getSets();
    echo "<script>console.log('All Sets: ', " . json_encode($allSets) . ");</script>";

} else {
    header("Location: match_passes.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Ajouter un Score</title>
    <script>
        function addSet() {
            const setsContainer = document.getElementById('setsContainer');
            const setCount = setsContainer.children.length + 1;

            if (setCount <= 5) {
                const setDiv = document.createElement('div');
                setDiv.className = 'form-group';
                setDiv.id = `set${setCount}`;
                setDiv.innerHTML = `
                    <h3>Set ${setCount}</h3>
                    <label for="scoreEquipe${setCount}">Score Équipe:</label>
                    <input type="number" id="scoreEquipe${setCount}" name="sets[${setCount}][scoreEquipe]" required>
                    <label for="scoreAdversaire${setCount}">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire${setCount}" name="sets[${setCount}][scoreAdversaire]" required>
                    <label for="tieBreak${setCount}">Tie Break:</label>
                    <input type="checkbox" id="tieBreak${setCount}" name="sets[${setCount}][tieBreak]">
                    <button type="button" onclick="removeSet(${setCount})">Remove Set</button>
                `;
                setsContainer.appendChild(setDiv);
            }
        }

        function removeSet(setNumber) {
            const setDiv = document.getElementById(`set${setNumber}`);
            if (setDiv) {
                setDiv.remove();
            }
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Ajouter un Score pour le Match ID: <?= htmlspecialchars($idMatch) ?></h1>
        <form method="post" action="ajoutScore.php">
            <input type="hidden" name="match_id" value="<?= htmlspecialchars($idMatch) ?>">
            <div id="setsContainer">
                <div class="form-group" id="set1">
                    <h3>Set 1</h3>
                    <label for="scoreEquipe1">Score Équipe:</label>
                    <input type="number" id="scoreEquipe1" name="sets[1][scoreEquipe]" required>
                    <label for="scoreAdversaire1">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire1" name="sets[1][scoreAdversaire]" required>
                    <label for="tieBreak1">Tie Break:</label>
                    <input type="checkbox" id="tieBreak1" name="sets[1][tieBreak]">
                </div>
                <div class="form-group" id="set2">
                    <h3>Set 2</h3>
                    <label for="scoreEquipe2">Score Équipe:</label>
                    <input type="number" id="scoreEquipe2" name="sets[2][scoreEquipe]" required>
                    <label for="scoreAdversaire2">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire2" name="sets[2][scoreAdversaire]" required>
                    <label for="tieBreak2">Tie Break:</label>
                    <input type="checkbox" id="tieBreak2" name="sets[2][tieBreak]">
                </div>
                <div class="form-group" id="set3">
                    <h3>Set 3</h3>
                    <label for="scoreEquipe3">Score Équipe:</label>
                    <input type="number" id="scoreEquipe3" name="sets[3][scoreEquipe]" required>
                    <label for="scoreAdversaire3">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire3" name="sets[3][scoreAdversaire]" required>
                    <label for="tieBreak3">Tie Break:</label>
                    <input type="checkbox" id="tieBreak3" name="sets[3][tieBreak]">
                </div>
            </div>
            <button type="button" onclick="addSet()">Ajouter un autre set</button>
            <button type="submit" class="editButton">Ajouter Scores</button>
        </form>
    </div>
</body>
</html>
