<?php

/**
 * Envoie des informations sous forme d'un document JSON
 * @param string[] $infos les informations à envoyer
 * @param int $codeRetour le code de retour à envoyer
 */
function sendJSON(array $infos, int $codeRetour): void
{
    header("Access-Control-Allow-Origin: *"); // Autorisation d'accès depuis n'importe quel site
    header("Content-Type: application/json; charset=UTF-8"); // Format de la réponse
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Méthodes autorisées

    http_response_code($codeRetour); // Code de retour
    echo json_encode($infos, JSON_UNESCAPED_UNICODE); // Encodage de la structure au format JSON
}

/**
 * Renvoie une erreur sous forme JSON
 * @param string $message le message à envoyer
 * @param int $code le code de retour
 */
function retourKO(string $message, int $code): void
{
    $retour['statut'] = "KO";
    $retour['message'] = $message;
    sendJSON($retour, $code);
}

/**
 * Renvoie une réussite et un message sous forme JSON
 * @param string $message le message à envoyer
 * @param int $code le code d'erreur
 */
function retourOK(string $message, int $code): void
{
    $retour['statut'] = "OK";
    $retour['message'] = $message;
    sendJSON($retour, $code);
}