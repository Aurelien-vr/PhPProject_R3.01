<!DOCTYPE HTML> 
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
    <?php
        class BDD {
            private $pdo; 
            private $error;

            public function __construct() {
                $this->error = '';
                try {
                    $host = 'mysql-malekaurel.alwaysdata.net';
                    $dbname = 'malekaurel_bdd';
                    $username = '386527';
                    $password = '9g^aYMeUs#yQKU';

                    // Connexion à la base de données
                    $this->pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
                    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $this->error = 'success';
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                        $this->error = "Connexion à la base de données échouée. Pensez à utiliser un réseau Wi-Fi sans restrictions.";
                    } else {
                        $this->error = "Erreur lors de l'identification : " . $e;
                    }
                }
            }

            public function getError() {
                return $this->error;
            }
            public function createRequest($request, $param) {
                $this->error = '';
                try {
                    $stmt = $this->pdo->prepare($request);

                    if ($param != null) {
                        foreach ($param as $key => $value) {
                            $stmt->bindValue($key, $value, PDO::PARAM_STR);
                        }
                    }

                    $stmt->execute();

                    // Determine if it's a SELECT
                    if (stripos($request, 'SELECT') === 0) {
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (count($results) == 1) {
                            return $results[0]; // Return single result if only one row is found
                        }
                        return $results; // Return array of results if multiple rows are found
                    }

                    // For INSERT, UPDATE, DELETE, return true if success
                    return true;
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                    return false;
                }
            }

            // SELECT   
            public function getPWD($log) {
                $param = [
                    ':login' => $log
                ];
                return $this->createRequest("SELECT motDePasse FROM Utilisateurs WHERE logins = :login", $param);
            }

            public function getJoueurs() {
                $result = $this->createRequest("SELECT * FROM Joueurs ORDER BY nom, prenom", []);
                if (!is_array($result)) {
                    return [];
                }
                return $result;
            }

            public function getJoueursActif() {
                $result = $this->createRequest("SELECT * FROM Joueurs WHERE statutJoueur = 'Actif' ORDER BY nom, prenom", []);
                if (!is_array($result) || (isset($result['numLicence']))) {
                    // Si un seul joueur est retourné, encapsulez-le dans un tableau
                    $result = [$result];
                }
                return $result;
            }
            
            public function getJoueur($id) {
                $param = [
                    ':id' => $id
                ];
                return $this->createRequest(
                    "SELECT * FROM Joueurs  WHERE numLicence = :id ORDER BY nom, prenom", 
                    $param
                );
            }

            public function getJoueursPosteSelect($idMatch)
            {
                $param = [
                    ':idMatch' => $idMatch
                ];
                $result = $this->createRequest(
                    "SELECT numLicence, poste, titulaireON FROM EtreSelectionner WHERE idMatch = :idMatch",
                    $param
                );
            
                if (empty($result)) {
                    return [];
                }
            
                if (isset($result['numLicence'])) {
                    return [$result]; // Encapsule la ligne unique dans un tableau
                }
            
                return $result;
            }

            public function getJoueursNotations($idMatch){
                $param = [
                    ':idMatch' => $idMatch
                ];
                $result = $this->createRequest(
                    "SELECT Joueurs.numLicence, nom, prenom, poste, titulaireON, notationJoueur FROM EtreSelectionner JOIN Joueurs ON EtreSelectionner.numLicence = Joueurs.numLicence WHERE idMatch = :idMatch",
                    $param
                );

                if (empty($result)) {
                    return [];
                }
            
                if (isset($result['numLicence'])) {
                    return [$result];
                }
            
                return $result;
            }

            public function getPourcentagesMatchs(){
                $query = "SELECT 
                        SUM(CASE WHEN avoirGagnerMatchON = 1 THEN 1 ELSE 0 END) AS gagnes,
                        SUM(CASE WHEN avoirGagnerMatchON = 0 THEN 1 ELSE 0 END) AS perdus,
                        SUM(CASE WHEN avoirGagnerMatchON IS NULL THEN 1 ELSE 0 END) AS non_renseignes
                    FROM Matchs WHERE etreMatchPasseON = 1";
                $result = $this->createRequest($query, []);
                return $result;
            }
            
            public function getPourcentagesMatchsGagnerJoueur($idJoueur){
                $query = "SELECT 
                        SUM(CASE WHEN avoirGagnerMatchON = 1 THEN 1 ELSE 0 END) AS gagnes,
                        SUM(CASE WHEN avoirGagnerMatchON = 0 OR avoirGagnerMatchON IS NULL THEN 1 ELSE 0 END) AS autre
                        FROM Matchs 
                        JOIN EtreSelectionner ON Matchs.idMatch = EtreSelectionner.idMatch
                        WHERE etreMatchPasseON = 1
                        AND numLicence = :idJoueur";
                $result = $this->createRequest($query, [':idJoueur'=> $idJoueur]);
                $gagner = $result['gagnes'] ?? 0;
                $coef = $gagner + $result['autre'] ?? 0;
                if($coef!=0){
                    $total = $gagner / ($gagner + $result['autre'] ?? 0) * 100;
                    return round($total, 2);
                }
                return 'N/A';
            }
            

            public function getAVGNotationJoueur($idJoueur) {
                $result = $this->createRequest(
                    "SELECT AVG(notationJoueur) as avgNotation FROM EtreSelectionner WHERE numLicence = :idJoueur",
                    [':idJoueur' => $idJoueur]
                );
            
                if ($result && isset($result['avgNotation'])) {
                    // Arrondir à une décimale
                    $avgNotation = round($result['avgNotation'], 1);
            
                    // Si la notation arrondie est un entier (par exemple 5.0), la convertir en entier
                    if (intval($avgNotation) == $avgNotation) {
                        return intval($avgNotation); // Retourne un entier si la notation est un entier
                    }
                    
                    return $avgNotation; // Sinon, retourne la notation arrondie à 1 décimale
                } else {
                    return 'N/A'; // Retourne null si aucun résultat n'est trouvé
                }
            }
            
            

            public function getPosteFavJoueur($idJoueur) {
                $result = $this->createRequest(
                    "SELECT poste, COUNT(poste) as countPoste
                    FROM EtreSelectionner
                    WHERE numLicence = :idJoueur
                    GROUP BY poste
                    ORDER BY countPoste DESC
                    LIMIT 1",
                    [':idJoueur' => $idJoueur]
                );
            
                if ($result && isset($result['poste'])) {
                    return $result['poste']; // Retourne le poste favori
                } else {
                    return 'N/A'; // Retourne null si aucun poste n'est trouvé
                }
            }

            public function getNbTitularisation($numLicence){
                $result = $this->createRequest(
                    "SELECT COUNT(idMatch) as a
                    FROM EtreSelectionner
                    WHERE numLicence = :idJoueur
                    AND titulaireON = '1'",
                    [':idJoueur' => $numLicence]
                );
                return $result['a'];
            }
            
            public function getNbRemplacements($numLicence){
                $result = $this->createRequest(
                    "SELECT COUNT(idMatch) as a
                    FROM EtreSelectionner
                    WHERE numLicence = :idJoueur
                    AND titulaireON = '0'",
                    [':idJoueur' => $numLicence]
                );
                return $result['a'];
            }
            
            public function getNbMatchConsecutif($numLicence) {
                // Récupère les matchs passés en ordre décroissant (du plus récent au plus ancien)
                $query = "SELECT Matchs.idMatch, dateMatch
                          FROM Matchs
                          JOIN EtreSelectionner ON Matchs.idMatch = EtreSelectionner.idMatch
                          WHERE EtreSelectionner.numLicence = :numLicence
                          AND Matchs.dateMatch < NOW()
                          ORDER BY Matchs.dateMatch DESC";
            
                $matches = $this->createRequest($query, [':numLicence' => $numLicence]);
            
                if (empty($matches)) {
                    return 0; // Aucun match trouvé
                }
            
                $consecutiveCount = 0;
                $previousDate = null;
            
                foreach ($matches as $match) {
                    // Vérifie que $match est un tableau associatif
                    if (!is_array($match) || !isset($match['dateMatch'])) {
                        continue; // Passe à l'itération suivante si $match est invalide
                    }
            
                    // Vérifie si la date est bien une chaîne de caractères
                    $currentDate = new DateTime($match['dateMatch']);
            
                    if ($previousDate === null) {
                        // Premier match traité
                        $consecutiveCount++;
                    } else {
                        // Vérifie si la différence entre les dates dépasse 1 jour (pas consécutif)
                        $diff = $previousDate->diff($currentDate)->days;
                        if ($diff > 1) {
                            break; // Pas consécutif, stoppe la boucle
                        }
                        $consecutiveCount++;
                    }
            
                    $previousDate = $currentDate;
                }
            
                return $consecutiveCount;
            }
            
            
            
            public function getMatchsPassee() {
                $result = $this->createRequest("SELECT * FROM Matchs WHERE dateMatch < NOW() ORDER BY dateMatch DESC", []);
                if (!is_array($result)) {
                    return [];
                }
                return $result;
            }

            public function getMatchsFutur() {
                $result = $this->createRequest("SELECT * FROM Matchs WHERE dateMatch >= NOW() ORDER BY dateMatch", []);
                if (!is_array($result)) {
                    return [];
                }
                return $result;
            }

            public function getSets($idMatch) {
                $param = [
                    ':idMatch' => $idMatch
                ];
                $result = $this->createRequest("SELECT * FROM Sets WHERE idMatch = :idMatch ORDER BY idSet", $param);
                return (!is_array($result)) ? [] : $result; 
            }


            public function getMatch($idMatch) {
                $param = [
                    ':idMatch' => $idMatch
                ];
                $match = $this->createRequest(
                    "SELECT * FROM Matchs WHERE IDMatch = :idMatch", 
                    $param
                );
                // Print match details to the console
                echo "<script>console.log('Match Details: " . json_encode($match) . "');</script>";
                return $match;
            }

            public function getSet($idSet) {
                $param = [
                    ':idSet' => $idSet
                ];
                return $this->createRequest(
                    "SELECT * FROM Sets WHERE IDSet = :idSet", 
                    $param
                );
            }           
            
            public function getEtreSelectionner($numLicence, $idMatch) {
                $param = [
                    ':numLicence' => $numLicence,
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "SELECT * FROM EtreSelectionner WHERE numLicence = :numLicence AND IDMatch = :idMatch", 
                    $param
                );
            }

            public function hasSet($idMatch) {
                $param = [
                    ':idMatch' => $idMatch
                ];
                $result = $this->createRequest(
                    "SELECT COUNT(*) as count FROM Sets WHERE IDMatch = :idMatch", 
                    $param
                );
                return $result['count'] > 0;
            }

            public function getAllMatches() {
                $result = $this->createRequest("SELECT * FROM Matchs", []);
                if (!is_array($result)) {
                    return [];
                }
                return $result;
            }
            
            // INSERT
            public function insertJoueur($id, $nom, $prenom, $dateNaissance, $taille, $poids, $statutJoueur, $commentaire) {
                $param = [
                    ':id' => $id,
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':dateNaissance' => $dateNaissance,
                    ':taille' => $taille,
                    ':poids' => $poids,
                    ':statutJoueur' => $statutJoueur,
                    ':commentaire' => $commentaire
                ];
                
                try {
                    return $this->createRequest(
                        "INSERT INTO Joueurs (numLicence, nom, prenom, dateNaissance, taille, poids, statutJoueur, commentaire)
                         VALUES (:id, :nom, :prenom, :dateNaissance, :taille, :poids, :statutJoueur, :commentaire)",
                        $param
                    );
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                    return false;
                }
            }
            
            public function insertMatch($dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatchON) {
                $param = [
                    ':dateMatch' => $dateMatch,
                    ':nomAdversaires' => $nomAdversaires,
                    ':lieuRencontre' => $lieuRencontre,
                    ':domicileON' => $domicileON,
                    ':avoirGagnerMatchON' => $avoirGagnerMatchON
                ];
                return $this->createRequest(
                    "INSERT INTO Matchs (dateMatch, nomAdversaires, lieuRencontre, domicileON, avoirGagnerMatchON)
                    VALUES (:dateMatch, :nomAdversaires, :lieuRencontre, :domicileON, :avoirGagnerMatchON)",
                    $param
                );
            }
            
            public function insertSet($scoreEquipe, $scoreAdversaire, $tieBreak, $idMatch) {
                $param = [
                    ':scoreEquipe' => $scoreEquipe,
                    ':scoreAdversaire' => $scoreAdversaire,
                    ':tieBreak' => $tieBreak,
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "INSERT INTO Sets (scoreEquipe, scoreAdversaire, tieBreak, IDMatch)
                    VALUES (:scoreEquipe, :scoreAdversaire, :tieBreak, :idMatch)",
                    $param
                );
            }
            
            

            public function insertUtilisateur($login, $motDePasse) {
                $param = [
                    ':login' => $login,
                    ':motDePasse' => $motDePasse
                ];
                return $this->createRequest(
                    "INSERT INTO Utilisateurs (logins, motDePasse) VALUES (:login, :motDePasse)",
                    $param
                );
            }
            
            public function insertEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, $notationJoueur) {
                $param = [
                    ':numLicence' => $numLicence,
                    ':idMatch' => $idMatch,
                    ':titulaireON' => $titulaireON,
                    ':poste' => $poste,
                    ':notationJoueur' => $notationJoueur !== null ? $notationJoueur : null
                ];
            
                return $this->createRequest(
                    "INSERT INTO EtreSelectionner (numLicence, IDMatch, titulaireON, poste, notationJoueur)
                     VALUES (:numLicence, :idMatch, :titulaireON, :poste, :notationJoueur)",
                    $param
                );
            }
            
            public function updateEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, $notationJoueur) {
                $param = [
                    ':numLicence' => $numLicence,
                    ':idMatch' => $idMatch,
                    ':titulaireON' => $titulaireON,
                    ':poste' => $poste,
                    ':notationJoueur' => $notationJoueur !== null ? $notationJoueur : null
                ];
            
                return $this->createRequest(
                    "UPDATE EtreSelectionner 
                     SET titulaireON = :titulaireON, poste = :poste, notationJoueur = :notationJoueur
                     WHERE numLicence = :numLicence AND IDMatch = :idMatch",
                    $param
                );
            }
            
            
            

            // UPDATE
            public function updateJoueur($id, $nom, $prenom, $dateNaissance, $taille, $poids, $statutJoueur, $commentaire) {
                $param = [
                    ':id' => $id,
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':dateNaissance' => $dateNaissance,
                    ':taille' => $taille,
                    ':poids' => $poids,
                    ':statutJoueur' => $statutJoueur,
                    ':commentaire' => $commentaire
                ];
                return $this->createRequest(
                    "UPDATE Joueurs 
                     SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, taille = :taille, poids = :poids, 
                         statutJoueur = :statutJoueur, commentaire = :commentaire
                     WHERE numLicence = :id",
                    $param
                );
            }

            public function updateMatch($idMatch, $dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatchON) {
                $param = [
                    ':idMatch' => $idMatch,
                    ':dateMatch' => $dateMatch,
                    ':nomAdversaires' => $nomAdversaires,
                    ':lieuRencontre' => $lieuRencontre,
                    ':domicileON' => $domicileON,
                    ':avoirGagnerMatchON' => $avoirGagnerMatchON
                ];
                return $this->createRequest(
                    "UPDATE Matchs 
                     SET dateMatch = :dateMatch, nomAdversaires = :nomAdversaires, lieuRencontre = :lieuRencontre, 
                         domicileON = :domicileON, avoirGagnerMatchON = :avoirGagnerMatchON
                     WHERE IDMatch = :idMatch",
                    $param
                );
            }
            
            public function updateSet($idSet, $scoreEquipe, $scoreAdversaire, $tieBreak, $idMatch) {
                $param = [
                    ':idSet' => $idSet,
                    ':scoreEquipe' => $scoreEquipe,
                    ':scoreAdversaire' => $scoreAdversaire,
                    ':tieBreak' => $tieBreak,
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "UPDATE Sets 
                     SET scoreEquipe = :scoreEquipe, scoreAdversaire = :scoreAdversaire, tieBreak = :tieBreak, 
                         IDMatch = :idMatch
                     WHERE IDSet = :idSet",
                    $param
                );
            }

            

            public function updateAvoirGagnerMatchON($idMatch, $avoirGagnerMatchON) {
                $param = [
                    ':idMatch' => $idMatch,
                    ':avoirGagnerMatchON' => $avoirGagnerMatchON
                ];
                return $this->createRequest(
                    "UPDATE Matchs 
                     SET avoirGagnerMatchON = :avoirGagnerMatchON
                     WHERE IDMatch = :idMatch",
                    $param
                );
            }

            // DELETE
            public function deleteJoueur($numLicence) {
                $param = [
                    ':numLicence' => $numLicence
                ];
                return $this->createRequest(
                    "DELETE FROM Joueurs WHERE numLicence = :numLicence", 
                    $param
                );
            }
            
            public function deleteMatch($idMatch) {
                $param = [
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "DELETE FROM Matchs WHERE IDMatch = :idMatch", 
                    $param
                );
            }
            
            public function deleteSet($idSet) {
                $param = [
                    ':idSet' => $idSet
                ];
                return $this->createRequest(
                    "DELETE FROM Sets WHERE IDSet = :idSet", 
                    $param
                );
            }
            
            public function deleteEtreSelectionner($numLicence, $idMatch) {
                $param = [
                    ':numLicence' => $numLicence,
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "DELETE FROM EtreSelectionner WHERE numLicence = :numLicence AND IDMatch = :idMatch", 
                    $param
                );
            }
        }
    ?>
    </body>
</html>
