<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Vollet Manager</title>
    </head>
    <body>
    <?php
        class BDD{
            
            private $host = 'mysql-malekaurel.alwaysdata.net';
            private $dbname = 'malekaurel_bdd';
            private $username = '386527';
            private $password = '9g^aYMeUs#yQKU';
            private $pdo;
            private $error;

            public function __construct(){
                try {
                    // Connexion à la base de données
                    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    $error=$e->getMessage();
                } finally {
                    $error='success';
                }
            }

            public function getError(){
                return $error;
            }

            public function getPWD($log){
                try {
                    $stmt = $pdo->prepare("SELECT motDePasse FROM Utilisateurs WHERE logins = :login");
                    $stmt->bindParam(':login', $log, PDO::PARAM_STR);
                    $stmt->execute();

                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result;
                } catch (PDOException $e) {
                    $error=$e->getMessage();
                } finally {
                    $error='success';
                }
            }

        }

    ?>
    </body>
</html>