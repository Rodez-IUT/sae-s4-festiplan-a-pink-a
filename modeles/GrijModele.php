<?php

namespace modeles;

use PDO;
use PDOException;

/**
 * class GrijModele
 * Contient toutes les méthodes relatives à la feature "Grij".
 */
class GrijModele
{
    /**
     * Modifie ou crée une grij pour un festival sélectionné et génére des jours qui seront remplis par les
     * spectacles dans une autre méthodes.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on modifie la grij.
     * @param heureDebut L'heure de début sélectionnée par l'utilisateur lors du paramétrage de la grij.
     * @param heureFin L'heure de fin sélectionnée par l'utilisateur lors du paramétrage de la grij.
     * @param ecartEntreSpectacle L'écart entre chaque spéctacle entré par l'utilisateur lors du paramétrage
     * de la grij. Cette écart temporelle séparera les spectacles dans une journé.
     * @return true si l'opération à fonctionné, false sinon.
     */
    public function modifierCreerGrij(PDO $pdo, $idFestival, $heureDebut, $heureFin, $ecartEntreSpectacles)
    {
        try {
            // Début de la transaction
            $pdo->beginTransaction();
        
            // Vérifier si l'ID du festival existe dans la table Grij
            $stmt = $pdo->prepare("SELECT 1 FROM Grij WHERE idGrij = ?");
            $stmt->execute([$idFestival]);
        
            if ($stmt->rowCount() > 0) {
                // Si l'ID existe, effectuer une mise à jour
                $stmt = $pdo->prepare("UPDATE Grij SET heureDebut = ?, heureFin = ?, tempsEntreSpectacle = ? WHERE idGrij = ?");
                $stmt->execute([$heureDebut, $heureFin, $ecartEntreSpectacles, $idFestival]);
        
                // Suppression des jours déjà générés
                $stmt = $pdo->prepare("DELETE FROM SpectaclesJour WHERE idFestival = ?");
                $stmt->execute([$idFestival]);
                $stmt = $pdo->prepare("DELETE FROM Jour WHERE idGrij = ?");
                $stmt->execute([$idFestival]);
            } else {
                // Si l'ID n'existe pas, effectuer une insertion
                $stmt = $pdo->prepare("INSERT INTO Grij (idGrij, heureDebut, heureFin, tempsEntreSpectacle) VALUES (?,?,?,?)");
                $stmt->execute([$idFestival, $heureDebut, $heureFin, $ecartEntreSpectacles]);
            }
        
            // Récupérer les dates du Festival
            $stmt = $pdo->prepare("SELECT dateDebut, dateFin FROM Festival WHERE idFestival = ?");
            $stmt->execute([$idFestival]);
            $row = $stmt->fetch();
            $f_dateDebut = $row['dateDebut'];
            $f_dateFin = $row['dateFin'];
            
            // Insérer les jours dans la table Jour
            $listeDate = array();
            $sql = "INSERT INTO Jour (idGrij, dateDuJour) VALUES";
            while (strtotime($f_dateDebut) <= strtotime($f_dateFin)) {
                $sql .= " ( ? , ? ),";
                $listeDate[] = intval($idFestival);
                $listeDate[] = $f_dateDebut;
                $f_dateDebut = date("Y-m-d", strtotime($f_dateDebut . " +1 day"));
            }
            // On retire la virgule finale
            $sql = rtrim($sql, ',');
            $stmt = $pdo->prepare($sql);
            $stmt->execute($listeDate);

            // Valider la transaction
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $pdo->rollBack();
            echo "Erreur : " . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère l'heure de début, l'heure de fin et le temps d'écart entre chaque
     * spectacle relative à la grij.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on récupère des information.
     * @return stmt Le résultat de la requête. Contient les informtions spécifique
     * à la grij.
     */
    public function recupererParametresGrij(PDO $pdo, $idFestival)
    {
        $sql = "SELECT heureDebut, heureFin, tempsEntreSpectacle FROM Grij WHERE idGrij = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival]);
        return $stmt;
    }

    /**
     * Récupère les données relatives aux jours d'un festival.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on récupère des informations.
     * @return stmt Contient le résultat de la requête contenant les informations
     * relatives aux jours du festival.
     */
    public function recupererJours(PDO $pdo, $idFestival)
    {
        $sql = "SELECT * FROM Jour WHERE idGrij = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival]);
        return $stmt;
    }

    /**
     * Récupère les spectacles d'un festival.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on veut récupérer des informations.
     * @return stmt Le résultat de la requête.
     */
    public function recupererSpectacles(PDO $pdo, $idFestival)
    {
        $sql = "SELECT spec.titre as titre, spec.duree as duree, spec.tailleSceneRequise as taille, spec.idSpectacle as id
        FROM Festival as f
        JOIN SpectacleDeFestival as sf ON f.idFestival = sf.idFestival
        JOIN Spectacle as spec ON spec.idSpectacle = sf.idSpectacle
        WHERE f.idFestival = ?
        ORDER BY spec.duree";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival]);
        return $stmt;
    }

    /**
     * Insert un spectacle dans un jour à une heure précise dans un ordre précis.
     * Si le paramètre "place" est égal à 0 alors le spectacle n'est pas placé.  
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on insert le spectacle dans la planification.
     * @param idJour L'id du jour où on ajoute un spectacle.
     * @param idSpectacle L'id du spectacle qu'on ajoute dans la planification.
     * @param ordre Le numero de la place qu'occupe le spectacle dans la journé.
     * @param place Indique si le spectacle est placé ou non dans la planification.
     * @param heureDebut L'heure de début du spectacle dans la journé.
     * @param heureFin L'heure de fin du spectacle dans la journé.
     * @param causeNonPlace Si le spectacle n'est pas placé alors cette valeur indique
     * pour qu'elle raison il n'est pas placé.
     */
    public function insertSpectaclesParJour(PDO $pdo,$idFestival, $idJour, $idSpectacle, $ordre, $place, $heureDebut, $heureFin, $causeNonPlace)
    {
        $sql = "INSERT INTO SpectaclesJour VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival,$idJour, $idSpectacle, $ordre, $place,$heureDebut,$heureFin, $causeNonPlace]);
    }

    /**
     * Récupère la grille de planification avec toutes les informations nécessaires
     * pour l'affichage.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on veut récupérer la planification.
     * @return stmt Le résultat de la requête.
     */
    public function recupererGrij(PDO $pdo, $idFestival)
    {
        $sql = "SELECT j.dateDuJour as dateJour, GROUP_CONCAT(DISTINCT s.titre ORDER BY sj.ordre) as titres, GROUP_CONCAT(DISTINCT sj.idSpectacle ORDER BY sj.ordre) as idSpectacles,
                GROUP_CONCAT(DISTINCT sj.heureDebut) as heureDebut, GROUP_CONCAT(DISTINCT sj.heureFin) as heureFin
                FROM Grij as g
                JOIN Jour as j ON j.idGrij = g.idGrij
                JOIN SpectaclesJour as sj ON j.idJour = sj.idJour
                JOIN Spectacle as s ON s.idSpectacle = sj.idSpectacle
                WHERE sj.place = 1
                AND g.idGrij = ?
                GROUP BY j.idJour, j.dateDuJour
                ORDER BY j.dateDuJour";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival]);
        return $stmt;
    }
    
    /**
     * Récupération des scènes adéquate spéciphique à la taille d'un spectacle.
     * @param pdo Objet PDO connecté à la base de données.
     * @param taille La taille sélectionné.
     * @return stmt La liste des scènes correspondantes.
     */
    public function recuperationSceneAdequate(PDO $pdo,$taille)
    {
        $sql = "SELECT idScene
                FROM Scene
                WHERE taille >= ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$taille]);
        return $stmt;
    }

