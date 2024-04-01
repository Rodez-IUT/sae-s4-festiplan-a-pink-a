<?php
    require_once('json.php');
    require_once('service/fonctionsBd.php');
    try {
        // Récupère l'objet PDO
        $pdo = getPDO();
        // Sépare les différentes arguments de l'URL
        $infos = explode('/', $_SERVER['REQUEST_URI']);
        // Vérifie la validité de la clé API et récupère l'utilisateur associé
        $utilisateur = authApi($pdo, $_SERVER["HTTP_KEY"] ?? 0);
        // Suprime les 2 premiers arguments de l'URL (vide et "api")
        for ($i = 0; $i < 2; $i++) {
            array_shift($infos);
        }
        // Si l'utilisateur est valide (la clé est valide)
        if ($utilisateur != false) {
            if (isset($infos[0]) && $infos[0] != '') {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        traitementGET($pdo, $utilisateur, $infos);
                        break;
                    case 'POST':
                        traitementPOST($pdo, $utilisateur, $infos);
                        break;
                    case 'DELETE':
                        traitementDELETE($pdo, $utilisateur, $infos);
                        break;
                    default:
                        retourKO("Méthode inconnue", 404);
                }
            } else {
                retourKO("Demande inconnue", 404);
            }
        } else {
            traitementUtilisateurInconnu($pdo, $infos);
        }
    } catch (Exception $e) {
        retourKO($e->getMessage(), 500);
    }

    /**
     * Traite la requête avec la méthode GET
     * @param PDO $pdo l'objet PDO
     * @param int $utilisateur l'utilisateur qui se connecte à la BD
     * @param string[] $infos les informations transmises par l'URL
     */
    function traitementGET(PDO $pdo, int $utilisateur, array $infos) : void {
        switch ($infos[0]) {
            case "festivals":
                sendJSON(getListeFestival($pdo, $utilisateur), 200);
                break;
            case "festival":
                $festival = $infos[1];
                if ($festival != null) {
                    if (is_numeric($festival)){
                        sendJSON(getDetailsFestival($pdo, (int) $festival), 200);
                    } else {
                        retourKO("Paramètre `idFestival` invalide", 400);
                    }
                } else {
                    retourKO("Paramètre manquant", 404);
                }
                break;
            default :
                retourKO("Demande inconnue", 404);
        }
    }

    /**
     * Traite la requête avec la méthode POST
     * @param PDO $pdo l'objet PDO
     * @param int $utilisateur l'identifiant de l'utilisateur qui effectue la requête
     * @param string[] $infos les informations transmises par l'URL
     */
    function traitementPOST(PDO $pdo, int $utilisateur, array $infos):void{
        switch ($infos[0]) {
            case 'ajoutFavori':
                $requestBody = file_get_contents('php://input');
                if ($requestBody == false){
                    retourKO("Aucun favori n'a été transmis", 400);
                } else {
                    $donnees = json_decode($requestBody, true);
                    $res = ajouterFavori($pdo, $utilisateur, $donnees['idFestival']);
                    switch ($res) {
                        case 0:
                            retourOK("Favori ajouté", 201);
                            break;
                        case 1452:
                            retourKO("Festival inexistant", 404);
                            break;
                        case 1062:
                            retourKO("Favori déjà existant", 409);
                            break;
                        case 1292:
                            retourKO("Données invalides", 400);
                            break;
                        default:
                            retourKO("Erreur inconnue", 500);
                    }
                }
                break;
            default :
                retourKO("Demande inconnue", 404);
        }
    }

    /**
     * Traite la requête avec la méthode DELETE
     * @param PDO $pdo l'objet PDO
     * @param int $utilisateur l'identifiant de l'utilisateur qui fait la requête
     * @param string[] $infos les informations transmises par l'URL
     */
    function traitementDELETE(PDO $pdo, int $utilisateur, array $infos):void{
        switch ($infos[0]) {
            case "supprimerFavori":
                if (!isset($infos[1])){
                    retourKO("Aucun favori n'a été transmis", 400);
                } else {
                    $festival = $infos[1];
                    if (!is_numeric($festival)) {
                        retourKO("Paramètre invalide", 400);
                    } else {
                        $res = supprimerFavori($pdo, $utilisateur, (int) $festival);
                        switch($res) {
                            case 0:
                                retourOK("Favori supprimé", 200);
                                break;
                            case 1:
                                retourKO("Favori inexistant", 404);
                                break;
                            case 1292:
                                retourKO("Données invalides", 400);
                                break;
                            default:
                                retourKO("Erreur inconnue", 500);
                        }
                    }
                }
                break;
            case "logout":
                logout($pdo, $utilisateur);
                retourOK("Déconnexion réussie", 200);
                break;
            default :
                retourKO("Demande inconnue", 404);
        }
    }

    /**
     * Répond à la requête si l'utilisateur n'est pas reconnu
     * @param PDO $pdo l'objet PDO
     * @param string[] $infos les informations transmises par l'URL
     */
    function traitementUtilisateurInconnu(PDO $pdo, array $infos):void {
        // Si l'utilisateur essaye de s'enregistrer
        if ($infos[0] == "login" && $_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($infos[1]) && isset($infos[2])) {
                // enregistre l'utilisateur si valide et renvoie sa clé API
                $token = verifLoginPassword($pdo, $infos[1], $infos[2]);
                if ($token != "erreur") {
                    $retour['cle'] = $token;
                    sendJSON($retour, 200);
                } else {
                    retourKO("Utilisateur inconnu", 404);
                }
            } else {
                retourKO("Paramètres manquants", 404);
            }
        } else if (!isset($_SERVER["HTTP_KEY"])){
            retourKO("Clé API manquante", 401);
        } else {
            retourKO("Clé API invalide", 403);
        }
    }

