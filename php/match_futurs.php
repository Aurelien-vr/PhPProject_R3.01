<?php
require_once 'bdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_match_id'])) {
    $idMatch = $_POST['delete_match_id'];
    $db = new BDD();
    $db->deleteMatch($idMatch);
    header("Location: match_futurs.php");
    exit();
}

ob_start();
include 'header.php';
ob_end_flush();

$db = new BDD();
$matchFutres = $db->getMatchsFutur();
$matchFutresJson = json_encode($matchFutres);
echo "<script>console.log('$matchFutresJson');</script>";

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
        if (is_array($matchFutres) && !empty($matchFutres)) {
            foreach ($matchFutres as $matchFutur) {
                $id = htmlspecialchars($matchFutur['IDMatch']);
                $date = htmlspecialchars($matchFutur['dateMatch']);
                $adversaire = htmlspecialchars($matchFutur['nomAdversaires']); // Corrected key
                $details = 'Lieu: ' . htmlspecialchars($matchFutur['lieuRencontre']). '<br>Domicile: ' . htmlspecialchars($matchFutur['domicileON']);

                echo "<tr class='collapsible' onclick='toggleRow(this)'>";
                echo "<td>$id</td>";
                echo "<td>$date</td>";
                echo "<td>$adversaire</td>";
                echo "<td>
                        <form method='POST' action='assigner_joueur.php' style='display:inline;'>
                            <button type='submit' class='editButton' name='idMatch' value='$id'>Ajout de joueurs</button>
                        </form>
                        <form method='POST' style='display:inline;' onsubmit='return confirmDelete();'>
                            <input type='hidden' name='delete_match_id' value='$id'>
                            <button type='submit' class='deleteButton'>Delete</button>
                        </form>
                        <form method='GET' action='ajout_match.php'><button type='submit' class='editButton' name='idMatch' value='{$id}'>Modifier le match</button></form>
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

function confirmDelete() {
    return confirm('Are you sure you want to delete this match?');
}
</script>

</body>
</html>