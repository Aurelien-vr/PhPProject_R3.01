<?php
function isValidVolleyballScore($scoreEquipe, $scoreAdversaire, $tieBreak) {
    if ($scoreEquipe < 0 || $scoreAdversaire < 0) {
        return false;
    }

    if ($tieBreak) {
        if((($scoreEquipe > 15 || $scoreAdversaire > 15)) && abs($scoreEquipe - $scoreAdversaire) != 2) {
            return false;
        }
        if ($scoreEquipe < 15 && $scoreAdversaire < 15) {
            return false;
        }
    } else {
        if((($scoreEquipe > 25 || $scoreAdversaire > 25)) && abs($scoreEquipe - $scoreAdversaire) != 2) {
            return false;
        }
        if ($scoreEquipe < 25 && $scoreAdversaire < 25) {
            return false;
        }
    }


    return true;
}

function calculateMatchResult($sets) {
    $teamAWins = 0;
    $teamBWins = 0;

    foreach ($sets as $set) {
        if ($set['scoreEquipe'] > $set['scoreAdversaire']) {
            $teamAWins++;
        } else {
            $teamBWins++;
        }
    }

    if ($teamAWins >= 3 || $teamBWins >= 3) {
        return $teamAWins > $teamBWins ? 1 : 0;
    }

    return null;
}

function isValidMatch($sets) {
    $teamAWins = 0;
    $teamBWins = 0;

    foreach ($sets as $set) {
        if ($set['scoreEquipe'] > $set['scoreAdversaire']) {
            $teamAWins++;
        } else {
            $teamBWins++;
        }
    }

    $totalSets = count($sets);

    if ($totalSets < 3 || $totalSets > 5) {
        return false;
    }

    if ($totalSets == 3 && ($teamAWins == 3 || $teamBWins == 3)) {
        return true;
    }

    if ($totalSets == 4 && (($teamAWins == 3 && $teamBWins == 1) || ($teamAWins == 1 && $teamBWins == 3))) {
        return true;
    }

    if ($totalSets == 5 && (($teamAWins == 3 && $teamBWins == 2) || ($teamAWins == 2 && $teamBWins == 3))) {
        return true;
    }

    return false;
}

require_once 'bdd.php';
$db = new BDD();

