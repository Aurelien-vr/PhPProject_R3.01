<?php
require_once 'bdd.php';

ob_start();
include 'header.php';
ob_end_flush();

$db = new BDD();
$joueurs = $db->getJoueurs();

// Print the request method
echo "<script>console.log('Request method: " . $_SERVER['REQUEST_METHOD'] . "');</script>";

// Handle delete player request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_player_id']) && !empty($_POST['delete_player_id'])) {
        $deletePlayerId = intval($_POST['delete_player_id']);
        if (!$db->hasPlayedPastMatch($deletePlayerId)) {
            if ($db->deleteJoueur($deletePlayerId)) {
                echo "<script>console.log('Request was POST before redirect');</script>";
                header("Location: joueurs.php"); // Redirect to avoid resubmission
                exit();
            } else {
                echo "<script>console.log('Failed to delete player');</script>";
                echo $db->getError();
            }
        } else {
            echo "<script>alert('Cannot delete player who has played past matches');</script>";
        }
    } else {
        echo "<script>console.log('No player ID provided');</script>";
    }
}
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

<button onclick="window.location.href = 'ajout_joueurs.php';" class="bouttonAjouter">AJOUTER JOUEUR</button>

<div id="containerTable">
    <table>
        <thead>
            <tr>
                <th>Numero de Licence</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($joueurs) && !empty($joueurs)) {
                foreach ($joueurs as $joueur) {
                    $id = htmlspecialchars($joueur['numLicence']);
                    $name = htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']);
                    $details = 'Date de naissance: ' . htmlspecialchars($joueur['dateNaissance']) .
                    '<br>Taille: ' . htmlspecialchars($joueur['taille']) .
                    '<br>Poids: ' . htmlspecialchars($joueur['poids']) .
                    '<br>Statut: ' . htmlspecialchars($joueur['statutJoueur']) .
                    '<br>Commentaire: ' . htmlspecialchars($joueur['commentaire']);


                    echo "<tr class='collapsible' onclick='toggleRow(this)'>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$name}</td>";
                    echo "<td>
                            <form method='POST' action='modifier_joueur.php' style='display:inline;'>
                                <button type='submit' class='editPlayerButton' name='player_id' value='{$id}'>Edit player</button>
                            </form>";
                    if (!$db->hasPlayedPastMatch($id)) {
                        echo "<form method='POST' action='joueurs.php' style='display:inline;' onsubmit='return confirmDelete();'>
                            <input type='hidden' name='delete_player_id' value='$id'>
                            <button type='submit' class='deleteButton'>Delete</button>
                      </form>";
                    }
                    echo "</td>";
                    echo "</tr>";
                    echo "<tr class='hidden hiddenStill'>";
                    echo "<td colspan='3'>{$details}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No players found.</td></tr>";
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

    function confirmDelete() {
        return confirm('Are you sure you want to delete this player?');
    }
</script>

</body>
</html>