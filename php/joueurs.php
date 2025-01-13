
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>


    <?php include 'header.php';?>
    <?php
        include 'bdd.php'; 
        $db = new BDD(); 
        $player = [34, 'John', 'Doe', '1990-05-15', 180, 75, 'Actif', 'Aucun'];
        $res = $db->insertJoueur($player[0], $player[1], $player[2], $player[3], $player[4], $player[5], $player[6], $player[7]);
        if ($res) {
            echo "Joueur ajouté avec succès !";
        } else {
            echo "Une erreur est survenue lors de l'ajout du joueur : " . $db->getError();
        }
        
        $joueurs = $db->getJoueurs();
        if ($joueurs) {
            $joueurs_json = json_encode($joueurs);
            echo "<script>console.log('Players:', $joueurs_json);</script>";
        } else {
            echo "<script>console.log('No players found.');</script>";
        }
    ?>

<div id="containerTable">
    <table>
        <thead>
            <tr>
                <th>ID</th>
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
                    echo "<td><form method='GET' action='ajout_joueurs.php'><button type='submit' class='editButton' name='numLicence' value='{$id}'>Edit player</button></form></td>";
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
    </script>

    </body>
</html>