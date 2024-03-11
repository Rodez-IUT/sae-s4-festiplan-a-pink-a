<?php
    require $_SERVER['DOCUMENT_ROOT'] . '/festiplan/api/json.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/festiplan/api/pdo.php';
    try {
        $pdo = getPDO();
        $infos= explode('/', $_SERVER['REQUEST_URI']);
        for ($i = 0; $i < 3; $i++) {
            array_shift($infos);
        }
        if (isset($infos[0]) && $infos[0] === '') {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    switch ($infos[0]) {
                        // Ajouter ici les différents cas
                        default :
                            $infos['statut'] = "KO";
                            $infos['message'] = "Demande inconnue";
                            sendJSON($infos, 404);
                    }
                    break;
                case 'POST':
                    switch ($infos[0]) {
                        // Ajouter ici les différents cas
                        default :
                            $infos['statut'] = "KO";
                            $infos['message'] = "Demande inconnue";
                            sendJSON($infos, 404);
                    }
                case 'DELETE':
                    switch ($infos[0]) {
                        // Ajouter ici les différents cas
                        default :
                            $infos['statut'] = "KO";
                            $infos['message'] = "Demande inconnue";
                            sendJSON($infos, 404);
                    }
                default:
                    $infos['statut'] = "KO";
                    $infos['message'] = "Méthode non autorisée";
                    sendJSON($infos, 405);
            }
        } else {
            $infos['statut'] = "KO";
            $infos['message'] = "Demande inconnue";
            sendJSON($infos, 404);
        }
    } catch (Exception $e) {
        $infos['statut'] = "KO";
        $infos['message'] = "Erreur interne au serveur";
        sendJSON($infos, 500);
    }