    /**
     * Insert la liste des liaisons entre un spectacle et des scènes.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival concerné.
     * @param idSpectacle L'id du spectacle dont on ajoute les scènes.
     * @param listeScenesAdequates La liste des scènes à ajouter au spectacle.
     * 
     */
    public function insertionSpectacleScene(PDO $pdo, $idFestival, $idSpectacle, $listeScenesAdequates)
    {
        // Suppression de la liste des scènes existante du spectacle
        $sql = "DELETE FROM SpectacleScenes WHERE idSpectacle = ".$idSpectacle;
        $stmt = $pdo->query($sql);

        // Ajout de la liste des scènes possibles du spectacle
        $sql = "INSERT INTO SpectacleScenes (idSpectacle,idScene,idFestival)
                VALUES ";
        foreach($listeScenesAdequates as $idScene) {
            $sql .= "(".$idSpectacle.",".$idScene['idScene'].",".$idFestival."),";
        }
        $sql = substr($sql,0,-1);
        $pdo->query($sql);
    }

    /**
     * Récupérer la listes des scènes relativ à un spectacle d'un festival.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival contenant le spectacle.
     * @param idSpectacle L'id du spectacle dont on récupère la liste des
     * scènes.
     * @return stmt La liste des scènes.
     */
    public function recupererListeScenes(PDO $pdo, $idFestival, $idSpectacle)
    {
        $sql = "SELECT s.nom as nomScene, s.nombreSpectateurs as nbSpectateurs, s.longitude as longitude,
                s.latitude as latitude
                FROM SpectaclesJour as sj
                JOIN SpectacleScenes as ss
                ON sj.idFestival = ss.idFestival AND sj.idSpectacle = ss.idSpectacle
                JOIN Scene as s
                ON s.idScene = ss.idScene
                WHERE sj.idFestival = ?
                AND sj.idSpectacle = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival, $idSpectacle]);
        return $stmt;
    }

    /**
     * Récupérer le profil du spectacle à afficher sur la planification.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on récupère les informations.
     * @param idSpectacle L'id du spectacle du festival dont on récupère les
     * informations.
     * @return stmt Contient les données relatives au profil du spectacle.
     */
    public function recupererProfilSpectacle(PDO $pdo, $idFestival, $idSpectacle)
    {
        $sql = "SELECT sj.heureDebut as heureDebut, sj.heureFin as heureFin, s.titre as titre, s.duree as duree
                FROM SpectaclesJour as sj
                JOIN Spectacle as s
                ON s.idSpectacle = sj.idSpectacle
                WHERE sj.idFestival = ?
                AND sj.idSpectacle = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival, $idSpectacle]);
        return $stmt;
    }

    /**
     * Récupère les spectacles qui n'ont pas été placé sur la grij suite
     * à une tentative de planification grâce aux paramétrage de la grij.
     * @param pdo Objet PDO connecté à la base de données.
     * @param idFestival L'id du festival dont on veut récupérer des
     * informations.
     * @return stmt La liste des spectacles non placés.
     */
    public function recupererSpectacleNonPlace(PDO $pdo, $idFestival)
    {
        $sql = "SELECT s.titre as titre, s.duree as duree, c.intitule as causeNonPlace
                FROM SpectaclesJour as sj
                JOIN Spectacle as s
                ON s.idSpectacle = sj.idSpectacle
                JOIN CauseSpectacleNonPlace as c
                ON c.idCause = sj.idCauseNonPlace
                WHERE sj.idFestival = ?
                AND sj.place = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idFestival]);
        return $stmt;
    }
}