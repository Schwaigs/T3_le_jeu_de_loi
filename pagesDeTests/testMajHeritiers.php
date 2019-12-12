<?php
session_start();
require_once '../accesBDD/classesPHP/Heritage.php';
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="arbreGenealogique.css">
        <title>Page test maj heritage</title>
    </head>
    <body>

    <?php
        $heritage = new Heritage();
        try{
            $heritage->majHeritiers();

        }
        catch( PDOException $e ) {
            echo 'Erreur : '.$e->getMessage();
            exit;
        }
        header('Location: ../pageDeLancement/lancement.php');
        exit();
    ?>

    </body>
</html>
