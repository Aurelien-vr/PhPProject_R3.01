<html>
   
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>
    <?php
        // http://localhost/PhPProject_R3.01/index.php

        // Connection à la bd
        try {
            $linkpdo = new PDO("mysql:host=localhost;dbname=carnetcontact", "root", "");
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        if(false){
            header('Location: /PhPProject_R3.01/acceuil.php');
            exit();
        }
    ?>
    <div class="loginPannel" >
        <h1 id="loginTitle">LOGIN</h1>
        <form action="verification.php" method="POST">
        <div id="loginContainer">
            <input type="text" placeholder="username" name="username" class="fieldLogin" id="userNameId" required>
            <input type="password" placeholder="password" name="password" class="fieldLogin" required>
            <input type="submit" id='submit' value='LOGIN' class="fieldLogin" >
    </div>
       </div>        
        </form>
    </body>
</html>