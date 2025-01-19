<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Volley Manager</title>
    <style>
        .pie-chart {
            display: block !important;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <?php
       include 'bdd.php';
       $bdd = new BDD();
        $bdd->updateDateMatchs();


       // Calcul des pourcentages
       $stats = $bdd->getPourcentagesMatchs();
        
       $gagnes = $stats['gagnes'] ?? 0;
       $perdus = $stats['perdus'] ?? 0;
       $non_renseignes = $stats['non_renseignes'] ?? 0;
       $total = $gagnes + $perdus + $non_renseignes;
       
       // Print match statistics to the console
       echo "<script>console.log('Matchs Gagnés: $gagnes, Matchs Perdus: $perdus, Non Renseignés: $non_renseignes, Total: $total');</script>";
       
       if ($total > 0) {
           $pourcentage_gagnes = ($gagnes / $total) * 100;
           $pourcentage_perdus = ($perdus / $total) * 100;
           $pourcentage_non_renseignes = ($non_renseignes / $total) * 100;
       } else {
           // Aucun match trouvé, tout sera gris
           $pourcentage_gagnes = 0;
           $pourcentage_perdus = 0;
           $pourcentage_non_renseignes = 100;
       }

       // Calcul du style dynamique pour le graphique en camembert
        $pie_chart_style = "background: conic-gradient(
            #28a745 0deg, 
            #28a745 " . ($pourcentage_gagnes * 360 /100) . "deg, 
            #dc3545 " . ($pourcentage_gagnes * 360 /100) . "deg, 
            #dc3545 " . (($pourcentage_gagnes + $pourcentage_perdus) * 360 /100) . "deg,
            #6c757d " . (($pourcentage_gagnes + $pourcentage_perdus) * 360 /100) . "deg
        );";

        include 'header.php'; 
       
       // Call getMatch and print the match details to the console
       ?>

<div class="acceuilPCdiv">
    <h1 class="h1">Statistiques des matchs passés</h1>
    <div id="divPc">
        <div class="pie-chart" style="<?= $pie_chart_style ?>"></div>
        <div class="legend">
            <?php if ($total > 0): ?>
                <div><span class="green"></span>Matchs Gagnés (<?= round($pourcentage_gagnes, 2) ?>%, <?= $gagnes ?>/<?= $total ?>)</div>
                <div><span class="red"></span>Matchs Perdus (<?= round($pourcentage_perdus, 2) ?>%, <?= $perdus ?>/<?= $total ?>)</div>
                <div><span class="gray"></span>Non Renseignés (<?= round($pourcentage_non_renseignes, 2) ?>%)</div>
            <?php else: ?>
                <div><span class="gray"></span>Aucune donnée disponible</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="divTabStat">
    <?php
       // Récupération des joueurs actifs
       $joueurs = $bdd->getJoueurs();

       // Ajout de la table avec les informations supplémentaires
       echo '<table>';
       echo '<thead>';
       echo '<tr>';
       echo '<th>Numéro de Licence</th>';
       echo '<th>Nom</th>';
       echo '<th>Statut</th>';
       echo '<th>Poste Favori</th>';
       echo '<th>Titularisations</th>';
       echo '<th>Remplacements</th>';
       echo '<th>Notation Moyenne</th>';
       echo '<th>Nb Matchs Consécutifs</th>';
       echo '<th>% Matchs Gagnés</th>';
       echo '</tr>';
       echo '</thead>';
       echo '<tbody>';

       // Parcours des joueurs et récupération des informations
       foreach ($joueurs as $joueur) {
           $numLicence = $joueur['numLicence'];
           $nom = $joueur['nom'] . ' ' . $joueur['prenom'];

           $statut = $joueur['statutJoueur'];
           $posteFavori = $bdd->getPosteFavJoueur($numLicence);
           $nbTitularisations = $bdd->getNbTitularisation($numLicence); // A faire
           $nbRemplacements = $bdd->getNbRemplacements($numLicence); // A faire
           $notationMoyenne = $bdd->getAVGNotationJoueur($numLicence);
           $nbMatchsConsecutifs = $bdd->getNbMatchConsecutif($numLicence); // A faire
           $pourcentageMatchsGagnes = $bdd->getPourcentagesMatchsGagnerJoueur($numLicence); // A faire

           // Affichage des données dans la table
           echo '<tr>';
           echo '<td>' . htmlspecialchars($numLicence) . '</td>';
           echo '<td>' . htmlspecialchars($nom) . '</td>';
           echo '<td>' . htmlspecialchars($statut) . '</td>';
           echo '<td>' . htmlspecialchars($posteFavori) . '</td>';
           echo '<td>' . htmlspecialchars($nbTitularisations) . '</td>';
           echo '<td>' . htmlspecialchars($nbRemplacements) . '</td>';
           echo '<td>' . htmlspecialchars($notationMoyenne) . '</td>';
           echo '<td>' . htmlspecialchars($nbMatchsConsecutifs) . '</td>';
           echo '<td>' . htmlspecialchars($pourcentageMatchsGagnes) . '%</td>';
           echo '</tr>';
       }

       echo '</tbody>';
       echo '</table>';
    ?>
    </div>
</body>
</html>
