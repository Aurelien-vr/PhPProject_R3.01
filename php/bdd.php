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
            

            public function getAVGNotationJoueur($idJoueur){
                $result = $this->createRequest(
                    "SELECT AVG(notationJoueur) as avgNotation FROM EtreSelectionner WHERE idJoueur = :idJoueur",
                    [':idJoueur' => $idJoueur]
                );

                return $result ? $result[0]['avgNotation'] : null; // Assure un accès correct à la valeur
            }


            public function getPosteFavJoueur($idJoueur){
                $result = $this->createRequest(
                    "SELECT poste, COUNT(poste) as countPoste
                    FROM EtreSelectionner
                    WHERE idJoueur = :idJoueur
                    GROUP BY poste
                    ORDER BY countPoste DESC
                    LIMIT 1",
                    [':idJoueur' => $idJoueur]
                );

                return $result ? $result[0]['poste'] : null; // Retourne le poste favori ou null s'il n'y a pas de résultat
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
            
            public function insertEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, $notationJoueur, $nbRemplacements) {
                $param = [
                    ':numLicence' => $numLicence,
                    ':idMatch' => $idMatch,
                    ':titulaireON' => $titulaireON,
                    ':poste' => $poste,
                    ':notationJoueur' => $notationJoueur,
                    ':nbRemplacements' => $nbRemplacements
                ];
                return $this->createRequest(
                    "INSERT INTO EtreSelectionner (numLicence, IDMatch, titulaireON, poste, notationJoueur, nbRemplacements)
                    VALUES (:numLicence, :idMatch, :titulaireON, :poste, :notationJoueur, :nbRemplacements)",
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
s                         domicileON = :domicileON, avoirGagnerMatchON = :avoirGagnerMatchON
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
            
            public function updateEtreSelectionner($numLicence, $idMatch, $titulaireON, $poste, $notationJoueur, $nbRemplacements) {
                $param = [
                    ':numLicence' => $numLicence,
                    ':idMatch' => $idMatch,
                    ':titulaireON' => $titulaireON,
                    ':poste' => $poste,
                    ':notationJoueur' => $notationJoueur,
                    ':nbRemplacements' => $nbRemplacements
                ];
                return $this->createRequest(
                    "UPDATE EtreSelectionner 
                     SET titulaireON = :titulaireON, poste = :poste, notationJoueur = :notationJoueur, nbRemplacements = :nbRemplacements
                     WHERE numLicence = :numLicence AND IDMatch = :idMatch",
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
