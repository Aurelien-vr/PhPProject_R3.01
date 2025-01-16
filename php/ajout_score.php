<?php
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
    // echo "<script>console.log('POST Data: ', " . json_encode($_POST) . ");</script>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sets']) && is_array($_POST['sets'])) {

            // echo "<script>console.log('Sets Data: ', " . json_encode($_POST['sets']) . ");</script>";
            $c = 0;
            foreach ($_POST['sets'] as $set) {
                $scoreEquipe = $set['scoreEquipe'];
                $scoreAdversaire = $set['scoreAdversaire'];
                $tieBreak = isset($set['tieBreak']) ? 1 : 0;
                
                if (isValidVolleyballScore($scoreEquipe, $scoreAdversaire, $tieBreak)) {
                    if($insert){
                        $db->insertSet($scoreEquipe, $scoreAdversaire, $tieBreak, $idMatch);
                    } else {
                        var_dump($c);
                        var_dump($sets[$c]['IDSet']);
                        $db->updateSet($sets[$c]['IDSet'], $scoreEquipe, $scoreAdversaire, $tieBreak, $idMatch);
                    }
                } else {
                    echo "<script>alert('Invalid score for set');</script>";
                }
                $c++;
            }

            // calculer qui a gagner le match

        // echo "<script>console.log('All Sets: ', " . json_encode($db->getSets()) . ");</script>";

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
            const tieBreak = existingSet.tieBreak ? 'checked' : '';

            setDiv.innerHTML = `
            <h3>Set ${setCount}</h3>
            <label for="scoreEquipe${setCount}">Score Équipe:</label>
            <input type="number" id="scoreEquipe${setCount}" name="sets[${setCount}][scoreEquipe]" value="${scoreEquipe}" required>
            <label for="scoreAdversaire${setCount}">Score Adversaire:</label>
            <input type="number" id="scoreAdversaire${setCount}" name="sets[${setCount}][scoreAdversaire]" value="${scoreAdversaire}" required>`;

            if (setCount === 5) {
                setDiv.innerHTML += `
                    <label for="tieBreak${setCount}">Tie Break:</label>
                    <input type="checkbox" id="tieBreak${setCount}" name="sets[${setCount}][tieBreak]" ${tieBreak}>`;
            }

            if (insert) {
                setDiv.innerHTML += `<button type="button" onclick="removeSet(${setCount})">Remove Set</button>`;
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
    <div class="container">
        <h1>Ajouter un Score pour le Match ID: <?= htmlspecialchars($idMatch) ?></h1>
        <form method="POST" action="ajout_score.php">
            <input type="hidden" name="match_id" value="<?= htmlspecialchars($idMatch) ?>">
            <div id="setsContainer">
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
                <button type="button" onclick='addSet()'>Ajouter un autre set</button>
            <?php endif; ?>
            
            <button type="submit" class="editButton">Ajouter Scores</button>
        </form>
    </div>
</body>
</html>
