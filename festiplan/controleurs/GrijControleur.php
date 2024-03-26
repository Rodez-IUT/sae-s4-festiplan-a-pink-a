<?php

namespace controleurs;

use PDO;
use PDOStatement;
use DateTime;
use yasmf\View;
use yasmf\HttpHelper;
use modeles\GrijModele;

/**
 * Class GrijControleur
 * Gère la feature "Grij" pour l'aaplication web.
 */
class GrijControleur
{
    // Objet GrijModele
    private GrijModele $grijModele;
    
    /**
     * Constructeur de l'objet GrijCOntroleur
     * @param GrijModele $grijModele Objet GrijModele qui sert à communiquer avec la base
     * de données.
     */
    public function __construct(GrijModele $grijModele)
    {
        $this->grijModele = $grijModele;
    }

    /**
     * Méthode par défaut du contrôleur.
     * @param PDO $pdo Objet PDO connecté à la base de données.
     * @return View La vue à afficher.
     */
    public function index(PDO $pdo)
    {
        $idFestival = HttpHelper::getParam('idFestival');

        $vue = new View('vues/vue_parametres_grij');
        $message = null;
        $stmt = $this->grijModele->recupererParametresGrij($pdo, $idFestival);
        $row = $stmt->fetch();
        if ($row) {
            $vue->setVar('heureDebut', $row['heureDebut']);
            $vue->setVar('heureFin', $row['heureFin']);
            $vue->setVar('ecartEntreSpectacles', $row['tempsEntreSpectacle']);
        }

        $vue->setVar('message', $message);
        $vue->setVar('idFestival', $idFestival);
        return $vue;
    }

    /**
     * Enregistre une grij ou met à jour une grij déjà existante.
     * @param PDO$pdo Objet PDO connecté à la base de données.
     * @return View La vue à afficher.
     */
    public function enregistrerGrij(PDO $pdo)
    {
        $message = null;
        // Récupération des données du lien.
        $idFestival = HttpHelper::getParam('idFestival');
        $heureDebut = HttpHelper::getParam('heureDebut');
        $heureFin = HttpHelper::getParam('heureFin');
        $ecartEntreSpectacles =HttpHelper::getParam('ecartEntreSpectacles');

        // Vérification de la cohérence des données.
        if ($heureDebut == null || $heureFin == null || $ecartEntreSpectacles == null){
            $vue = new View('vues/vue_parametres_grij');
            $message = "Vous n'avez pas entré tous les champs";
            $this->initialiseHeuresSelectionnees($vue, $heureDebut, $heureFin, $ecartEntreSpectacles);

        } else if (strtotime($heureDebut)> strtotime($heureFin)) {
            $vue = new View('vues/vue_parametres_grij');
            $message = "La date de fin est plus petite que celle de début.<br>Il faut entrer une date de fin plus grande";
            $this->initialiseHeuresSelectionnees($vue, $heureDebut, $heureFin, $ecartEntreSpectacles);
        
        } else if ($this->convertirEnMinutes($ecartEntreSpectacles)
          >= ($this->convertirEnMinutes($heureFin) - $this->convertirEnMinutes($heureDebut))) {
            $vue = new View('vues/vue_parametres_grij');
            $message = "L'écart entre les spectacles est supérieur à la durée totale";
            $this->initialiseHeuresSelectionnees($vue, $heureDebut, $heureFin, $ecartEntreSpectacles);
        
        } else {
            // Si les données sont cohérentes
            // On modifie la grij
            $ok = $this->grijModele->modifierCreerGrij($pdo, $idFestival, $heureDebut, $heureFin, $ecartEntreSpectacles);
            // Si la modification c'est bien effectuter
            if ($ok){
                // Récupération des jours du festival
                $jours = $this->grijModele->recupererJours($pdo, $idFestival);
                // Récupération des spectacles
                $spectacles = $this->grijModele->recupererSpectacles($pdo, $idFestival);
                // création de la grille d'affichage
                $this->planifierSpectacles($pdo, $idFestival,$spectacles,$heureDebut, $heureFin, $ecartEntreSpectacles, $jours);

                // récupération de la grij
                $grij = $this->grijModele->recupererGrij($pdo, $idFestival);
                $spectacleNonPlace = $this->grijModele->recupererSpectacleNonPlace($pdo,$idFestival);

                $vue = new View('vues/vue_consultation_planification');
                $vue->setVar('listeSpectacleNonPlace', $spectacleNonPlace);
                $vue->setVar('listeJours', $grij);

            } else {
                // réaffichage des paramétrage en cas d'erreur
                $vue = new View('vues/vue_parametres_grij');
                $message = "Erreur avec la base de données.";
                $this->initialiseHeuresSelectionnees($vue, $heureDebut, $heureFin, $ecartEntreSpectacles);
            }
        }
        $vue->setVar('profilSpectacle', null);
        $vue->setVar('idFestival', $idFestival);
        $vue->setVar('message', $message);
        return $vue;
    }

