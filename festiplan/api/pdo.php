<?php
    function getPDO()
    {
        // paramètres de connexion à la base de données
        $host = "SAE_S3_DevWeb_db";
        $port = '3306';
        $dbName = 'festiplanbfgi_sae';
        $user = 'root';
        $pass = 'root';
        $charset = 'utf8mb4';
        // connexion à la base de données
        $ds_name = "mysql:host=$host;port=$port;dbname=$dbName;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true
        ];

        return new PDO($ds_name, $user, $pass, $options);
    }

    function getListeFestival(PDO $pdo): array
    {
        $stmt = $pdo->prepare("SELECT idFestival, titre, dateDebut, dateFin FROM Festival");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function verifLoginPassword(PDO $pdo, $login, $pass): string
    {
        $stmt = $pdo->prepare("SELECT loginAPI(:login, :pass) as token");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':pass', $pass);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['token'];
    }

    function authApi(PDO $pdo, $token): bool|int
    {
        $stmt = $pdo->prepare("SELECT idUtilisateur FROM ClesApi WHERE cleApi = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return false;
        } else {
            return $stmt->fetch()['idUtilisateur'];
        }
    }

    function logout(PDO $pdo, $idUtilisateur): void
    {
        $stmt = $pdo->prepare("DELETE FROM ClesApi WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt->execute();
    }

    function getDetailsFestival(PDO $pdo, $idFestival): array
    {
        $stmt = $pdo->prepare("SELECT Festival.titre, Festival.dateDebut, Festival.dateFin, Festival.description, CategorieFestival.nom AS categorie
                                     FROM Festival 
                                     JOIN CategorieFestival
                                     ON Festival.categorie = CategorieFestival.idCategorie
                                     WHERE Festival.idFestival = :idFestival;");
        $stmt->bindParam(':idFestival', $idFestival);
        $stmt->execute();
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare("SELECT u.nom, u.prenom
                                     FROM Utilisateur u
                                     JOIN EquipeOrganisatrice o
                                     ON u.idUtilisateur = o.idUtilisateur
                                     WHERE o.idFestival = :idFestival
                                     ORDER BY o.responsable DESC;");
        $stmt->bindParam(':idFestival', $idFestival);
        $stmt->execute();
        $resultat['organisateurs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare("SELECT s.titre
                                     FROM Spectacle s
                                     JOIN SpectacleDeFestival sf
                                     ON s.idSpectacle = sf.idSpectacle
                                     WHERE sf.idFestival = :idFestival;");
        $stmt->bindParam(':idFestival', $idFestival);
        $stmt->execute();
        $resultat['spectacles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultat;
    }

    function ajouterFavorie(PDO $pdo, $idUtilisateur, $idFestival): void
    {
        $stmt = $pdo->prepare("INSERT INTO Favoris (idUtilisateur, idFestival) VALUES (:idUtilisateur, :idFestival)");
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt->bindParam(':idFestival', $idFestival);
        $stmt->execute();
    }

    function supprimerFavorie(PDO $pdo, $idUtilisateur, $idFestival): void
    {
        $stmt = $pdo->prepare("DELETE FROM Favoris WHERE idUtilisateur = :idUtilisateur AND idFestival = :idFestival");
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt->bindParam(':idFestival', $idFestival);
        $stmt->execute();
    }

    function getListeFavoris(PDO $pdo, $idUtilisateur): array
    {
        $stmt = $pdo->prepare("SELECT idFestival, titre, dateDebut, dateFin 
                                     FROM Festival
                                     JOIN Favoris
                                     ON Festival.idFestival = Favoris.idFestival
                                     WHERE Favoris.idUtilisateur = :idUtilisateur");
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
