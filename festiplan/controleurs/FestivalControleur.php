<?php

namespace controleurs;

use PDO;
use DateTime;
use yasmf\View;
use yasmf\HttpHelper;
use modeles\FestivalModele;
use modeles\SpectacleModele;

class FestivalControleur {

    private SpectacleModele $spectacleModele;

    private FestivalModele $festivalModele;

    public function __construct(SpectacleModele $spectacleModele, FestivalModele $festivalModele) {
        $this->spectacleModele = $spectacleModele;
        $this->festivalModele = $festivalModele;
    }

    public function index(PDO $pdo) : View {
        // Met tout les champs en rouge lorsqu'on arrive sur la page
        $verifNom = false;
        $verifDesc = false;
        $verifDate = false;
        // Recherche les différentes catégories
        $searchStmt = $this->festivalModele->listeCategorieFestival($pdo);
        $vue = new View("vues/vue_creer_festival");
        // Met a false toutes les champs de création du festival
        $vue->setVar("nomOk", $verifNom);
        $vue->setVar("descOk", $verifDesc);
        $vue->setVar("dateOk", $verifDate);
        $vue->setVar("ancienneCategorie", " ");
        $vue->setVar("searchStmt",$searchStmt);
        return $vue;
    }

    public function nouveauOuModificationFestival(PDO $pdo) : View {
        session_start();
        // Récupere tout les parametre d'un festival
        $nom = HttpHelper::getParam('nom');
        $description = HttpHelper::getParam('description');
        $dateDebut = HttpHelper::getParam('dateDebut');
        $dateFin = HttpHelper::getParam('dateFin');
        $categorie = HttpHelper::getParam('categorie');
        $img = "aaa";
        
        // Récupere true si on modifier un festival, false si on en créer un
        $modifier = HttpHelper::getParam('modifier');
        $verifNom = false;
        $verifDesc = false;
        $verifDate = false;
        // Verifie que le nom du festival fais moins de 36 carac et sois différent de vide
        if (strlen($nom) <= 35 && trim($nom) != "") {
            $verifNom = true;
        }
        // Verifie que la description du festival fais moins de 1001 carac et sois différent de vide
        if (strlen($description) <= 1000 && trim($description) != "") {
            $verifDesc = true;
        }

        // Date de debut minamale dun festival
        $dateDebutMin = DateTime::createFromFormat('Y-m-d', '1950-01-01');
        // Date de fin maximale dun festival
        $dateFinMax = DateTime::createFromFormat('d/m/Y', '01/01/2300');
        // Formater les dates au format 'Y-m-d' (année-mois-jour)
        $dateFormateeDebut = $dateDebutMin->format('Y-m-d');
        $dateFormateeFin = $dateFinMax->format('Y-m-d');

        // Verifie que la date de fin du festival sois plus tard que celle de début et quel sois valide
        if ($dateFin >= $dateDebut && $dateDebut>$dateFormateeDebut && $dateDebut<$dateFormateeFin && $dateFin>$dateFormateeDebut && $dateFin<$dateFormateeFin) {
            $verifDate = true;
        }

        // Si toute les valeurs sont correctes ajoute le festival a la base de données
        if ($verifDate && $verifDesc && $verifNom) {
            // Recupere l'id de l'utilisateur courant
            $idOrganisateur = $_SESSION['id_utilisateur'];
            // Insere ce festival dans la base de données ou le modifie selon la valeur de $modifier
            if ($modifier == 'true') {
                $idFestival = HttpHelper::getParam('idFestival');
                $modification = $this->festivalModele->modificationFestival($pdo, $nom, $description, $dateDebut, $dateFin, $categorie, $img, $idFestival);
            } else {
                $insertion = $this->festivalModele->insertionFestival($pdo, $nom, $description, $dateDebut, $dateFin, $categorie, $img, $idOrganisateur);
            }

            // Renvoie a la connexion qui renvoie lui même a l'accueil car l'utilisateur est connecté.
            // Ainsi cela permet de bloquer l'ajout de festival lorsque l'on refresh a l'infini 
            $vue = new View("vues/vue_connexion");
            return $vue;
        } else {
            // Si des valeurs sont incorectes renvoie lesquels le sont et les valeurs
            $searchStmt = $this->festivalModele->listeCategorieFestival($pdo);
            // Renvoie a la vue de modification ou de création selon le cas
            if ($modifier == 'true') {
                $idOrganisateur = $_SESSION['id_utilisateur'];
                $idFestival = HttpHelper::getParam('idFestival');
                $estResponsable = $this->festivalModele->estResponsable($pdo,$idFestival,$idOrganisateur);
                $listeOrganisateur = $this->festivalModele->listeOrganisateurFestival($pdo,$idFestival);
                $vue = new View("vues/vue_modifier_festival");
                $vue->setVar("estResponsable", $estResponsable['responsable']);
                $vue->setVar("listeOrganisateur", $listeOrganisateur);
            } else {
                $vue = new View("vues/vue_creer_festival");
            }
            $vue->setVar("nomOk", $verifNom);
            $vue->setVar("ancienNom", $nom);
            $vue->setVar("descOk", $verifDesc);
            $vue->setVar("ancienneDesc", $description);
            $vue->setVar("dateOk", $verifDate);
            $vue->setVar("ancienneDateDebut", $dateDebut);
            $vue->setVar("ancienneDateFin", $dateFin);
            $vue->setVar("ancienneCategorie", $categorie);
            $vue->setVar("searchStmt",$searchStmt);
            return $vue;
        }
    }

