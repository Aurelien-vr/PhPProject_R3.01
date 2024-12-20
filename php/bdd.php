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
            
            public function getPWD($log) {
                $param = [
                    ':login' => $log
                ];
                return $this->createRequest("SELECT motDePasse FROM Utilisateurs WHERE logins = :login", $param);
            }

            public function getJoueurs() {
                return $this->createRequest("SELECT nom, prenom, statutJoueur, commentaire FROM Joueurs ORDER BY nom, prenom", null);
            }

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
            
        }
    ?>
    </body>
</html>
