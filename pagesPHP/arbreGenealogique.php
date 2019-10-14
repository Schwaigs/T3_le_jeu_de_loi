<?php
require_once '../accesBDD/classesPHP/Arbre.php';
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/arbreGenealogique.css">
        <title>Page arbre généalogique</title>
    </head>
    <body>

    <?php
        $arbre = new Arbre();
        try{
            $arbre->initArbre();
        }
        catch( PDOException $e ) {
            echo 'Erreur : '.$e->getMessage();
            exit;
        }
    ?>

    </body>
</html>