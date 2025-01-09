<?php
require_once 'bdd.php'; // Use require_once to ensure it's only included once
$db = new BDD(); 

$joueurs = $db->getJoueurs();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
</head>
<body>

<?php include 'header.php';?>

<div id="containerTable">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($joueurs) && !empty($joueurs)) {
                foreach ($joueurs as $joueur) {
                    $id = htmlspecialchars($joueur['numLicence']);
                    $name = htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']);
                    $details = htmlspecialchars('More details about ' . $name . '...');

                    echo "<tr class='collapsible' onclick='toggleRow(this)'>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$name}</td>";
                    echo "</tr>";
                    echo "<tr class='hidden hiddenStill'>";
                    echo "<td colspan='2'>{$details}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No players found.</td></tr>";
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