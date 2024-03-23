<?php

namespace controleurs;

use modeles\UserModele;
use modeles\SpectacleModele;
use modeles\FestivalModele;
use PDO;
use yasmf\HttpHelper;
use yasmf\View;

class UtilisateurCompteControleur
{
    private UserModele $userModele;

    private SpectacleModele $spectacleModele;

    private FestivalModele $festivalModele;


    public function __construct(UserModele $userModele,SpectacleModele $spectacleModele, FestivalModele $festivalModele){
        $this->userModele = $userModele;
        $this->spectacleModele = $spectacleModele;
        $this->festivalModele = $festivalModele;
    }

    public function connexion($pdo) {
        $verifLoginOuMdp = true;
        $login = HttpHelper::getParam('login');
        $mdp = HttpHelper::getParam('mdp');
        $searchStmt = $this->userModele->trouverCompteUtilisateurParLoginMdp($pdo, $login, $mdp);
        $user = $searchStmt->fetch();
        if (!$user){
            $verifLoginOuMdp = false;
            $vue = new View("vues/vue_connexion");
            $vue->setVar("loginOuMdpOk", $verifLoginOuMdp);
            return $vue;
        } else {
            session_start();
            $_SESSION['utilisateur_connecte'] = true;
            $_SESSION['id_utilisateur'] = $user['idUtilisateur'];
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
            $vue->setVar("nbPages", $nbPages);
            $vue->setVar("afficher", false);
            $vue->setVar("mesFestivals", $mesFestivals);
            $vue->setVar("lesResponsables", $lesResponsables);
            return $vue;
        }
    }
    
    public function pageInscription() {
        $verifNom = true;
        $verifPrenom = true;
        $verifEmail = true;
        $verifLogin = true;
        $verifMdp = true;
        $verifConfirmMdp = true;
        $vue = new View("vues/vue_inscription");
        $vue->setVar("nomOk", $verifNom);
        $vue->setVar("prenomOk", $verifPrenom);
        $vue->setVar("emailOk", $verifEmail);
        $vue->setVar("loginOk", $verifLogin);
        $vue->setVar("mdpOk", $verifMdp);
        $vue->setVar("confirmMdpOk", $verifConfirmMdp);
        return $vue;
    }

    public function creerCompteUtilisateur(PDO $pdo) {

        $nom = HttpHelper::getParam('nom');
        $prenom = HttpHelper::getParam('prenom');
        $email = HttpHelper::getParam('email');
        $login = HttpHelper::getParam('login');
        $mdp = HttpHelper::getParam('mdp');
        $confirmMdp = HttpHelper::getParam('confirmMdp');

        $verifNom = (strlen($nom) <= 35);
        $verifPrenom = (strlen($prenom) <= 30);
        $verifEmail = (strlen($email) <= 50 && !$this->userModele->emailExisteDeja($pdo, $email));
        $verifLogin = (strlen($login) <= 35 && !$this->userModele->loginExisteDeja($pdo, $login));
        $verifMdp = (strlen($mdp) <= 30);
        $verifConfirmMdp = (strlen($confirmMdp) <= 30 && $mdp == $confirmMdp);

        try {
            $estOk = $verifConfirmMdp && $verifEmail && $verifLogin && $verifMdp && $verifNom && $verifPrenom;
            if ($estOk) {
                $searchStmt = $this->userModele->creerCompteUtilisateur($pdo, $login, $mdp, $nom, $prenom, $email);
                $verifLoginOuMdp = true;
                $vue = new View("vues/vue_connexion");
                $vue->setVar("loginOuMdpOk", $verifLoginOuMdp);
                return $vue;
            } else {
                $vue = new View("vues/vue_inscription");
                $vue->setVar("nomOk", $verifNom);
                $vue->setVar("ancienNom", $nom);
                $vue->setVar("prenomOk", $verifPrenom);
                $vue->setVar("ancienPrenom", $prenom);
                $vue->setVar("emailOk", $verifEmail);
                $vue->setVar("ancienEmail", $email);
                $vue->setVar("loginOk", $verifLogin);
                $vue->setVar("ancienLogin", $login);
                $vue->setVar("mdpOk", $verifMdp);
                $vue->setVar("confirmMdpOk", $verifConfirmMdp);
                return $vue;
            }
        } catch (PDOException $e) {
            return new View("vues/vue_inscription");
        }
    }

    public function pageModifierProfil(PDO $pdo) {
        $verifNom = true;
        $verifPrenom = true;
        $verifLogin = true;
        $verifMdp = true;
        $verifConfirmMdp = true;
        $verifEmail = true;
        $verifAncienMdp = true;
        session_start();
        $utilisateur = $this->userModele->recupererInformationsProfil($pdo, $_SESSION['id_utilisateur']);
        $utilisateur = $utilisateur->fetch();
        $vue = new View("vues/vue_modifier_profil");
        $vue->setVar("nomOk", $verifNom);
        $vue->setVar("ancienNom", $utilisateur['nom']);
        $vue->setVar("prenomOk", $verifPrenom);
        $vue->setVar("ancienPrenom", $utilisateur['prenom']);
        $vue->setVar("loginOk", $verifLogin);
        $vue->setVar("ancienLogin", $utilisateur['login']);
        $vue->setVar("emailOk", $verifEmail);
        $vue->setVar("ancienEmail", $utilisateur['mail']);
        $vue->setVar("ancienMdpOk", $verifAncienMdp);
        $vue->setVar("mdpOk", $verifMdp);
        $vue->setVar("confirmMdpOk", $verifConfirmMdp);
        return $vue;
    }

