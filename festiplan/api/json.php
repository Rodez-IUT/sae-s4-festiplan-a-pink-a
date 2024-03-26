<?php
function sendJSON($infos, $codeRetour): void
{
    header("Access-Control-Allow-Origin: *"); // Autorisation d'accès depuis n'importe quel site
    header("Content-Type: application/json; charset=UTF-8"); // Format de la réponse
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Méthodes autorisées

    http_response_code($codeRetour); // Code de retour
    echo json_encode($infos, JSON_UNESCAPED_UNICODE); // Encodage de la structure au format JSON
}