$insert = true;    

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $idMatch = $_POST['match_id'];
    $match = $db->getMatch($idMatch);

    if (is_null($match) || empty($match)) {
        // Redirection ou message d'erreur si le match est invalide
        // header("Location: match_passes.php");
        // exit();
    } else {
        $sets = $db->getSets($idMatch);

        if (!is_null($sets) && !empty($sets)) {
            $insert = false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sets']) && is_array($_POST['sets'])) {
    $setsData = [];
    $validSets = true;

    foreach ($_POST['sets'] as $index => $set) {
        $scoreEquipe = $set['scoreEquipe'];
        $scoreAdversaire = $set['scoreAdversaire'];
        $tieBreak = ($index == 5) ? 1 : 0; // Automatically set tieBreak for the fifth set

        echo "<script>console.log('tieBreak: $tieBreak');</script>";

        if (isValidVolleyballScore($scoreEquipe, $scoreAdversaire, $tieBreak)) {
            $setsData[] = ['scoreEquipe' => $scoreEquipe, 'scoreAdversaire' => $scoreAdversaire, 'tieBreak' => $tieBreak];
        } else {
            $validSets = false;
            echo "<script>alert('Invalid score for set');</script>";
            break;
        }
    }

    if ($validSets && isValidMatch($setsData)) {
        $c = 0;
        foreach ($setsData as $set) {
            if ($insert) {
                $db->insertSet($set['scoreEquipe'], $set['scoreAdversaire'], $set['tieBreak'], $idMatch);
            } else {
                $db->updateSet($sets[$c]['IDSet'], $set['scoreEquipe'], $set['scoreAdversaire'], $set['tieBreak'], $idMatch);
            }
            $c++;
        }

        $matchResult = calculateMatchResult($setsData);
        echo "<script>console.log('matchResult: $matchResult');</script>";
        if (!is_null($matchResult)) {
            $db->updateAvoirGagnerMatchON($idMatch, $matchResult);
        }
        header("Location: match_passes.php");
        exit();
    } else {
        echo "<script>alert('Invalid match configuration');</script>";
    }

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
    // Passer les valeurs PHP sous forme de JSON dans JavaScript
    const sets = <?= isset($sets) ? json_encode($sets) : '[]'; ?>;
    const insert = <?= $insert ? 'true' : 'false'; ?>;

    function addSet() {
        const setsContainer = document.getElementById('setsContainer');
        const setCount = setsContainer.children.length + 1;

        console.log(setCount);

        if (setCount <= 5) {
            const setDiv = document.createElement('div');
            setDiv.className = 'form-group';
            setDiv.id = `set${setCount}`;
            
            const existingSet = sets[setCount - 1] || {}; // `setCount - 1` pour l'index
            const scoreEquipe = existingSet.scoreEquipe || '';
            const scoreAdversaire = existingSet.scoreAdversaire || '';
            const tieBreak = (setCount === 5) ? 'checked' : (existingSet.tieBreak ? 'checked' : '');

            setDiv.innerHTML = `
            <h3>Set ${setCount}</h3>
            <label for="scoreEquipe${setCount}">Score Équipe:</label>
            <input type="number" id="scoreEquipe${setCount}" name="sets[${setCount}][scoreEquipe]" value="${scoreEquipe}" required>
            <label for="scoreAdversaire${setCount}">Score Adversaire:</label>
            <input type="number" id="scoreAdversaire${setCount}" name="sets[${setCount}][scoreAdversaire]" value="${scoreAdversaire}" required>`;

            if (setCount === 5) {
                setDiv.innerHTML += `
                    <input type="hidden" name="sets[${setCount}][tieBreak]" value="1">`;
            }

            if (insert && setCount <= 5) {
                setDiv.innerHTML += `<button type="button" onclick="removeSet(${setCount})" class = "setAddByJs">Remove Set</button>`;
            }
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
    <div class="ajoutSets">
        <h1>Ajouter un Score pour le Match ID: <?= htmlspecialchars($idMatch) ?></h1>
        <form method="POST" action="ajout_score.php" id="ajoutScoreForm">
            <input type="hidden" name="match_id" value="<?= htmlspecialchars($idMatch) ?>">
            <div id="setsContainer" class="sets-container">
                <div class="form-group" id="set1">
                    <h3>Set 1</h3>
                    <label for="scoreEquipe1">Score Équipe:</label>
                    <input type="number" id="scoreEquipe1" name="sets[1][scoreEquipe]" value="<?php if(!$insert) echo htmlspecialchars($sets[0]['scoreEquipe']); ?>" required>
                    <label for="scoreAdversaire1">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire1" name="sets[1][scoreAdversaire]" value="<?php if(!$insert) echo htmlspecialchars($sets[0]['scoreAdversaire']); ?>" required>
                </div>
                <div class="form-group" id="set2">
                    <h3>Set 2</h3>
                    <label for="scoreEquipe2">Score Équipe:</label>
                    <input type="number" id="scoreEquipe2" name="sets[2][scoreEquipe]" value="<?php if(!$insert) echo htmlspecialchars($sets[1]['scoreEquipe']); ?>" required>
                    <label for="scoreAdversaire2">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire2" name="sets[2][scoreAdversaire]" value="<?php if(!$insert) echo htmlspecialchars($sets[1]['scoreAdversaire']); ?>" required>
                </div>
                <div class="form-group" id="set3">
                    <h3>Set 3</h3>
                    <label for="scoreEquipe3">Score Équipe:</label>
                    <input type="number" id="scoreEquipe3" name="sets[3][scoreEquipe]" value="<?php if(!$insert) echo htmlspecialchars($sets[2]['scoreEquipe']); ?>" required>
                    <label for="scoreAdversaire3">Score Adversaire:</label>
                    <input type="number" id="scoreAdversaire3" name="sets[3][scoreAdversaire]" value="<?php if(!$insert) echo htmlspecialchars($sets[2]['scoreAdversaire']); ?>" required>
                </div>
            </div>
            <?php if($insert): ?>
                <button type="button"  onclick='addSet()'>Ajouter un autre set</button>
            <?php endif; ?>
            
            <button type="submit" id="submit" class="formulaireInsertion">Ajouter le score</button>
        </form>
    </div>
</body>
</html>