    public function afficherFestival(PDO $pdo) : View {
        session_start();

        $idOrganisateur = $_SESSION['id_utilisateur'];
        $idFestival = HttpHelper::getParam('idFestival');

        // Recupere si l'utilisateur et le responsable du festival
        $estResponsable = $this->festivalModele->estResponsable($pdo,$idFestival,$idOrganisateur);
        // Recupere les données du festival séléctionné
        $festivalAModifier = $this->festivalModele->leFestival($pdo,$idFestival);
        // Recupere les données de la liste des catégorie
        $searchStmt = $this->festivalModele->listeCategorieFestival($pdo);
        // Recupere l'ensemble des organisateur actuel du festival
        $listeOrganisateur = $this->festivalModele->listeOrganisateurFestival($pdo,$idFestival);
        // Mets les données dans la vue
        $vue = new View("vues/vue_modifier_festival");
        $vue->setVar("nomOk", true);
        $vue->setVar("ancienNom", $festivalAModifier['titre']);
        $vue->setVar("descOk", true);
        $vue->setVar("ancienneDesc", $festivalAModifier['description']);
        $vue->setVar("dateOk", true);
        $vue->setVar("ancienneDateDebut", $festivalAModifier['dateDebut']);
        $vue->setVar("ancienneDateFin", $festivalAModifier['dateFin']);
        $vue->setVar("ancienneCategorie", $festivalAModifier['categorie']);
        $vue->setVar("idFestival", $idFestival);
        $vue->setVar("searchStmt",$searchStmt);
        $vue->setVar("estResponsable", $estResponsable['responsable']);
        $vue->setVar("listeOrganisateur", $listeOrganisateur);
        return $vue;
    }

    public function supprimerFestival(PDO $pdo) : View {
        session_start();

        $idOrganisateur = $_SESSION['id_utilisateur'];
        $idFestival = HttpHelper::getParam('idFestival');
        $idUtilisateur = $_SESSION['id_utilisateur'];

        // Supprime le festival de la base de données
        if ($this -> festivalModele -> estResponsable($pdo, $idFestival, $idUtilisateur)){
            $supprimerFestival = $this->festivalModele->supprimerFestival($pdo, $idFestival);
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
            $lesResponsables = $this->festivalModele->listeLesResponsables($pdo);

            $vue = new View("vues/vue_accueil");
            $vue->setVar("afficherSpectacles", false);
            $vue->setVar("nbPages", $nbPages);
            $vue->setVar("mesFestivals", $mesFestivals);
            $vue->setVar("lesResponsables", $lesResponsables);
            return $vue;
        }
        header("Location: index.php");
        exit();
    }

