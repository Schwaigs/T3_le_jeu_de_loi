<?php

require_once '../accesBDD/bddT3.php';
require_once '../accesBDD/MyPDO.php';
require_once '../accesBDD/classesPHP/Heritage.php';

class Personnage {

    public function __construct(){
    }

    public function choixReligion() : string {
        //choisi aléatoirement une religion pour la creation d'un personnage
        //parmis celles disponible dans la bdd selon différentes probabilitées
        $religionAlea;

        $numAlea = rand(1,100);
        /* 75 % de catholiques */
        if ($numAlea < 76){
            $religionAlea = 'catholique';
        }
        /* 20 % de protestant */
        if (($numAlea > 75) && ($numAlea < 96)){
            $religionAlea = 'protestant';
        }
        /* 5 % les autres */
        if ($numAlea > 95){
            $religionAlea = 'autre';
        }

        return $religionAlea;
    }

    public function choixPrenomFemme() : string {
        //choisi aléatoirement un prenom pour la creation d'un personnage
		$prenomAlea;
		$tabPrenom=['Adélaïde','Adeline','Anastasie','Astrid','Aude','Aurore','Athénaïs','Arégonde','Anne','Agnès','Bertille','Blanche','Béatrice','Bérangère',
					'Clothilde','Cécile','Constance','Cunégonde','Cyrielle','Claudine','Désirée','Edith','Elaine','Edwige','Elisabeth','Flore','Frénégonde',
					'Guenièvre','Gwendoline','Galadrielle','Hildegarde','Henriette','Isabelle','Isaure','Jeanne','Jaqueline','Ludivine','Louise','Marie',
					'Mélissandre','Morgane','Mathilde','Mélusine','Marguerite','Ondine','Pétronille','Regine','Rolande','Raymonde','Viviane','Yseult'];
        $numAlea = rand(0,50);
        $prenomAlea = $tabPrenom[$numAlea];
        return $prenomAlea;
    }

	public function choixPrenomHomme() : string {
        //choisi aléatoirement un prenom pour la creation d'un personnage
		$prenomAlea;
		$tabPrenom=['Armand','Auguste','Amaury','Albert','Ambroise','Arnaud','Arthur','Barthélemy','Bertrand','Balthazar','Charles','Clotaire','Clovis','Côme',
					'Cédric','Conrad','Claudes','Dagobert','Eloi','Enguerrand','Eudes','Fernand','Flavien','Florimond','François','Florent','Gaulthier','Gaspard',
					'Gérald','Godefroy','Grégoire','Gilles','Hugues','Henri','Jaques','Jean','Lancelot','Louis','Norbert','Odin','Perceval','Pierrick','Pierre',
					'Philippe','Robin','Robert','Ruffin','Richard','Roland','Raymond','Tanguy','Thibault','Théobald','Tristan','Wilfrid','Ysangrin','Yves'];
        $numAlea = rand(0,56);
        $prenomAlea = $tabPrenom[$numAlea];
        return $prenomAlea;
    }

    public function choixNationnalite() : string {
        //choisi aléatoirement une nationnalite pour la creation d'un personnage
        //parmis celles disponible dans la bdd selon différentes probabilitées
        $nationnaliteAlea;

        $numAlea = rand(1,100);
        /* 95 % de français */
        if ($numAlea < 96){
            $nationnaliteAlea = 'France';
        }
        /* 5 % les autres */
        if ($numAlea > 95){
            $nationnaliteAlea = 'Étranger';
        }
        return $nationnaliteAlea;
    }

    public function chercherOrdreNaissance(int $parent) : int {
        //cherche dans la base tout les frères et soeurs plus âgés qu'un personnage
        //pour connaitre son ordre de naissance
        $result = MyPDO::pdo()->prepare("SELECT id FROM perso WHERE parent = :idParent");
        $idSucces = $result->bindValue(':idParent',$parent, PDO::PARAM_INT);
        $result->execute();
        //le nombre de lignes renvoyées par la requete correspond directement au nb de frères et soeurs plus agés
        $nbfrereEtSoeurs = $result->rowCount();
        return $nbfrereEtSoeurs+1;
    }

    public function choixSexe() : string {
        //choisi aléatoirement le sexe pour la creation d'un personnage
        $numAlea = rand(1,2);
        if($numAlea == 1){
            return 'homme';
        }
        return'femme';
    }

    public function choixEtatSante() : string {
        //choisi aléatoirement un etat de sante pour la creation d'un personnage
        //parmis ceux disponibles dans la bdd selon différentes probabilitées
        $etatSanteAlea;

        $numAlea = rand(1,100);
        /* 75 % de bonne sante */
        if ($numAlea < 76){
            $etatSanteAlea = 'bon';
        }
        /* 20 % moyen */
        if (($numAlea > 75) && ($numAlea < 96)){
            $etatSanteAlea = 'moyen';
        }
        /* 5 % faible */
        if ($numAlea > 95){
            $etatSanteAlea = 'faible';
        }
        return $etatSanteAlea;
    }

