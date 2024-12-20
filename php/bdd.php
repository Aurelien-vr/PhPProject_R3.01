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
                    $this->error = $e->getMessage();
                }
            }

            public function getError() {
                return $this->error;
            }

            public function selectRequest($request, $param){
                $this->error = '';
                try {
                    $stmt = $this->pdo->prepare($request);
                    
                    // bind les variables
                    foreach ($param as $cle => $valeur) {
                        $stmt->bindParam($cle, $valeur, PDO::PARAM_STR);
                    }
                    
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result;
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                }
                return null;
            }

            public function getPWD($log) {
                $param = [
                    ':login' => $log
                ];
                return $this->selectRequest("SELECT motDePasse FROM Utilisateurs WHERE logins = :login", $param);
            }

            public function getJoueurs() {
                return $this->selectRequest("SELECT nom, prenom, statutJoueur, commentaire FROM Joueurs ORDER BY nom, prenom", null);
            }
        }
    ?>
    </body>
</html>
