<?php
require_once 'bdd.php';

ob_start();
include 'header.php';
ob_end_flush();

$db = new BDD();
$matchPassees = $db->getMatchsPassee();
$matchPasseesJson = json_encode($matchPassees);
echo "<script>console.log('$matchPasseesJson');</script>";

// Ensure $matchPassees is an array of arrays
if (is_array($matchPassees) && isset($matchPassees['IDMatch'])) {
    $matchPassees = [$matchPassees];
}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body>
<button onclick="window.location.href = 'ajout_match.php';">AJOUTER MATCH</button>

<div id="containerTable">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date du match</th>
                <th>Adversaire</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($matchPassees) && !empty($matchPassees)) {
            foreach ($matchPassees as $matchPassee) {
                $id = htmlspecialchars($matchPassee['IDMatch']);
                $date = htmlspecialchars($matchPassee['dateMatch']);
                $adversaire = htmlspecialchars($matchPassee['nomAdversaires']);
                $details = 'Lieu: ' . htmlspecialchars($matchPassee['lieuRencontre']) . '<br>Domicile: ' . htmlspecialchars($matchPassee['domicileON']);
        
                if (is_null($matchPassee['avoirGagnerMatchON'])) {
                    $resultClass = 'match-inconnu'; // Gris pour les valeurs nulles
                } elseif ($matchPassee['avoirGagnerMatchON'] == 1) {
                    $resultClass = 'match-gagne'; // Vert pour gagné
                } else {
                    $resultClass = 'match-perdu'; // Rouge pour perdu
                }
        
                echo "<tr class='collapsible $resultClass' onclick='toggleRow(this)'>";
                echo "<td>$id</td>";
                echo "<td>$date</td>";
                echo "<td>$adversaire</td><td>";
                
                echo "<form method='POST' action='ajout_score.php' style='display:inline;'>
                            <button type='submit' class='editButton' name='match_id' value='$id'>
                            ";
                if (is_null($matchPassee['avoirGagnerMatchON'])) {
                    echo "Ajouter un score";
                } else {
                    echo "Modifier le score";
                }
                echo "</button></form><form method='GET' action='ajout_match.php'><button type='submit' class='editButton' name='idMatch' value='{$id}'>Modifier match</button></form>";
                echo "</td></tr>";
                echo "<tr class='hidden hiddenStill'>";
                echo "<td colspan='4'>$details</td>";
                echo "</tr>";
            }
        }
             
        ?>
        </tbody>
    </table>
</div>

<script>
function toggleRow(row) {
    var nextRow = row.nextElementSibling;
    if (nextRow && nextRow.classList.contains('hiddenStill')) {
        nextRow.classList.toggle('hidden');
    }
}

function confirmDelete() {
    return confirm('Are you sure you want to delete this match?');
}
</script>

</body>
</html>