<?php

namespace controleurs;

use PDO;
use yasmf\View;
use yasmf\HttpHelper;
use modeles\SpectacleModele;
use modeles\FestivalModele;

class SpectacleControleur {

    private SpectacleModele $spectacleModele;

    public function __construct(SpectacleModele $spectacleModele) {
        $this->spectacleModele = $spectacleModele;
    }
    
    public function index(PDO $pdo): View {
        session_start();
        // Met tout les champs en rouge lorsqu'on arrive sur la page
        $verifTitre = false;
        $verifDesc = false;
        $verifDuree = false;
        $verifCategorie = false;
        $verifTaille = false;
        // Recherche les différentes catégories
        $searchStmt = $this->spectacleModele->listeCategorieSpectacle($pdo);
        $search_stmt = $this->spectacleModele->listeTailleScene($pdo);
        $vue = new View("vues/vue_creer_spectacle");
        $vue->setVar("titreOk", $verifTitre);
        $vue->setVar("descOk", $verifDesc);
        $vue->setVar("dureeOk", $verifDuree);
        $vue->setVar("categorieOk", $verifCategorie);
        $vue->setVar("tailleOk",$verifTaille);
        $vue->setVar("ancienneCategorie", " ");
        $vue->setVar("ancienneTaille", " ");
        $vue->setVar('searchStmt',$searchStmt);
        $vue->setVar('search_stmt',$search_stmt);
        return $vue;
    }