    public function pageProfil(PDO $pdo) {
        session_start();
        $idUtilisateur = $_SESSION['id_utilisateur'];
        $utilisateur = $this->userModele->recupererInformationsProfil($pdo, $idUtilisateur);
        $utilisateur = $utilisateur->fetch();
        $vue = new View("vues/vue_profil");
        $vue->setVar("ancienNom", $utilisateur['nom']);
        $vue->setVar("ancienPrenom", $utilisateur['prenom']);
        $vue->setVar("ancienLogin", $utilisateur['login']);
        $vue->setVar("ancienEmail", $utilisateur['mail']);
        return $vue;
    }

    public function pageDesinscription() {
        session_start();    
        $verifLogin = true;
        $verifMdp = true;
        $vue = new View("vues/vue_desinscription");
        $vue->setVar("loginOk", $verifLogin);
        $vue->setVar("mdpOk", $verifMdp);
        return $vue;
    }

    public function supprimerProfil(PDO $pdo) {
        session_start();
        $login = HttpHelper::getParam('login');
        $mdp = HttpHelper::getParam('mdp');
        $utilisateur = $this->userModele->recupererInformationsProfil($pdo, $_SESSION['id_utilisateur']);
        $utilisateur = $utilisateur->fetch();
        if ($login === $utilisateur['login'] && $mdp == $utilisateur['mdp']) {
            $this->userModele->supprimerCompteUtilisateur($pdo, $_SESSION['id_utilisateur']);
            session_destroy();
            $verifLoginOuMdp = true;
            $vue = new View("vues/vue_connexion");
            $vue->setVar("loginOuMdpOk", $verifLoginOuMdp);
            return $vue;
        } else if ($login != $utilisateur['login'] && $mdp != $utilisateur['mdp']){
            $verifLogin = false;
            $verifMdp = false;
            $vue = new View("vues/vue_desinscription");
            $vue->setVar("loginOk", $verifLogin);
            $vue->setVar("mdpOk", $verifMdp);
            return $vue;
        } else if ($mdp != $utilisateur['mdp']){
            $verifMdp = false;
            $verifLogin = true;
            $vue = new View("vues/vue_desinscription");
            $vue->setVar("mdpOk", $verifMdp);
            $vue->setVar("loginOk", $verifLogin);
            return $vue;
        } else {
            $verifMdp = true;
            $verifLogin = false;
            $vue = new View("vues/vue_desinscription");
            $vue->setVar("mdpOk", $verifMdp);
            $vue->setVar("loginOk", $verifLogin);
            return $vue;
        }
    }

    public function modifierCompteUtilisateur(PDO $pdo) {
        $nom = HttpHelper::getParam('nom');
        $prenom = HttpHelper::getParam('prenom');
        $login = HttpHelper::getParam('login');
        $mdp = HttpHelper::getParam('mdp');
        $confirmMdp = HttpHelper::getParam('confirmMdp');
        $email = HttpHelper::getParam('email');
        $ancienMdp = HttpHelper::getParam('ancienMdp');

        // Initialisez les variables à true
        $verifNom = true;
        $verifPrenom = true;
        $verifLogin = true;
        $verifMdp = true;
        $verifConfirmMdp = true;
        $verifEmail = true;
        $verifAncienMdp = true;

        session_start();
        $utilisateur = $this->userModele->recupererInformationsProfil($pdo, $_SESSION['id_utilisateur']);
        $utilisateur = $utilisateur->fetch();

        if ($ancienMdp !== $utilisateur['mdp']) {
            $verifAncienMdp = false;
        } 
        $verifNom = (strlen($nom) <= 35);
        $verifPrenom = (strlen($prenom) <= 30);
        $verifLogin = (strlen($login) <= 35);
        $verifMdp = (strlen($mdp) <= 30);
        $verifEmail = (strlen($email) <= 50);
        $verifConfirmMdp = ($mdp == $confirmMdp);

        try {
            $estOk = $verifConfirmMdp && $verifLogin && $verifMdp && $verifNom && $verifPrenom && $verifEmail && $verifAncienMdp;
            if ($estOk) {
                $searchStmt = $this->userModele->modifierCompteUtilisateur($pdo, $login, $mdp, $nom, $prenom, $email);
                return new View("vues/vue_connexion");
                // a regler
            } else {
                $vue = new View("vues/vue_modifier_profil");
                $vue->setVar("nomOk", $verifNom);
                $vue->setVar("ancienNom", $nom);
                $vue->setVar("prenomOk", $verifPrenom);
                $vue->setVar("ancienPrenom", $prenom);
                $vue->setVar("loginOk", $verifLogin);
                $vue->setVar("ancienLogin", $login);
                $vue->setVar("emailOk", $verifEmail);
                $vue->setVar("ancienEmail", $email);
                $vue->setVar("ancienMdpOk", $verifAncienMdp);
                $vue->setVar("mdpOk", $verifMdp);
                $vue->setVar("confirmMdpOk", $verifConfirmMdp);
                return $vue;
            }
        } catch (PDOException $e) {
            return new View("vues/vue_modifier_profil");
        }
    }

    public function deconnexion() {
        session_start();
        session_destroy();
        $verifLoginOuMdp = true;
        $vue = new View("vues/vue_connexion");
        $vue->setVar("loginOuMdpOk", $verifLoginOuMdp);
        return $vue;
    }
}