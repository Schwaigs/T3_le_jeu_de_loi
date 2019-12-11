<?php

/*
* \file Lancement
* \par Permet de gèrer la recherche des héritiers.
 */

  //Démarrer la session
  session_start();
  require_once '../accesBDD/initBase.php';


  //Mise en place de la section, on débute à 1 et on va jusqu'à 5 sections (=1 cycle)
  //Puis on recommence
  if (!isset($_SESSION['section'])){
      /*
      * \var section est une variable de session qui correspond tour de jeu auquel on est dans un cycle de 5 tours.
      */
      $_SESSION['section'] = 1;
  }

  //Si on passe la 5ème section, on recommence un nouveau cycle de 5 sections
  if ($_SESSION['section'] > 5){
      $_SESSION['section'] = 1;
      $_SESSION['cycleFait'] ++;
      //Si le roi a atteint son nbmax de cycle on passe à un autre roi
      if($_SESSION['cycleFait'] >= $_SESSION['cycleRoi']){
          $heritage = new Heritage();
          try{
              $idNouveauRoi = $heritage->choisiRoi();
          }
          catch( PDOException $e ) {
              echo 'Erreur : '.$e->getMessage();
              exit;
          }
          $_SESSION['message'] = "Il est temps pour vous de passer la main, l'un de vos héritiers récupère le trône";
      }
  }

  //On initialise le nombre de lois que l'on peut voter à 2 par cycle de 5 sections,
  //si on est au début d'une section on a le droit à 2 votes
  if (!isset($_SESSION['nbLois']) || $_SESSION['section'] == 1){
    /*
    * \var nbLois est une variable de session qui correspond aux nombre de lois que l'on peut voter dans le cycle actuel.
    */
      $_SESSION['nbLois'] = 2;
  }

  if (!isset($_SESSION['cycleFait'])){
    /*
    * \var cycleFait est une variable de session qui correspond aux nombre de cycles de jeu éffectués par le roi actuel.
    */
    $_SESSION['cycleFait'] = 0;
  }

  if (!isset($_SESSION['cycleRoi'])){
    /*
    * \var cycleRoi est une variable de session qui correspond aux nombre de cycles de jeu que peut faire le roi actuel en fonction de son âge d'arrivée sur le trône.
    */
    $_SESSION['cycleRoi'] = 2;
  }

  if (!isset($_SESSION['suivant'])){
      /*
      * \var suivant est une variable de session qui permet de savoir si le joueur a déjà choisit entre un événement ou un vote.
      */
      $_SESSION['suivant'] = true;
  }

  if (!isset($_SESSION['texteEvent'])){
      /*
      * \var texteEvent est une variable de session qui permet d'afficher le texte liée à un événement.
      */
      $_SESSION['texteEvent'] = "Bienvenue";
  }

  if (!isset($_SESSION['annee'])){
      /*
      * \var année est une variable de session qui contient l'année courante dans le jeu.
      */
      $_SESSION['annee'] = 1763;
  }

  if (!isset($_SESSION['idCarac'])){
    /*
    * \var idCarac est une variable de session qui contient l'id du personnage dont on doit afficher les caractéristiques.
    */
    $_SESSION['idCarac'] = -1;
  }

  //Récupération de l'id en variable de session
  if (isset($_GET['id'])){
    $_SESSION['idCarac'] = $_GET['id'];
  }

  if (!isset($_SESSION['jeu'])){
    /*
    * \var jeu est une variable de session qui permet de savoir si le joueur à perdu, gagné ou bien joue encore.
    */
    $_SESSION['jeu'] = 'en cours';
  }

  if (!isset($_SESSION['messageFin'])){
    /*
    * \var messageFin est une variable de session qui contient le message à afficher quand le joueur perd ou gagne.
    */
    $_SESSION['messageFin'] = '';
  }

  if (!isset($_SESSION['message'])){
    /*
    * \var message est une variable de session qui permet d'afficher au joueurs différentes informations au cours de la partie.
    */
    $_SESSION['message'] = "";
  }

  if (!isset($_SESSION['noblesse'])){
    /*
    * \var noblesse est une variable de session qui évalue la relation entre le joueur et la noblesse.
    */
    $_SESSION['noblesse'] = 50;
  }

  if (!isset($_SESSION['clerge'])){
    /*
    * \var clerge est une variable de session qui évalue la relation entre le joueur et le clergé.
    */
    $_SESSION['clerge'] = 50;
  }

  if (!isset($_SESSION['tiersEtat'])){
    /*
    * \var tiersEtat est une variable de session qui évalue la relation entre le joueur et le tiers-état.
    */
    $_SESSION['tiersEtat'] = 50;
  }

  require_once '../accesBDD/classesPHP/Arbre.php';
  require_once '../accesBDD/chercheCaracPerso.php';

  ?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8"
    name="viewport"
    content="width=device-width, initial-scale=1">
    <title> Jeu de Lois </title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/arbreGenealogique.css">
    <!-- Permet d'utiliser une police d'écriture au style moyenâgeux -->
    <link href="https://fonts.googleapis.com/css?family=Almendra&display=swap" rel="stylesheet">
  </head>

  <body>

    <header>
      <div class="flex-container">
        <div id="annee" style="flex-grow: 1"> <h1><?php echo $_SESSION['annee'] ?></h1> </div>
        <!-- Si on clique sur le titre on recommence une nouvelle partie -->
        <div id="titreJeu" style="flex-grow: 8"><a href="../pagesPHP/quitter.php">Jeu de lois</a></div>
        <!-- Si on clique sur le livre on arrive sur la page d'encyclopédie qui éxplique certains termes -->
        <div id="encyclo" style="flex-grow: 1">
          <a href="../pagesPHP/encyclopedie.php" onclick="window.open(this.href); return false;"><img id="imgEncyclo" src="../images/encyclopedie.png"></a>
        </div>
      </div>
    </header>

    <main>
      <div class="main">
        <!-- Zone de l'arbre généalogique -->
        <div id="bandeauArbre">
          <h1>Arbre généalogique</h1>
          <!-- Création du bandeau dépliable  -->
          <div id="arbreDepl" class="overlayArbre">

           <!-- Bouton pour fermer/replier le bandeau -->
           <a href="javascript:void(0)" class="btnFermer" onclick="fermeArbre()">&times;</a>

           <!-- Contenu de l'overlay -->
           <div class="tree">
             <!-- Remplissage par la page de l'abre généalogique créé à part -->
             <?php
                 include '../pagesPHP/arbreGenealogique.php';
             ?>
           </div>
          </div>

          <!-- Bouton pour ouvrir/déplier le bandeau -->
          <br>
          <span onclick="ouvreArbre()">Afficher >></span>
          <script src="../js/index.js"></script>
        </div>
        <!-- Zone principale de jeu -->
        <div class="container" id="event">

          <div class="jauges">
            <div class="columnDiffJauges" id="jaugeUne">
              <p> Noblesse </p>
              <!-- Le height gère la hauteur globale de la jauge -->
              <div class="bar-container" style="height: 10rem;">
                <div class="goal-bar">
                  <div class="bar-wrap">
                    <!-- On gère la taille de la jauge via le translateY-->
                    <div class="bar" style="transform: translateY(<?php echo 100-$_SESSION['noblesse'] ?>%);">
                      <div class="bar-info">
                        <div class="bar-info-inner">
                          <!-- Valeur brute qui servira au debuggage (sera affiché la valeur de la varaible)-->
                          <?php echo $_SESSION['noblesse'] ?> / 100
                        </div>
                      </div>
                    </div>
                  </div>
                </div> <!-- /.goal-bar -->
              </div>
            </div>

            <div class="columnDiffJauges" id="jaugeDeux">
              <p> Clergé </p>
              <!-- Le height gère la hauteur globale de la jauge -->
              <div class="bar-container" style="height: 10rem;">
                <div class="goal-bar">
                  <div class="bar-wrap">
                    <!-- On gère la taille de la jauge via le translateY-->
                    <div class="bar" style="transform: translateY(<?php echo 100-$_SESSION['clerge'] ?>%);">
                      <div class="bar-info">
                        <div class="bar-info-inner">
                          <!-- Valeur brute qui servira au debuggage (sera affiché la valeur de la varaible)-->
                          <?php echo $_SESSION['clerge'] ?> / 100
                        </div>
                      </div>
                    </div>
                  </div>
                </div> <!-- /.goal-bar -->
              </div>
            </div>

            <div class="columnDiffJauges" id="jaugeTrois">
              <p> Tiers état </p>
              <!-- Le height gère la hauteur globale de la jauge -->
              <div class="bar-container" style="height: 10rem;">
                <div class="goal-bar">
                  <div class="bar-wrap">
                    <!-- On gère la taille de la jauge via le translateY-->
                    <div class="bar" style="transform: translateY(<?php echo 100-$_SESSION['tiersEtat'] ?>%);">
                      <div class="bar-info">
                        <div class="bar-info-inner">
                          <!-- Valeur brute qui servira au debuggage (sera affiché la valeur de la varaible)-->
                          <?php echo $_SESSION['tiersEtat'] ?> / 100
                        </div>
                      </div>
                    </div>
                  </div>
                </div> <!-- /.goal-bar -->
              </div>
            </div>
          </div>

          <div class="evenements">
            <div class="flex-event-header">
              <div style="flex-grow: 10">
                <h1>Les aléas de la vie</h1>
              </div>

            </div>
            <div class="contenuEvent">
              <!-- Remplissage par la page des event et de vote créé à part -->
                <?php
                include '../pagesPHP/choixEventLoi.php';
                ?>
            </div>
          </div>
        </div>

        <div class="column">
          <!-- Zone des lois en place -->
          <div id="lois">
            <h2>Lois promulguées:</h2>
              <!-- Remplissage par la page d'affichage des lois créé à part -->
              <?php
              include '../pagesPHP/afficheLois.php'
              ?>
          </div>
          <!-- Zone des caractéristiques des personnages -->
          <div id="carac">
            <div id="affichageImageEtTexte">
                <!-- Remplissage par la page d'affichage des caractéristiques créé à part -->
                <?php
                    include '../pagesPHP/infoCarac.php';
                ?>

            </div>
          </div>
        </div>
      </div>
    </main>

  </body>
</html>
