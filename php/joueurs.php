
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
                    </tr>
                </thead>
                <tbody>
                    <tr cclass="collapsible" onclick="toggleRow(this)">
                        <td>1</td>
                        <td>John Smith</td>
                    </tr>
                    <tr class="hidden hiddenStill">
                        <td colspan="2">More details about John Smith...</td>
                    </tr>
                    <tr class="foldable" onclick="toggleRow(this)">
                        <td>2</td>
                        <td>Alice Brown</td>
                    </tr>
                    <tr class="hidden hiddenStill">
                        <td colspan="2">
                            <div>
                                <p>More details about Alice Brown...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button onclick="window.location.href = '\ajout_joueurs.php';">INSERER JOUEUR</button>

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