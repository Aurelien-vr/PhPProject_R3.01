<?php
        // Connexion à la base de données
        include 'bdd.php';

        $bdd = new BDD();
        $query = "SELECT 
                    SUM(CASE WHEN avoirGagnerMatch = 1 THEN 1 ELSE 0 END) AS gagnes,
                    SUM(CASE WHEN avoirGagnerMatch = 0 THEN 1 ELSE 0 END) AS perdus,
                    SUM(CASE WHEN avoirGagnerMatch IS NULL THEN 1 ELSE 0 END) AS non_renseignes
                FROM Matchs";
        $stats = $bdd->createRequest($query, []);

        $gagnes = $stats['gagnes'] ?? 0;
        $perdus = $stats['perdus'] ?? 0;
        $non_renseignes = $stats['non_renseignes'] ?? 0;

        // Données pour le graphique
        $data = [$gagnes, $perdus, $non_renseignes];
        $colors = [[40, 167, 69], [220, 53, 69], [108, 117, 125]]; // Vert, rouge, gris

        // Dimensions de l'image
        $width = 400;
        $height = 400;

        // Création de l'image
        $image = imagecreatetruecolor($width, $height);

        // Couleur de fond (blanc)
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $background_color);

        // Calcul des angles
        $total = array_sum($data);
        $start_angle = 0;

        // Dessiner les segments
        for ($i = 0; $i < count($data); $i++) {
            $slice_angle = ($data[$i] / $total) * 360;
            $end_angle = $start_angle + $slice_angle;

            // Couleur du segment
            $color = imagecolorallocate($image, $colors[$i][0], $colors[$i][1], $colors[$i][2]);

            // Dessiner un arc
            imagefilledarc($image, $width / 2, $height / 2, $width - 20, $height - 20, 
                        $start_angle, $end_angle, $color, IMG_ARC_PIE);

            $start_angle = $end_angle;
        }

        // Afficher l'image
        header("Content-Type: image/png");
        imagepng($image);
        imagedestroy($image);
?>