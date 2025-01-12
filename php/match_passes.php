<?php
require_once 'bdd.php';
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
        if (is_array($matchPassees) && !empty($matchPassees)) {
            foreach ($matchPassees as $matchPassee) {
                if (is_array($matchPassee)) {
                    $id = htmlspecialchars($matchPassee['IDMatch']);
                    $date = htmlspecialchars($matchPassee['dateMatch']);
                    $adversaire = htmlspecialchars($matchPassee['nomAdversaires']);
                    $details = 'Lieu: ' . htmlspecialchars($matchPassee['lieuRencontre']). '<br>Domicile: ' . htmlspecialchars($matchPassee['domicileON']);

                    echo "<tr class='collapsible' onclick='toggleRow(this)'>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$date}</td>";
                    echo "<td>{$adversaire}</td>";
                    echo "<td><form method='POST' action='modifierMatchPassee.php'><button type='submit' class='editButton' name='player_id' value='{$id}'>Edit player</button></form></td>";
                    echo "</tr>";
                    echo "<tr class='hidden hiddenStill'>";
                    echo "<td colspan='4'>{$details}</td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='4'>Invalid match data: " . htmlspecialchars(json_encode($matchPassee)) . "</td></tr>";
                }
            }
        } else {
            echo "<tr><td colspan='4'>No past matches found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    function toggleRow(row) {
        const nextRow = row.nextElementSibling;

        if (nextRow && nextRow.classList.contains('hidden')) {
            nextRow.classList.remove('hidden');
        } else if (nextRow) {
            nextRow.classList.add('hidden');
        }
    }
</script>

</body>
</html>