    /**
     * Converti les heures en minutes.
     * @param string $heure Les heures à convertir.
     * @return int Le résultat en minutes.
     */
    private function convertirEnMinutes(string $heure) {
        $temps = explode(":", $heure);
        return (int) $temps[0] * 60 + (int) $temps[1];
    }

    /**
     * Initialise les variables relatives aux heures de la vue.
     * @param View $vue La vue où on initialise les variables.
     * @param string $debut L'heure de début.
     * @param string $fin L'heure de fin.
     * @param string $ecart L'écart entre chaque spectacle (en heure).
     */
    private function initialiseHeuresSelectionnees($vue, $debut, $fin, $ecart)
    {
        $vue->setVar('heureDebut', $debut);
        $vue->setVar('heureFin', $fin);
        $vue->setVar('ecartEntreSpectacles', $ecart);
    }

    /**
     * Planifie les spectacle du festivals par rapport au temps, au nombre de
     * jours, des scènes disponible et des spectacles.
     * @param PDO $pdo Objet PDO connecté à la base de données.
     * @param int $idFestival L'id du festival que l'on planifie.
     * @param PDOStatement $spectacles la liste des spectacles à planifier.
     * @param string $heureDebut L'heure de début d'une journé.
     * @param string $heureFin L'heure de fin d'une journé.
     * @param string $ecartEntreSpectacles L'écart entre deux spectacles dans une journée
     * @param PDOStatement $jours La liste des jours d'un festival.
     */
    private function planifierSpectacles(PDO $pdo, int $idFestival, PDOStatement $spectacles, string $heureDebut, string $heureFin,
                                         string $ecartEntreSpectacles, PDOStatement $jours)
    {
        // Calcule de la durée d'une journé en minute
        $dureeTotal = $this->convertirEnMinutes($heureFin) - $this->convertirEnMinutes($heureDebut);
        $ecart = $this->convertirEnMinutes($ecartEntreSpectacles);
        $i = 0;
        $unSpectacle = $spectacles->fetch();

        // Parcours chaque jour
        while (($jour = $jours->fetch()) && $unSpectacle) {
            $ordre = 0;
            $duree = 0;
            $leJourContinue = true;
            $spectacleNonPlace = null;
            
            //On entre un spectcale
            if (($this->convertirEnMinutes($unSpectacle['duree'])+ $duree) <= $dureeTotal) {
                $scenesAdequates = $this->grijModele->recuperationSceneAdequate($pdo, $unSpectacle['taille']);
                $heureDebutSpectacle = $this->convertirMinutesEnHeuresMySQL($duree + $this->convertirEnMinutes($heureDebut));
                $duree += $this->convertirEnMinutes($unSpectacle['duree']);
                $heureFinSpectacle = $this->convertirMinutesEnHeuresMySQL($duree + $this->convertirEnMinutes($heureDebut));
                // vérifie si des scènes sont compatible avec le spectacle
                if ($scenesOk = $scenesAdequates->fetchAll()) {
                    $this->grijModele->insertSpectaclesParJour($pdo,$idFestival, $jour['idJour'],$unSpectacle['id'], $ordre, 1,$heureDebutSpectacle,$heureFinSpectacle,null);
                    $this->grijModele->insertionSpectacleScene($pdo, $idFestival, $unSpectacle['id'], $scenesOk);
                    $ordre++;
                    $duree += $ecart;
                } else {
                    $this->grijModele->insertSpectaclesParJour($pdo,$idFestival, null,$unSpectacle['id'], $ordre, 0,null,null,3);
                    $duree -= $this->convertirEnMinutes($unSpectacle['duree']);
                }
            }else {
                $leJourContinue = false;
            }

            //Boucle s'il y a d'autre spectacles
            while($leJourContinue && ($unSpectacle = $spectacles->fetch()) && $duree < $dureeTotal) {
                if (($this->convertirEnMinutes($unSpectacle['duree'])+ $duree) < $dureeTotal) {
                    $heureDebutSpectacle = $this->convertirMinutesEnHeuresMySQL($duree + $this->convertirEnMinutes($heureDebut));
                    $duree += $this->convertirEnMinutes($unSpectacle['duree']);
                    $heureFinSpectacle = $this->convertirMinutesEnHeuresMySQL($duree + $this->convertirEnMinutes($heureDebut));
                    $scenesAdequates = $this->grijModele->recuperationSceneAdequate($pdo, $unSpectacle['taille']);
                    $scenesOk = $scenesAdequates->fetchAll();
                    if ($scenesOk) {
                        $this->grijModele->insertSpectaclesParJour($pdo,$idFestival, $jour['idJour'],$unSpectacle['id'], $ordre, 1,$heureDebutSpectacle,$heureFinSpectacle,null);
                        $this->grijModele->insertionSpectacleScene($pdo, $idFestival, $unSpectacle['id'], $scenesOk);
                        $ordre++;
                        $duree += $ecart;
                    } else {
                        $this->grijModele->insertSpectaclesParJour($pdo,$idFestival, null,$unSpectacle['id'], 0, 0,null,null,3);
                        $duree -= $this->convertirEnMinutes($unSpectacle['duree']);
                    }
                    
                } else {
                    $leJourContinue  = false;
                    $duree += $this->convertirEnMinutes($unSpectacle['duree']);
                    $spectacleNonPlace = $unSpectacle;
                }
            }
            if($unSpectacle && $this->convertirEnMinutes($unSpectacle['duree']) > $dureeTotal) {
                $spectacleNonPlace = $unSpectacle;
                $unSpectacle = false;
            }
        }
        // Ajoute les spectacles non ajoutés à la planification 
        if (isset ($spectacleNonPlace)) {
            $causeNonPlace = null;
            if($jour != false) {
                $causeNonPlace = 1;
            } else {
                $causeNonPlace = 2;
            }
            $this->grijModele->insertSpectaclesParJour($pdo,$idFestival, null,$spectacleNonPlace['id'], 0,
                0,null,null,$causeNonPlace);
            while ($unSpectacle = $spectacles->fetch()){
                $this->grijModele->insertSpectaclesParJour($pdo,$idFestival, null,$unSpectacle['id'], 0,
                    0,null,null,$causeNonPlace);
            }
        }
    }

