<?php
require_once 'bdd.php';
require_once 'insert_sample_matches.php'; // Corrected file path
$db = new BDD(); 
$matchFutres = $db->getMatchsFutur();
$matchFutresJson = json_encode($matchFutres);
echo "<script>console.log('$matchFutresJson');</script>";
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body>

<?php include 'header.php'; ?>
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
                $adversaire = htmlspecialchars($matchFutur['nomAdversaire']);
                $details = 'Lieu: ' . htmlspecialchars($matchFutur['lieuRencontre']). '<br>Domicile: ' . htmlspecialchars($matchFutur['domicileON']);

                echo "<tr class='collapsible' onclick='toggleRow(this)'>";
                echo "<td>{$id}</td>";
                echo "<td>{$date}</td>";
                echo "<td>{$adversaire}</td>";
                echo "<td><form method='POST' action='modifierMatchFutur.php'><button type='submit' class='editButton' name='player_id' value='{$id}'>Edit player</button></form></td>";
                echo "</tr>";
                echo "<tr class='hidden hiddenStill'>";
                echo "<td colspan='3'>{$details}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No future matches found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>