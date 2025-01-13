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

       // Calcul des pourcentages
       $query = "SELECT 
                   SUM(CASE WHEN avoirGagnerMatch = 1 THEN 1 ELSE 0 END) AS gagnes,
                   SUM(CASE WHEN avoirGagnerMatch = 0 THEN 1 ELSE 0 END) AS perdus,
                   SUM(CASE WHEN avoirGagnerMatch IS NULL THEN 1 ELSE 0 END) AS non_renseignes
                 FROM Matchs WHERE etreMatchPasseON = 1";
       $stats = $bdd->createRequest($query, []);
       $gagnes = $stats['gagnes'] ?? 0;
       $perdus = $stats['perdus'] ?? 0;
       $non_renseignes = $stats['non_renseignes'] ?? 0;
       $total = $gagnes + $perdus + $non_renseignes;
       
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

        $query = "
        SELECT 
            joueur.nomJoueur AS joueur,
            ROUND(AVG(note), 2) AS moyenne_note
        FROM 
            NotesJoueur nj
        JOIN 
            Joueurs joueur ON nj.idJoueur = joueur.idJoueur
        GROUP BY 
            joueur.nomJoueur
        ORDER BY 
            moyenne_note DESC
        LIMIT 3;
        ";

        $topJoueurs = $bdd->createRequest($query, []);

        include 'header.php'; 
    ?>

<div class="acceuilPCdiv">
    <h1>Statistiques des matchs passés</h1>
    <div id="divPc">
        <div class="pie-chart" style="<?= $pie_chart_style ?>"></div>
        <div class="legend">
            <?php if ($total > 0): ?>
                <div><span class="green"></span>Matchs Gagnés (<?= round($pourcentage_gagnes, 2) ?>%)</div>
                <div><span class="red"></span>Matchs Perdus (<?= round($pourcentage_perdus, 2) ?>%)</div>
                <div><span class="gray"></span>Non Renseignés (<?= round($pourcentage_non_renseignes, 2) ?>%)</div>
            <?php else: ?>
                <div><span class="gray"></span>Aucune donnée disponible</div>
            <?php endif; ?>
        </div>
    </div>
    <h2>Podium des meilleurs joueurs</h2>
    <div class="podium">
        <?php if (!empty($topJoueurs)): ?>
        <div class="place first">
            <span class="medal">🥇</span>
            <strong><?= $topJoueurs[0]['joueur'] ?></strong> (Moyenne : <?= $topJoueurs[0]['moyenne_note'] ?>)
        </div>
        <?php if (isset($topJoueurs[1])): ?>
        <div class="place second">
            <span class="medal">🥈</span>
            <strong><?= $topJoueurs[1]['joueur'] ?></strong> (Moyenne : <?= $topJoueurs[1]['moyenne_note'] ?>)
        </div>
        <?php endif; ?>
        <?php if (isset($topJoueurs[2])): ?>
        <div class="place third">
            <span class="medal">🥉</span>
            <strong><?= $topJoueurs[2]['joueur'] ?></strong> (Moyenne : <?= $topJoueurs[2]['moyenne_note'] ?>)
        </div>
        <?php endif; ?>
        <?php else: ?>
            <p>Aucune donnée disponible pour le podium.</p>
            <?php endif; ?>
        </div>
</div>

</body>
</html>
