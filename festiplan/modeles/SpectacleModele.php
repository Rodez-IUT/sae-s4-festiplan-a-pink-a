<?php

namespace modeles;

use PDOException;
use PDO;
use PDOStatement;
use PHPUnit\Exception;

class SpectacleModele 
{
    /**
     * Renvoie dans une liste des différentes
     * catégories de spectacles
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @return PDOStatement la liste des catégories de spectacle
     */
    public function listeCategorieSpectacle(PDO $pdo)
    {
        $sql = "SELECT * FROM CategorieSpectacle";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->execute();
        return $searchStmt;
    }

    /**
     * Renvoie dans une liste déroulante les différentes 
     * tailles de scènes
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @return PDOStatement un statement contenant les tailles de scène
     */
    public function listeTailleScene(PDO $pdo)
    {
        $sql = "SELECT * FROM Taille";
        $search_stmt = $pdo->prepare($sql);
        $search_stmt->execute();
        return $search_stmt;
    }

    /**
     * Vérifie que l'utilisateur
     * @param int $idUtilisateur l'identifiant de l'utilisateur qui cherche à modifier le spectacle
     * @param int $idSpectacle l'identifiant du spectacle qu'on cherche à modifier
     * @return bool true si l'utilisateur a les droits sur ce spectacle, false sinon
     */
    public function verifierDroitSurSpectacle(PDO $pdo, int $idUtilisateur, int $idSpectacle) :bool {
        try {
            $requete = "SELECT Utilisateur.idUtilisateur
                        FROM Utilisateur
                        JOIN SpectacleOrganisateur
                        ON Utilisateur.idUtilisateur = SpectacleOrganisateur.idUtilisateur
                        JOIN Spectacle
                        ON SpectacleOrganisateur.idSpectacle = Spectacle.idSpectacle
                        WHERE Utilisateur.idUtilisateur = :user
                        AND Spectacle.idSpectacle = :spectacle";
            $stmt = $pdo -> prepare($requete);
            $stmt -> bindValue("user", $idUtilisateur);
            $stmt -> bindValue("spectacle", $idSpectacle);
            $stmt -> execute();
            return $stmt ->rowCount() != 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Insèrer un spectacle dans la base de données
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param string $titre nom du spectalce
     * @param string $description description du spectacle
     * @param string $duree temps du spectacle
     * @param string $illustration image du spectacle
     * @param int $categorie du spectacle
     * @param int $taille de la scène dont le spectacle à besoin
     * @param int $idUtilisateur de la scène dont le spectacle à besoin
     * @return bool en fonction de la réussite de la transaction
     */
    public function insertionspectacle(PDO $pdo, string $titre, string $description, string $duree, string $illustration,
                                       int $categorie, int $taille, int $idUtilisateur) :bool
    {   
        try {
            $pdo -> beginTransaction();
            $sql =
                "INSERT INTO Spectacle (titre,description,duree,illustration,categorie,tailleSceneRequise) 
                VALUES (:leTitre,:laDesc,:leTemps,:illu,:laCate,:tailleScene)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam("leTitre",$titre);
            $stmt->bindParam("laDesc",$description);
            $stmt->bindParam("leTemps",$duree);
            $stmt->bindParam("illu",$illustration);
            $stmt->bindParam("laCate",$categorie);
            $stmt->bindParam("tailleScene",$taille);
            $stmt->execute();
            // Enregistre le créateur du spectacle en temps qu'organisateur
            $idSpectacle = $pdo->lastInsertId();
            $sql2 = "INSERT INTO SpectacleOrganisateur (idUtilisateur, idSpectacle) VALUES (:idOrg,:idSpectacle)";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindParam("idOrg",$idUtilisateur);
            $stmt2->bindParam("idSpectacle",$idSpectacle);
            $stmt2->execute();
            $pdo -> commit();
            return true;
        } catch (PDOException $e) {
            $pdo -> rollBack();
            return false;
        }
    }

    /**
     * Récupère les infos d'un spectacle
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param int $idSpectacle pour savoir quelle spectacle récupéré
     * @return array les infos du spectacle
     */
    public function leSpectacle(PDO $pdo, int $idSpectacle):array
    {
        $sql = "SELECT * FROM Spectacle WHERE idSpectacle = :id";
        $search_stmt = $pdo->prepare($sql);
        $search_stmt->bindParam("id",$idSpectacle);
        $search_stmt->execute();
        $fetch = $search_stmt->fetch();
        return $fetch;
    }

    /**
     * Récupère les informations d'un intervenant
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param int $idIntervenant pour savoir quelle spectacle récupéré
     * @return array les infos de l'intervenant
     */
    public function intervenant(PDO $pdo, int $idIntervenant)
    {
        $sql = "SELECT * FROM Intervenant WHERE idIntervenant = :id";
        $search_stmt = $pdo->prepare($sql);
        $search_stmt->bindParam("id",$idIntervenant);
        $search_stmt->execute();
        $fetch = $search_stmt->fetch();
        return $fetch;
    }


    /**
     * Recherche la nombre de spectacle de l'utilisateur
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param int $idOrganisateur l'id de l'utilisateur courant.
     * @return int l'ensemble des festivals.
     */
    public function nombreMesSpectacles(PDO $pdo, $idOrganisateur) 
    {
        $sql = "SELECT Count(Spectacle.idSpectacle) AS nbSpectacle FROM Spectacle JOIN SpectacleOrganisateur ON Spectacle.idSpectacle=SpectacleOrganisateur.idSpectacle JOIN Utilisateur ON Utilisateur.idUtilisateur=SpectacleOrganisateur.idUtilisateur WHERE SpectacleOrganisateur.idUtilisateur = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("id",$idOrganisateur);
        $stmt->execute();
        // Récupérer le résultat du COUNT
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Maintenant, $result contient le résultat du COUNT
        $nbSpectacle = $result['nbSpectacle'];

        return $nbSpectacle;
    }

    /**
     * Récupère le nombre de spectacles ayant le terme recherche dans leur nom
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param string $recherche le terme à chercher parmi les spectacles
     */
    public function nombreSpectaclesRecherche (PDO $pdo, $recherche) :int
    {
        $sql = "SELECT Count(idSpectacle) AS nbSpectacle 
                FROM Spectacle 
                WHERE titre LIKE :terme";
        $stmt = $pdo->prepare($sql);
        $terme = '%'.$recherche.'%';
        $stmt->bindParam('terme', $terme);
        $stmt->execute();
        // Récupérer le résultat du COUNT
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // Maintenant, $result contient le résultat du COUNT
        $nbSpectacle = $result['nbSpectacle'];
        return $nbSpectacle;
    }

    /**
     * Recherche la liste des spectacle dont l'utilisateur est organisateur
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param int $idOrganisateur l'id de l'utilisateur courant.
     * @param int $premierElement l'indice du premier élément à afficher sur la page
     * @return PDOStatement l'ensemble des festivals.
     */
    public function listeMesSpectacles(PDO $pdo, int $idOrganisateur, int $premierElement):PDOStatement
    {
        $sql =
            "SELECT Spectacle.titre, Utilisateur.nom, Spectacle.idSpectacle 
            FROM Spectacle 
            JOIN SpectacleOrganisateur 
                ON Spectacle.idSpectacle = SpectacleOrganisateur.idSpectacle 
            JOIN Utilisateur 
                ON Utilisateur.idUtilisateur = SpectacleOrganisateur.idUtilisateur 
            WHERE SpectacleOrganisateur.idUtilisateur = :id 
            LIMIT 4 
            OFFSET :nPage";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("id",$idOrganisateur);
        $stmt->bindParam("nPage",$premierElement,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Recherche la liste de tout les spectacles ayant un certain terme (récupération paginée)
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param int $premier l'indice du premier élément à afficher dans la page
     * @param string $recherche le terme recherché dans le nom du spectacle
     * @return PDOStatement l'ensemble des festivals.
     */
    public function listeSpectacles(PDO $pdo, int $premier,  $recherche)
    {
        $sql = "SELECT Spectacle.titre,Spectacle.idSpectacle,Spectacle.duree FROM Spectacle WHERE titre LIKE :terme LIMIT 4 OFFSET :nPage ";
        $stmt = $pdo->prepare($sql);
        $terme = '%'.$recherche.'%';
        $stmt->bindParam('terme', $terme);
        $stmt->bindParam("nPage",$premier,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Renvoie de la liste des métiers possibles pour un intervenant
     * @param PDO $pdo un objet PDO connecté à la base de données
     */
    public function listeMetiersIntervenants(PDO $pdo)
    {
        $sql = "SELECT * FROM MetierIntervenant ORDER BY metier";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Insertion des intervenants
     * @param PDO $pdo pour la connexion à la base de données
     * @param int $idSpectacle le spectacle sur lequel on ajoute l'intervenant
     * @param string $nom le nom de l'intervenant
     * @param string $prenom le prénom de l'intervenant
     * @param string $mail le mail de l'intervenant
     * @param int $surScene l'intervenant est sur ou hors scene
     * @param string $typeIntervenant pour récupérer le métier de l'intervenant
     * @return bool en fonction de la réussite de la transaction
     */
    public function insertionIntervenant(PDO $pdo, int $idSpectacle, string $nom, string $prenom, string $mail, int $surScene,
                                         string $typeIntervenant):bool
    {
        try {
            $pdo -> beginTransaction();
            $sql = "INSERT INTO Intervenant (idSpectacle,   nom,   prenom,   mail,   surScene, typeIntervenant) 
                    VALUES                  (:leIdSpectacle,:leNom,:lePrenom,:leMail,:surScene,:typeIntervenant)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam("leIdSpectacle",$idSpectacle);
            $stmt->bindParam("leNom",$nom);
            $stmt->bindParam("lePrenom",$prenom);
            $stmt->bindParam("leMail",$mail);
            $stmt->bindParam("surScene",$surScene);
            $stmt->bindParam("typeIntervenant",$typeIntervenant);
            $stmt->execute();
            $pdo ->commit();
            return true;
        } catch (PDOException $e) {
            echo "Sur scène : ";
            var_dump($surScene);
            $pdo -> rollBack();
            return false;
        }
    }

    /**
     * Supprimer le spectacle voulu
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param int $idSpectacle l'id du festival a supprimer.
     */
    public function supprimerSpectacle(PDO $pdo, int $idSpectacle)
    {   
        $sql = "DELETE FROM SpectacleOrganisateur WHERE idSpectacle = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("id",$idSpectacle);
        $stmt->execute();
        $sql2 = "DELETE FROM SpectacleDeFestival WHERE idSpectacle = :id";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam("id",$idSpectacle);
        $stmt2->execute();
        $sql3 = "DELETE FROM Spectacle WHERE idSpectacle = :id";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->bindParam("id",$idSpectacle);
        $stmt3->execute();
        $sql4 = "DELETE FROM Intervenant WHERE idSpectacle = :id";
        $stmt4 = $pdo->prepare($sql4);
        $stmt4->bindParam("id",$idSpectacle);
        $stmt4->execute();
    }

    /**
     * Modifier un spectacle dans la base de données
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param string $titre nom du spectalce
     * @param string $description description du spectacle
     * @param string $duree temps du spectacle
     * @param string $illustration image du spectacle
     * @param string $categorie du spectacle
     * @param int $taille de la scène dont le spectacle à besoin
     * @param int $idSpectacle le spectacle à modifier
     * @return boolean en fonction de la réussite de la transaction
     */
    public function modifspectacle(PDO $pdo, string $titre, string $description, $duree, string $illustration,
                                   string $categorie, $taille, int $idSpectacle)
    {
        try {
            $pdo -> beginTransaction();
            $sql = "UPDATE Spectacle SET titre = :leTitre, description = :laDesc, duree = :leTemps, illustration = :illu, categorie = :laCate, tailleSceneRequise = :tailleScene WHERE idSpectacle = :idSpectacle";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam("leTitre", $titre);
            $stmt->bindParam("laDesc", $description);
            $stmt->bindParam("leTemps", $duree);
            $stmt->bindParam("illu", $illustration);
            $stmt->bindParam("laCate", $categorie);
            $stmt->bindParam("tailleScene", $taille);
            $stmt->bindParam("idSpectacle", $idSpectacle);
            $stmt->execute();
            $pdo ->commit();
            return true;
        } catch (PDOException $e) {
            $pdo -> rollBack();
            return false;
        }
    }

    /**
     * Modifier un intervenat dans la base de données
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param string $nom nom de l'intervenat
     * @param string $prenom de l'intervenant du spectacle
     * @param string $mail de l'intervenant du spectacle
     * @param int $surScene de l'intervenant du spectacle
     * @param string $metierIntervenant le métier de l'intervenant
     * @param int $idIntervenant l'identifiant de l'intervenant
     * @return boolean en fonction de la réussite de la transaction
     */
    public function modifIntervenant(PDO $pdo, string $nom, string $prenom, string $mail, int $surScene,
                                     string $metierIntervenant, int $idIntervenant)
    {
        try {
            $pdo -> beginTransaction();
            $sql = "UPDATE Intervenant 
                    SET nom = :leNom, 
                        prenom = :lePrenom, 
                        mail = :leMail, 
                        surScene = :surScene, 
                        typeIntervenant = :leMetier 
                    WHERE idIntervenant = :idIntervenant";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam("leNom", $nom);
            $stmt->bindParam("lePrenom", $prenom);
            $stmt->bindParam("leMail", $mail);
            $stmt->bindParam("surScene", $surScene);
            $stmt->bindParam("leMetier", $typeIntervenant);
            $stmt->bindParam("idIntervenant", $idIntervenant);
            $stmt->execute();
            $pdo ->commit();
            return true;
        } catch (PDOException $e) {
            $pdo -> rollBack();
            return false;
        }
    }

    /**
     * Renvoie l'intervenant recherché, s'il existe
     * @param PDO $pdo pour la connexion à la base de données
     * @param string $nom pour récupérer le nom de l'intervenant
     * @param string $prenom pour récupérer le prénom de l'intervenant
     * @param string $mail pour récuperer le mail de l'intervenant
     * @param $surScene pour savoir si l'intervenant est sur ou hors scene
     * @param $typeIntervenant pour récupérer le métier de l'intervenant
     * @return bool les données de l'intervenant
     */
    public function existeIntervenant(PDO $pdo, int $idSpectacle, string $nom, string $prenom, string $mail, $surScene,
                                      $typeIntervenant) : bool
    {
        $sql = "SELECT idSpectacle,nom,prenom,mail,surScene,typeIntervenant 
                FROM Intervenant 
                WHERE idSpectacle=:leIdSpectacle 
                    AND nom=:leNom 
                    AND prenom=:lePrenom 
                    AND mail=:leMail 
                    AND surScene=:surScene 
                    AND typeIntervenant=:typeIntervenant";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("leIdSpectacle",$idSpectacle);
        $stmt->bindParam("leNom",$nom);
        $stmt->bindParam("lePrenom",$prenom);
        $stmt->bindParam("leMail",$mail);
        $stmt->bindParam("surScene",$surScene);
        $stmt->bindParam("typeIntervenant",$typeIntervenant);
        $stmt->execute();
        $fetch = $stmt->fetch();
        return $fetch != false;
    }

    /**
     * Pour afficher la liste des intervenants d'un spectacle
     * @param PDO $pdo pour se connecter à la base de donnée
     * @param int $idSpectacle le spectacle dont on veut connaître les intervenants
     */
    public function infoIntervenant(PDO $pdo, int $idSpectacle)
    {
        $sql = "SELECT nom, prenom, metier, surScene, idIntervenant, idSpectacle 
                FROM Intervenant 
                JOIN MetierIntervenant 
                    ON idMetierIntervenant = typeIntervenant 
                WHERE idSpectacle =:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("id",$idSpectacle);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Pour supprimer un intervenant d'un spectacle
     * @param PDO $pdo pour se connecter à la base de donnée
     * @param int $idIntervenant l'intervenant que l'on souhaite supprimer
     */
    public function supprimerIntervenant(PDO $pdo, int $idIntervenant)
    {
        $sql = "DELETE FROM Intervenant 
                WHERE idIntervenant = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam("id",$idIntervenant);
        $stmt->execute();
    }
}