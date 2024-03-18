<?php
    require $_SERVER['DOCUMENT_ROOT'] . '/festiplan/api/json.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/festiplan/api/pdo.php';
    try {

        $pdo = getPDO();
        $infos= explode('/', $_SERVER['REQUEST_URI']);
        $utilisateur = authApi($pdo, $_SERVER["HTTP_KEY"] ?? 0);
        for ($i = 0; $i < 3; $i++) {
            array_shift($infos);
        }
        if ($utilisateur != 0) {
            if (isset($infos[0]) && $infos[0] != '') {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        switch ($infos[0]) {
                            // Ajouter ici les différents cas
                            case "festivals":
                                sendJSON(getListeFestival($pdo), 200);
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
                            default :
                                retourKO("Demande inconnue", 404);
                        }
                        break;
                    case 'DELETE':
                        switch ($infos[0]) {
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
            if ($infos[0] == "login" && $_SERVER['REQUEST_METHOD'] == 'GET') {
                if (isset($infos[1]) && isset($infos[2])) {
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