    public function nouveauSpectacle(PDO $pdo) : View
    {   
        session_start();
        //Récupère tous les paramètres d'un spectacle
        $titre = HttpHelper::getParam('titre');
        $description = HttpHelper::getParam('description');
        $duree = HttpHelper::getParam('duree');
        $categorie = HttpHelper::getParam('categorie');
        $taille = HttpHelper::getParam('taille');
        $illustration = 'aaa';

        // Récupere true si on modifier un spectalce, false si on en créer un
        $modifier = HttpHelper::getParam('modifier');

        $verifTitre = false;
        $verifDesc = false;
        $verifDuree = false;
        $verifTaille = false;
        $verifCategorie = false;
        // Verifie que le titre du spectacle fasse moins de 36 carac et sois différent de vide
        if (strlen($titre) <= 35 && trim($titre) != "") {
            $verifTitre = true;
        }
        // Verifie que la description du spectacle fasse moins de 1001 carac et sois différent de vide
        if (strlen($description) <= 1000 && trim($description) != "") {
            $verifDesc = true;
        }
        // Verifie que la durée du spectacle sois supérieure a 00:00 est inférieure a 24h
        if ($duree != '00:00') {
            $verifDuree = true;
        }

        if ($taille != 0) {
            $verifTaille = true;
        }

        if ($categorie != 0) {
            $verifCategorie = true;
        }

         // Si toutes les valeurs sont correctes ajoute le spectacle a la base de données
        if ($verifDuree && $verifDesc && $verifTitre && $verifTaille && $verifCategorie) {
            
            // Recupere l'id de l'utilisateur
            $idUtilisateur = $_SESSION['id_utilisateur'];
            // Insere ce spectacle dans la base de données ou le modifie selon la valeur de $modifier
            if ($modifier == 'true') {
                $idSpectacle = HttpHelper::getParam('idSpectacle');
                if ($this -> spectacleModele -> verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)) {
                    $modif = $this->spectacleModele->modifspectacle($pdo, $titre, $description, $duree, $illustration, $categorie, $taille, $idSpectacle);
                }
            } else {
                $search = $this->spectacleModele->insertionspectacle($pdo, $titre, $description, $duree, $illustration, $categorie, $taille, $idUtilisateur);
            }

            //Renvoie à l'accueil car quand il va à la pgae connexion il est déjà connecté
            $vue = new View("vues/vue_connexion");
            return $vue;
        } else {
            // Si des valeurs sont incorectes renvoie lesquels le sont et les valeurs
            $searchStmt = $this->spectacleModele->listeCategorieSpectacle($pdo);
            $search_stmt = $this->spectacleModele->listeTailleScene($pdo);
            if ($modifier == 'true') {
                $vue = new View("vues/vue_modifier_spectacle");
            } else {
                $vue = new View("vues/vue_creer_spectacle");
            }
            $vue->setVar("titreOk", $verifTitre);
            $vue->setVar("ancienTitre", $titre);
            $vue->setVar("descOk", $verifDesc);
            $vue->setVar("ancienneDesc", $description);
            $vue->setVar("dureeOk", $verifDuree);
            $vue->setVar("ancienneDuree", $duree);
            $vue->setVar("categorieOk", $verifCategorie);
            $vue->setVar("tailleOk",$verifTaille);
            $vue->setVar("ancienneCategorie", $categorie);
            $vue->setVar("ancienneTaille", $taille);
            $vue->setVar('searchStmt',$searchStmt);
            $vue->setVar('search_stmt',$search_stmt);
            return $vue;
        }
    }

    public function afficherSpectacle(PDO $pdo) : View {
        session_start();

        $idOrganisateur = $_SESSION['id_utilisateur'];
        $idSpectacle = HttpHelper::getParam('idSpectacle');

        // Recupere les données du spectacle séléctionné
        $spectacleAModifier = $this->spectacleModele->leSpectacle($pdo,$idSpectacle);
        // Recupere les données de la liste des catégorie
        $searchStmt = $this->spectacleModele->listeCategorieSpectacle($pdo);
        // Recupere les données de la liste des tailles de scènes
        $search_stmt = $this->spectacleModele->listeTailleScene($pdo);
        // Mets les données dans la vue
        $vue = new View("vues/vue_modifier_spectacle");
        $vue->setVar("titreOk", true);
        $vue->setVar("ancienTitre", $spectacleAModifier['titre']);
        $vue->setVar("descOk", true);
        $vue->setVar("ancienneDesc", $spectacleAModifier['description']);
        $vue->setVar("dureeOk",true);
        $vue->setVar("categorieOk", true);
        $vue->setVar("tailleOk", true);
        $vue->setVar("ancienneDuree", $spectacleAModifier['duree']);
        $vue->setVar("ancienneCategorie", $spectacleAModifier['categorie']);
        $vue->setVar("ancienneTaille", $spectacleAModifier['tailleSceneRequise']);
        $vue->setVar("idSpectacle", $idSpectacle);

        $vue->setVar("searchStmt",$searchStmt);
        $vue->setVar("search_stmt",$search_stmt);
        return $vue;
    }

    public function supprimerSpectacle(PDO $pdo) : View {
        session_start();
        $idSpectacle = HttpHelper::getParam('idSpectacle');
        $idUtilisateur = $_SESSION['id_utilisateur'];

        if ($this -> spectacleModele -> verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)) {
            // Supprime le festival de la base de données
            $supprimerSpectacle = $this->spectacleModele->supprimerSpectacle($pdo, $idSpectacle);
        } else {
            header("Location: ?controller=Home");
            exit();
        }
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
        return $vue;
    }

    public function ajouterIntervenant(PDO $pdo) : View|null
    {
        $existeIntervenant = false;
        $idSpectacle = HttpHelper::getParam('idSpectacle');
        $idUtilisateur = $_SESSION['id_utilisateur'];
        // Recupere les données de la liste des métiers des intervenants
        if ($this -> spectacleModele -> verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)) {
            $searchStmt = $this->spectacleModele->listeMetiersIntervenants($pdo);
            $vue = new View("vues/vue_ajouter_intervenant");
            $vue->setVar("searchStmt", $searchStmt);
            $vue->setVar("idSpectacle", $idSpectacle);
            $vue->setVar("existePas", $existeIntervenant);
            return $vue;
        } else {
            header("Location: ?controller=Home");
            exit();
        }
    }
    
    public function modifierIntervenant(PDO $pdo) : View {
        $idIntervenant = HttpHelper::getParam('idIntervenant');
        $idSpectacle = HttpHelper::getParam('idSpectacle');
        $idUtilisateur = $_SESSION['id_utilisateur'];

        if ($this -> spectacleModele -> verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)) {
            $intervenantAModifier = $this->spectacleModele->intervenant($pdo, $idIntervenant);
            // Recupere les données de la liste des métiers des intervenants
            $searchStmt = $this->spectacleModele->listeMetiersIntervenants($pdo);
            $vue = new View("vues/vue_modifier_intervenant");
            $vue->setVar("existePas", false);
            $vue->setVar("searchStmt", $searchStmt);
            $vue->setVar("nom", $intervenantAModifier['nom']);
            $vue->setVar("prenom", $intervenantAModifier['prenom']);
            $vue->setVar("mail", $intervenantAModifier['mail']);
            $vue->setVar("ancienMetier", $intervenantAModifier['typeIntervenant']);
            $vue->setVar("ancienSurScene", $intervenantAModifier['surScene']);
            $vue->setVar("idIntervenant", $idIntervenant);
            $vue->setVar("idSpectacle", $idSpectacle);
            return $vue;
        }
        header("Location: ?controller=Home");
        exit();
    }


    public function nouveauIntervenant(PDO $pdo) : View {
        //Récupère tous les paramètres d'un intervenant
        $nom = HttpHelper::getParam('nom');
        $prenom = HttpHelper::getParam('prenom');
        $mail = HttpHelper::getParam('email');
        $surScene = HttpHelper::getParam('categorieIntervenant');
        $typeIntervenant = HttpHelper::getParam('metierIntervenant');

        $idSpectacle = HttpHelper::getParam('idSpectacle');
        $idIntervenant = HttpHelper::getParam('idIntervenant');
        $idUtilisateur = $_SESSION['id_utilisateur'];

        // Récupere true si on modifier un intervenant
        $modifier = HttpHelper::getParam('modifier');
        if ($this->spectacleModele->verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)){
            if ($modifier == 'true') {
                $existeIntervenant = $this->spectacleModele->existeIntervenant($pdo, $idSpectacle, $nom, $prenom, $mail,
                    $surScene, $typeIntervenant);
                if ($existeIntervenant) {
                    $this->spectacleModele->modifIntervenant($pdo, $nom, $prenom, $mail, $surScene, $typeIntervenant,
                        $idIntervenant);
                    // Mets les données dans la vue
                    $search_stmt = $this->spectacleModele->infoIntervenant($pdo, $idSpectacle);
                    $vue = new View("vues/vue_intervenant");
                    $vue->setVar("idSpectacle",$idSpectacle);
                    $vue->setVar("search_stmt",$search_stmt);
                    return $vue;
                } else {
                    // Raffiche la page de modification
                    $intervenantAModifier = $this->spectacleModele->intervenant($pdo, $idIntervenant);
                    // Recupere les données de la liste des métiers des intervenants
                    $searchStmt = $this->spectacleModele->listeMetiersIntervenants($pdo);
                    $vue = new View("vues/vue_modifier_intervenant");
                    $vue->setVar("existePas",true);
                    $vue->setVar("searchStmt",$searchStmt);
                    $vue->setVar("nom", $intervenantAModifier['nom']);
                    $vue->setVar("prenom", $intervenantAModifier['prenom']);
                    $vue->setVar("mail", $intervenantAModifier['mail']);
                    $vue->setVar("ancienMetier", $intervenantAModifier['typeIntervenant']);
                    $vue->setVar("ancienSurScene", $intervenantAModifier['surScene']);
                    $vue->setVar("idIntervenant",$idIntervenant);
                    $vue->setVar("idSpectacle",$idSpectacle);
                    return $vue;
                }
            } else {
                // Regarde si l'intervenant a insérer existe déja.
                $existeIntervenant = $this->spectacleModele->existeIntervenant($pdo, $idSpectacle, $nom, $prenom, $mail,
                    $surScene, $typeIntervenant);
                if(!$existeIntervenant) {
                    $this->spectacleModele->insertionIntervenant($pdo, $idSpectacle, $nom, $prenom, $mail, $surScene,
                        $typeIntervenant);
                    // Mets les données dans la vue
                    // Renvoie le nom prénom et métier de notre artiste
                    $search_stmt = $this->spectacleModele->infoIntervenant($pdo, $idSpectacle);
                    $vue = new View("vues/vue_intervenant");
                    $vue->setVar("idSpectacle",$idSpectacle);
                    $vue->setVar("search_stmt",$search_stmt);
                    return $vue;
                } else {
                    $idSpectacle = HttpHelper::getParam('idSpectacle');
                    // Recupere les données de la liste des métiers des intervenants
                    $searchStmt = $this->spectacleModele->listeMetiersIntervenants($pdo);
                    $vue = new View("vues/vue_ajouter_intervenant");
                    $vue->setVar("searchStmt",$searchStmt);
                    $vue->setVar("idSpectacle",$idSpectacle);
                    $vue->setVar("existePas",$existeIntervenant);
                    return $vue;
                }
            }
        }
        header("Location: ?controller=Home");
        exit();
    }
    
    public function afficherIntervenant(PDO $pdo)
    {

        $idSpectacle = HttpHelper::getParam('idSpectacle');
        $idUtilisateur = $_SESSION['id_utilisateur'];

        if ($this -> spectacleModele -> verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)) {
            //Renvoie le nom prénom et métier de notre intervenant
            $search_stmt = $this->spectacleModele->infoIntervenant($pdo, $idSpectacle);

            $vue = new View("vues/vue_intervenant");
            $vue->setVar("idSpectacle", $idSpectacle);
            $vue->setVar("search_stmt", $search_stmt);
            return $vue;
        }
        header("Location: index.php?controller=Home");
        exit();
    }

    public function supprimerIntervenant(PDO $pdo)
    {
        $idSpectacle = HttpHelper::getParam('idSpectacle');
        $idIntervenant = HttpHelper::getParam('idIntervenant');
        $idUtilisateur = $_SESSION['id_utilisateur'];

        if ($this -> spectacleModele -> verifierDroitSurSpectacle($pdo, $idUtilisateur, $idSpectacle)){
            $this->spectacleModele->supprimerIntervenant($pdo, $idIntervenant);

            //Renvoie le nom prénom et métier de notre intervenant
            $search_stmt = $this->spectacleModele->infoIntervenant($pdo, $idSpectacle);

            $vue = new View("vues/vue_intervenant");
            $vue->setVar("idSpectacle",$idSpectacle);
            $vue->setVar("search_stmt",$search_stmt);
            return $vue;
        }
        header("Location: ?controller=Home");
        exit();
    }
}