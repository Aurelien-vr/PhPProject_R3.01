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
                    
                    // Bind des variables
                    foreach ($param as $cle => $valeur) {
                        $stmt->bindParam($cle, $valeur, PDO::PARAM_STR);
                    }
                    $stmt->execute();
            
                    // Si c'est un SELECT, retourner les résultats
                    if (stripos($request, 'SELECT') === 0) {
                        return $stmt->fetch(PDO::FETCH_ASSOC);
                    }
            
                    // Pour INSERT, UPDATE, DELETE, retourner true si succès
                    return true;
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                }
                return null;
            }
            
            // SELECT
            public function getPWD($log) {
                $param = [
                    ':login' => $log
                ];
                return $this->createRequest("SELECT motDePasse FROM Utilisateurs WHERE logins = :login", $param);
            }

            public function getJoueurs() {
                return $this->createRequest(
                    "SELECT * FROM Joueurs ORDER BY nom, prenom", 
                    null
                );
            }

            public function getMatch($idMatch) {
                $param = [
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "SELECT * FROM Matchs WHERE IDMatch = :idMatch", 
                    $param
                );
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
                return $this->createRequest(
                    "INSERT INTO Joueurs (numLicence, nom, prenom, dateNaissance, taille, poids, statutJoueur, commentaire)
                    VALUES (:id, :nom, :prenom, :dateNaissance, :taille, :poids, :statutJoueur, :commentaire)",
                    $param
                );
            }
            
            public function insertMatch($idMatch, $dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatch) {
                $param = [
                    ':idMatch' => $idMatch,
                    ':dateMatch' => $dateMatch,
                    ':nomAdversaires' => $nomAdversaires,
                    ':lieuRencontre' => $lieuRencontre,
                    ':domicileON' => $domicileON,
                    ':avoirGagnerMatch' => $avoirGagnerMatch
                ];
                return $this->createRequest(
                    "INSERT INTO Matchs (IDMatch, dateMatch, nomAdversaires, lieuRencontre, domicileON, avoirGagnerMatch)
                    VALUES (:idMatch, :dateMatch, :nomAdversaires, :lieuRencontre, :domicileON, :avoirGagnerMatch)",
                    $param
                );
            }
            
            public function insertSet($idSet, $scoreEquipe, $scoreAdversaire, $tieBreak, $idMatch) {
                $param = [
                    ':idSet' => $idSet,
                    ':scoreEquipe' => $scoreEquipe,
                    ':scoreAdversaire' => $scoreAdversaire,
                    ':tieBreak' => $tieBreak,
                    ':idMatch' => $idMatch
                ];
                return $this->createRequest(
                    "INSERT INTO Sets (IDSet, scoreEquipe, scoreAdversaire, tieBreak, IDMatch)
                    VALUES (:idSet, :scoreEquipe, :scoreAdversaire, :tieBreak, :idMatch)",
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

            public function updateMatch($idMatch, $dateMatch, $nomAdversaires, $lieuRencontre, $domicileON, $avoirGagnerMatch) {
                $param = [
                    ':idMatch' => $idMatch,
                    ':dateMatch' => $dateMatch,
                    ':nomAdversaires' => $nomAdversaires,
                    ':lieuRencontre' => $lieuRencontre,
                    ':domicileON' => $domicileON,
                    ':avoirGagnerMatch' => $avoirGagnerMatch
                ];
                return $this->createRequest(
                    "UPDATE Matchs 
                     SET dateMatch = :dateMatch, nomAdversaires = :nomAdversaires, lieuRencontre = :lieuRencontre, 
                         domicileON = :domicileON, avoirGagnerMatch = :avoirGagnerMatch
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