    public function gestionOrganisateur(PDO $pdo) : View {
        session_start();
        $idResponsable = $_SESSION['id_utilisateur'];
        $idFestival = HttpHelper::getParam('idFestival');

        $chipi_chipi = $this -> festivalModele -> verifierDroitSurFestival($pdo, $idUtilisateur, $idFestival);
        var_dump($chipi_chipi);
        if ($chipi_chipi){
            // Recupere les données du festival séléctionné
            $festival = $this->festivalModele->leFestival($pdo,$idFestival);

            // Recupere tout les utilisateurs
            $listeUtilisateur = $this->festivalModele->listeUtilisateur($pdo);
            // Recupere tout les organisateurActuel du festival
            $listeOrganisateur = $this->festivalModele->listeOrganisateurFestival($pdo,$idFestival);

            $vue = new View("vues/vue_ajouter_organisateur");
            $vue->setVar("nomFestival", $festival['titre']);
            $vue->setVar("idFestival", $idFestival);
            $vue->setVar("idResponsable", $idResponsable);
            $vue->setVar("listeOrganisateur", $listeOrganisateur);
            $vue->setVar("listeUtilisateur", $listeUtilisateur);

            return $vue;
        }
        header("Location: index.php");
        exit();
    }


    /**
     * Récupère un array depuis les paramètres
     * @param string $name the name of the param
     * @return array|null the value of the param if defined, null otherwise
     */
    public static function getParamArray(string $name): ?array {
        if (isset($_GET[$name])) return $_GET[$name];
        if (isset($_POST[$name])) return $_POST[$name];
        return null;
    }

    
    public function majOrganisateur(PDO $pdo) : View {
        session_start();
        $idOrganisateur = $_SESSION['id_utilisateur'];
        $idFestival = HttpHelper::getParam('idFestival');

        // Récupere tout les utilisateurs checks
        $idUtilisateurs = self::getParamArray('Utilisateurs');
        // Supprime tout les organisateur sauf le responsable
        
        $this->festivalModele->supprimerOrganisateurs($pdo,$idFestival);
        foreach($idUtilisateurs as $utilisateur) {
            
            // Ajoute un a un les nouveaux organisateurs
            $this->festivalModele->majOrganisateur($pdo,$idFestival,$utilisateur);
        }

        $festivalAModifier = $this->festivalModele->leFestival($pdo,$idFestival);
        // Recupere les données de la liste des catégorie
        $searchStmt = $this->festivalModele->listeCategorieFestival($pdo);
        // Recupere l'ensemble des organisateur actuel du festival
        $listeOrganisateur = $this->festivalModele->listeOrganisateurFestival($pdo,$idFestival);
        // Mets les données dans la vue
        $vue = new View("vues/vue_modifier_festival");
        $vue->setVar("nomOk", true);
        $vue->setVar("ancienNom", $festivalAModifier['titre']);
        $vue->setVar("descOk", true);
        $vue->setVar("ancienneDesc", $festivalAModifier['description']);
        $vue->setVar("dateOk", true);
        $vue->setVar("ancienneDateDebut", $festivalAModifier['dateDebut']);
        $vue->setVar("ancienneDateFin", $festivalAModifier['dateFin']);
        $vue->setVar("ancienneCategorie", $festivalAModifier['categorie']);
        $vue->setVar("idFestival", $idFestival);
        $vue->setVar("searchStmt",$searchStmt);
        $vue->setVar("estResponsable", true);
        $vue->setVar("listeOrganisateur", $listeOrganisateur);
        return $vue;
    }


    public function modifierListeSpectacleFestival(PDO $pdo) : View {
        $idFestival = HttpHelper::getParam('idFestival');
        // On détermine sur quelle page on se trouve
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $pageActuelle = (int) strip_tags($_GET['page']);
        } else {
            $pageActuelle = 1;
        }
        // On Recupere la recherche
        $recherche =  HttpHelper::getParam('derniereRecherche');

        $nbSpectacles = (int)$this->spectacleModele->nombreSpectaclesRecherche($pdo,$recherche);
        // On calcule le nombre de pages total
        $nbPages = ceil($nbSpectacles / 4);
        // Calcul du 1er element de la page
        $premier = ($pageActuelle * 4) - 4;
        $listeSpectacles = $this->spectacleModele->listeSpectacles($pdo,$premier,$recherche);
        $listeSpectacleDeFestival = $this->festivalModele->listeSpectacleDeFestival($pdo,$idFestival);

