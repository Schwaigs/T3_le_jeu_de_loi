<?php

require_once '../bddT3.php';
require_once '../MyPDO.php';

class Arbre {

    public function __construct(){
    }

    public function triParFratrie(array $tabPersoParents) : array{
        //renvoie un tableau de tableau, organisé par parent, chaque sous tableau etant la liste de frères et soeurs qui son les enfant du parent
        //on l'initialise avec la racine 1
        $tableParentEnfants[1][] = 1;
        foreach ($tabPersoParents as $enfant => $parent){
            //si on pas de case ayant pour clé $parent dans le tableau $tableParentEnfants alors on en créer une         
            if (!(array_key_exists($parent,$tableParentEnfants))){
                //on creer pour le parent une liste de ses enfants contenant à la premiere case l'id du parent
                $tableParentEnfants[$parent][] = $parent;
            }
            //on ajoute l'enfantà sa liste de freres et soeurs
            $tableParentEnfants[$parent][] = $enfant;

            /*print_r($tableParentEnfants);
            echo '<br>';*/
        }
        //enfin on renvoie notre tableau contenant les fratries par parent
        return $tableParentEnfants;
    }

    public function remplissageArbre(int $idParent, array $tableParentEnfants) : void {
        //on regarde si le parent à des enfants
        if (array_key_exists($idParent,$tableParentEnfants)){
            //si oui on créer une liste html pour y mettre ses enfants
            echo '<ul>';
            //puis pour chacun de ses enfant on créer un élément de liste avec son id
            $i = 1;
            while (isset($tableParentEnfants[$idParent][$i])){
                echo'<li>';
                echo'<a href="#">'.$tableParentEnfants[$idParent][$i].'</a>';
                //et on fait la même chose avec ses propres enfants
                $this->remplissageArbre($tableParentEnfants[$idParent][$i],$tableParentEnfants);
                echo'</li>';
                $i++;
            }
            echo'</ul>';
        }
    }

    public function initArbre() : void {
        //on prends touts les perso de notre base personnage
        $resultBase = MyPDO::pdo()->prepare("SELECT id,parent FROM personnage");
        $resultBase->execute();
        //on creer un tableau contenant tout les id de notre base
        $tabId;
        //on creer un tableau contenant en clé l'id d'un personnage et en valeur celui de son parent
        $ListePersoParent;
        foreach ($resultBase as $row){
            if($row['id']!=1){
                $ListePersoParent[$row['id']] = $row['parent'];
                $tabId[] = $row['id'];
            }
        }

        //on tris nos perso selon leur fratrie et leur parents
        $tabParentEnfant = $this->triParFratrie($ListePersoParent);
        //ici débute la création de l'arbre
        echo '<div class="tree"> <ul> <li>';

        //on creer d'abord le personnage racine
        $persoRacine = 1;
        echo '<a href="#">'.$persoRacine.'</a>';

        //la suite de l'arbre est créer de maniere recursive
        $this->remplissageArbre($persoRacine,$tabParentEnfant);

        //on referme l'arbre
        echo '</li>  </ul>  </div>';
    }

}
