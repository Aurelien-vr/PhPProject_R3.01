<?php
require_once 'bdd.php';

// Debugging statements
ob_start();
include 'header.php';
ob_end_flush();

$db = new BDD();
$db->updateDateMatchs();
$matchFutres = $db->getMatchsFutur();
$matchFutresJson = json_encode($matchFutres);
echo "<script>console.log('Matches retrieved: $matchFutresJson');</script>";
echo "<script>console.log('Request Method: " . $_SERVER['REQUEST_METHOD'] . "');</script>";

// Ensure $matchFutres is an array of arrays
if (is_array($matchFutres) && isset($matchFutres['IDMatch'])) {
    $matchFutres = [$matchFutres];
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
<button onclick="window.location.href = 'ajout_match.php';" class="bouttonAjouter">AJOUTER MATCH</button>

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
        if (is_array($matchFutres) && !empty($matchFutres)) {
            foreach ($matchFutres as $matchFutur) {
                $id = htmlspecialchars($matchFutur['IDMatch']);
                $date = htmlspecialchars($matchFutur['dateMatch']);
                $adversaire = htmlspecialchars($matchFutur['nomAdversaires']);
                $details = 'Lieu: ' . htmlspecialchars($matchFutur['lieuRencontre']). '<br>Domicile: ' . htmlspecialchars($matchFutur['domicileON']);

                echo "<tr class='collapsible' onclick='toggleRow(this)'>";
                echo "<td>$id</td>";
                echo "<td>$date</td>";
                echo "<td>$adversaire</td>";
                echo "<td>
                        <form method='POST' action='assigner_joueur.php' style='display:inline;'>
                            <input type='hidden' name='idMatch' value='$id'>
                            <button type='submit' class='editButton'>Ajout de joueurs</button>
                        </form>
                        <form method='POST' action='supprimer_match.php' style='display:inline;'>
                            <input type='hidden' name='delete_match_id' value='$id'>
                            <button type='submit' class='deleteButton'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";   
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
</script>

</body>
</html>