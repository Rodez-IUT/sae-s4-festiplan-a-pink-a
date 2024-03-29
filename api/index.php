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
        // Suprime les 3 premiers arguments de l'URL
        for ($i = 0; $i < 3; $i++) {
            array_shift($infos);
        }
        // Si l'utilisateur est valide (la clé est valide)
        if ($utilisateur != 0) {
            if (isset($infos[0]) && $infos[0] != '') {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        switch ($infos[0]) {
                            case "festivals":
                                sendJSON(getListeFestival($pdo, $utilisateur), 200);
                                break;
                            case "festival":
                                if (isset($infos[1])) {
                                    sendJSON(getDetailsFestival($pdo, $infos[1]), 200);
                                } else {
                                    retourKO("Paramètre manquant", 404);
                                }
                                break;
                            default :
                                retourKO("Demande inconnue", 404);
                        }
                        break;
                    case 'POST':
                        switch ($infos[0]) {
                            case 'ajoutFavori':
                                $donnees = json_decode(file_get_contents('php://input'), true);
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
                                break;
                            default :
                                retourKO("Demande inconnue", 404);
                        }
                        break;
                    case 'DELETE':
                        switch ($infos[0]) {
                            case "supprimerFavori":
                                $donnees = json_decode(file_get_contents('php://input'), true);
                                $res = supprimerFavori($pdo, $utilisateur, $donnees['idFestival']);
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
                                break;
                            case "logout":
                                logout($pdo, $utilisateur);
                                retourOK("Déconnexion réussie", 200);
                                break;
                            default :
                                retourKO("Demande inconnue", 404);
                        }
                        break;
                    default:
                        retourKO("Méthode inconnue", 404);
                }
            } else {
                retourKO("Demande inconnue", 404);
            }
        } else {
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
    } catch (Exception $e) {
        retourKO($e->getMessage(), 500);
    }