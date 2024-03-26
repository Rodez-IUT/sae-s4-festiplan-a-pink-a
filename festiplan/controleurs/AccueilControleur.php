<?php

namespace controleurs;

use PDO;
use yasmf\View;
use modeles\SpectacleModele;
use modeles\FestivalModele;


class AccueilControleur {


    public function __construct(SpectacleModele $spectacleModele, FestivalModele $festivalModele) {
        $this->spectacleModele = $spectacleModele;
        $this->festivalModele = $festivalModele;
    }

    public function index(PDO $pdo) : View {
        $afficher = false;
        $idUtilisateur = $_SESSION['id_utilisateur'];
        // On détermine sur quelle page on se trouve
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $pageActuelle = (int) strip_tags($_GET['page']);
        }else{
            $pageActuelle = 1;
        }

        $nbFestival = (int)$this->festivalModele->nombreMesFestivals($pdo,$idUtilisateur);
        // On calcule le nombre de pages total
        $nbPages = ceil($nbFestival / 4);
        // Calcul du 1er element de la page
        $premier = ($pageActuelle * 4) - 4;
        $mesFestivals = $this->festivalModele->listeMesFestivals($pdo,$idUtilisateur,$premier);
        // Recupere le responsable de chaque Festival
        $lesResponsables = $this->festivalModele->listeLesResponsables($pdo);

        $vue = new View("vues/vue_accueil");
        $vue->setVar("afficherSpectacle", false);
        $vue->setVar("nbPages", $nbPages);
        $vue->setVar("mesFestivals", $mesFestivals);
        $vue->setVar("lesResponsables", $lesResponsables);
        return $vue;
    }

    public function voirFestival(PDO $pdo) {
        session_start();
        $afficher = false;
        $idUtilisateur = $_SESSION['id_utilisateur'];
        // On détermine sur quelle page on se trouve
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $pageActuelle = (int) strip_tags($_GET['page']);
        }else{
            $pageActuelle = 1;
        }
        $nbFestival = (int)$this->festivalModele->nombreMesFestivals($pdo,$idUtilisateur);
        // On calcule le nombre de pages total
        $nbPages = ceil($nbFestival / 4);
        // Calcul du 1er article de la page
        $premier = ($pageActuelle * 4) - 4;
        $mesFestivals = $this->festivalModele->listeMesFestivals($pdo,$idUtilisateur,$premier);
        // Recupere le responsable de chaque Festival
        $lesResponsables = $this->festivalModele->listeLesResponsables($pdo);

        $vue = new View("vues/vue_accueil");
        $vue->setVar("afficherSpectacles", false);
        $vue->setVar("nbPages", $nbPages);
        $vue->setVar("mesFestivals", $mesFestivals);
        $vue->setVar("lesResponsables", $lesResponsables);
        return $vue;
    }   

    public function voirSpectacle(PDO $pdo) {
        session_start();
        $afficher = true;
        $idUtilisateur = $_SESSION['id_utilisateur'];
        // On détermine sur quelle page on se trouve
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $pageActuelle = (int) strip_tags($_GET['page']);
        }else{
            $pageActuelle = 1;
        }
        $nbSpectacle = (int)$this->spectacleModele->nombreMesSpectacles($pdo,$idUtilisateur);
        
        // On calcule le nombre de pages total
        $nbPagesSpectacle = ceil($nbSpectacle / 4);
        // Calcul du 1er element de la page
        $premier = ($pageActuelle * 4) - 4;
        $mesSpectacles = $this->spectacleModele->listeMesSpectacles($pdo,$idUtilisateur,$premier);

        $vue = new View("vues/vue_accueil");
        $vue->setVar("afficherSpectacles", true);
        $vue->setVar("mesSpectacles", $mesSpectacles);
        $vue->setVar("nbPages", $nbPagesSpectacle);
        $vue->setVar("afficherSpectacles",$afficher);
        return $vue;
    }
}   