<?php
    function getPDO()
    {
        // paramètres de connexion à la base de données
        $host = "localhost";
        $db = "mezabi57";
        $charset = "utf8";
        $username = "root";
        $password = "root";
        // connexion à la base de données
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // retourne l'objet PDO
        return $pdo;
    }