    public function choixParent() : int {
        //choisi aléatoirement un parent pour la creation d'un personnage
        //parmis les perso la bdd
        //on récupère les id de chaque parent potentiel
       if ($_SESSION['peutEnfant'] ==1){
            $resultParents = MyPDO::pdo()->prepare("SELECT id FROM perso WHERE age < 50 AND age > 15 AND classe!='mort'");
        }
        else{
            $resultParents = MyPDO::pdo()->prepare("SELECT id FROM perso WHERE age < 50 AND age > 15 AND classe not in ('mort','roi')");
       }
        $resultParents->execute();
        $nbLigne = $resultParents->rowCount();
        //Si pas de parent possible
        if ($nbLigne == 0){
            return 0;
        }

        //on stock tout les id récupérés et on les mets dans un tableau
        $tabIdParents;
        foreach ($resultParents as $row){
            $tabIdParents[] = $row['id'];
        }

        //Puis on tire un nombre aléatoire qui correspond à l'indice de l'id d'un parent
        $numAlea = rand(0,$nbLigne-1);
        $parentAlea = $tabIdParents[$numAlea];

        return $parentAlea;
    }

    public function creerPersonnage() : int {
        /*On met null pour l'id car la base gère l'auto-incrémentation
         age toujours 0 vu qu'il s'agit de naissances
         la classe est nonHeritier car à la naissance il est trop jeune*/
        $result = MyPDO::pdo()->prepare("INSERT INTO perso VALUES(null,:prenom,:religion,:nationnalite,:ordreNaissance,0,:sexe,:etatSante,:parent,'nonHeritier')");

        $religion = $this->choixReligion();
        //echo'religion = '.$religion.'<br>';
        $religionSucces = $result->bindValue(':religion',$religion, PDO::PARAM_STR);

        $nationnalite = $this->choixNationnalite();
        //echo'nationnalite = '.$nationnalite.'<br>';
        $nationnaliteSucces = $result->bindValue(':nationnalite',$nationnalite, PDO::PARAM_STR);

        $sexe = $this->choixSexe();
        //echo'sexe = '.$sexe.'<br>';
        $sexeSucces = $result->bindValue(':sexe',$sexe, PDO::PARAM_STR);

        $prenom;
		if($sexe == 'homme'){
			$prenom = $this->choixPrenomHomme();
		}
		else{
			$prenom = $this->choixPrenomFemme();
		}
		$prenomSucces = $result->bindValue(':prenom',$prenom, PDO::PARAM_STR);
        //echo'prenom = '.$prenom.'<br>';

        $etatSante = $this->choixEtatSante();
        //echo'etatSante = '.$etatSante.'<br>';
        $etatSanteSucces = $result->bindValue(':etatSante',$etatSante, PDO::PARAM_STR);

        $parent = $this->choixParent();
        //s'il n'y a pas de parent possible on n'execute pas la requete et on renvoie que 0 lignes ont été modifiées dans la bdd
        if($parent == 0){
            $_SESSION['message'] = "Aucun membre de votre famille ne peut avoir d'enfant actuellemnt";
            return 0;
        }
        //echo'parent = '.$parent.'<br>';
        $parentSucces = $result->bindValue(':parent',$parent, PDO::PARAM_INT);

        $ordreNaissance = $this->chercherOrdreNaissance($parent);
        //echo'ordreNaissance = '.$ordreNaissance.'<br>';
        $ordreNaissanceSucces = $result->bindValue(':ordreNaissance',$ordreNaissance, PDO::PARAM_INT);

        $result->execute();
        $nbLigne = $result->rowCount();

        //on renvoie le nb de lignes modifiées dans la base
        return $nbLigne;
    }
    

    public function vieillirPerso() : void {
        $result = MyPDO::pdo()->prepare("UPDATE perso SET age = age+3 where classe <> 'mort'");
        $result->execute();
    }


    public function mortPerso() : int {
        $result = MyPDO::pdo()->prepare("SELECT id,age,etatSante From perso where classe not in ('mort','roi')");
        $listePerso = [];
        $probaMort;

        foreach ($result as $row){
            if ($row['age'] <30){
                $probaMort = 5;
            }
            else if ($row['age'] >= 30  && $row['age'] < 60){
                $probaMort = 30;
            }
            else{
                $probaMort = 80;
            }

            if ($row['etatSante'] == 'bon'){
                $probaMort *= 0.8;
            }
            else if ($row['etatSante'] == 'moyen'){
                $probaMort *= 1.2;
            }
            else{
                $probaMort *= 1.5;
            }

            $listePerso[$row['id']] = $probaMort;
        }

        $compteurMort =0;

        foreach ($listePerso as $idPerso => $proba){
            $numAlea = rand(1,100);
            if($proba > $numAlea){
                $resultMort= MyPDO::pdo()->prepare("UPDATE perso SET classe='mort' WHERE id=:id");
                $idSucces = $resultMort->bindValue(':id',$idPerso, PDO::PARAM_INT);
                $resultMort->execute();
                $compteurMort++;
            }
        }


        return $compteurMort;
    }
}