        $vue = new View("vues/vue_ajouter_spectacle");
        $vue->setVar("idFestival", $idFestival);
        $vue->setVar("nbPages",$nbPages);
        $vue->setVar("pageActuelle",$pageActuelle);
        $vue->setVar("listeSpectacleDeFestival", $listeSpectacleDeFestival);
        $vue->setVar("listeSpectacles", $listeSpectacles);
        $vue->setVar("derniereRecherche", $recherche);
        return $vue;
    }

    public function ajouterSpectacleDeFestival(PDO $pdo) : View {
        $idFestival = HttpHelper::getParam('idFestival');
        // Récupere le spectacle check
        $idSpectacle = HttpHelper::getParam('spectacle');
        // On récupere sur quelle page on se trouve
        $pageActuelle = (int) HttpHelper::getParam('pageActuelle');
        // Recupere la recherche
        $recherche =  HttpHelper::getParam('derniereRecherche');
        // Ajoute le nouveau spectacle
        $this->festivalModele->majSpectacleDeFestival($pdo,$idFestival,$idSpectacle);

        $nbSpectacles = (int)$this->spectacleModele->nombreSpectaclesRecherche($pdo,$recherche);
        // On calcule le nombre de pages total
        $nbPages = ceil($nbSpectacles / 4);
        // Calcul du 1er element de la page
        $premier = ($pageActuelle * 4) - 4;
        $listeSpectacles = $this->spectacleModele->listeSpectacles($pdo,$premier,$recherche);
        $listeSpectacleDeFestival = $this->festivalModele->listeSpectacleDeFestival($pdo,$idFestival);

        $vue = new View("vues/vue_ajouter_spectacle");
        $vue->setVar("idFestival", $idFestival);
        $vue->setVar("nbPages",$nbPages);
        $vue->setVar("pageActuelle",$pageActuelle);
        $vue->setVar("listeSpectacleDeFestival", $listeSpectacleDeFestival);
        $vue->setVar("listeSpectacles", $listeSpectacles);
        $vue->setVar("derniereRecherche", $recherche);
        return $vue;
    }
    
    public function supprimerSpectacleDeFestival (PDO $pdo) : View {
        $idFestival = HttpHelper::getParam('idFestival');
        // Récupere le spectacle checks
        $idSpectacle = HttpHelper::getParam('spectacle');
        // On récupere sur quelle page on se trouve
        $pageActuelle = (int) HttpHelper::getParam('pageActuelle');
        // Recupere la recherche
        $recherche =  HttpHelper::getParam('derniereRecherche');

        // Supprime le spectacle
        $this->festivalModele->supprimerSpectacleDeFestival($pdo,$idFestival,$idSpectacle);
        $nbSpectacles = (int) $this->spectacleModele->nombreSpectaclesRecherche($pdo,$recherche);
        // On calcule le nombre de pages total
        $nbPages = ceil($nbSpectacles / 4);
        // Calcul du 1er element de la page
        $premier = ($pageActuelle * 4) - 4;
        $listeSpectacles = $this->spectacleModele->listeSpectacles($pdo,$premier,$recherche);
        $listeSpectacleDeFestival = $this->festivalModele->listeSpectacleDeFestival($pdo,$idFestival);

        $vue = new View("vues/vue_ajouter_spectacle");
        $vue->setVar("idFestival", $idFestival);
        $vue->setVar("nbPages",$nbPages);
        $vue->setVar("pageActuelle",$pageActuelle);
        $vue->setVar("listeSpectacleDeFestival", $listeSpectacleDeFestival);
        $vue->setVar("listeSpectacles", $listeSpectacles);
        $vue->setVar("derniereRecherche", $recherche);
        return $vue;
    }

    public function rechercheSpectacle (PDO $pdo) : View {
        $idFestival = HttpHelper::getParam('idFestival');
        // On récupere sur quelle page on se trouve
        $recherche =  HttpHelper::getParam('recherche');

        $nbSpectacles = (int)$this->spectacleModele->nombreSpectaclesRecherche($pdo,$recherche);
        // On calcule le nombre de pages total
        $nbPages = ceil($nbSpectacles / 4);
        // Calcul du 1er element de la page
        $premier = (1 * 4) - 4;
        $listeSpectacles = $this->spectacleModele->listeSpectacles($pdo,$premier,$recherche);
        $listeSpectacleDeFestival = $this->festivalModele->listeSpectacleDeFestival($pdo,$idFestival);

        $vue = new View("vues/vue_ajouter_spectacle");
        $vue->setVar("idFestival", $idFestival);
        $vue->setVar("nbPages",$nbPages);
        $vue->setVar("pageActuelle",1);
        $vue->setVar("listeSpectacleDeFestival", $listeSpectacleDeFestival);
        $vue->setVar("listeSpectacles", $listeSpectacles);
        $vue->setVar("derniereRecherche", $recherche);
        return $vue;
    }

}