    /**
     * Affiche le profil du spectacle sélectionné dans la planification.
     * @param PDO $pdo Objet PDO connecté à la base de données.
     * @return View La vue à afficher.
     */
    public function profilSpectacleJour(PDO $pdo)
    {
        $message = null;
        $idFestival = HttpHelper::getParam('idFestival');
        $idSpectacle = HttpHelper::getParam('idSpectacle');

        $listeScenes = $this->grijModele->recupererListeScenes($pdo,$idFestival, $idSpectacle);
        $infosSpectacle = $this->grijModele->recupererProfilSpectacle($pdo, $idFestival, $idSpectacle);
        $grij = $this->grijModele->recupererGrij($pdo, $idFestival);
        $spectacleNonPlace = $this->grijModele->recupererSpectacleNonPlace($pdo,$idFestival);

        $vue = new View("vues/vue_consultation_planification");
        $vue->setVar('idFestival', $idFestival);
        $vue->setVar('message', $message);
        $vue->setVar('profilSpectacle', true);
        $vue->setVar('listeScenes', $listeScenes);
        $vue->setVar('infosSpectacle', $infosSpectacle);
        $vue->setVar('listeJours', $grij);
        $vue->setVar('listeSpectacleNonPlace', $spectacleNonPlace);
        return $vue;
    }

    /**
     * Converti des heures en minutes.
     * @param int $minutes La valeur en minute à convertir en heures.
     * @return string Le résultat en heure compatible au type TIME en mySQL.
     */
    public function convertirMinutesEnHeuresMySQL(int $minutes) {
        $heures = floor($minutes / 60);
        $minutesRestantes = $minutes % 60;
        $tempsFormate = new DateTime("1970-01-01 $heures:$minutesRestantes:00");
        $tempsMySQL = $tempsFormate->format('H:i:s');
        
        return $tempsMySQL;
    }
}