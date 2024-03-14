<?php

namespace modeles;

use PDO;
use PDOException;
use PDOStatement;

class UserModele
{
    /**
     * Cherche un compte Festiplan dans la base de données par rapport au login
     * et au mot ed passe.
     * @param PDO $pdo un objet PDO connecté à la base de données.
     * @param string $login le login entré par un utilisateur.
     * @param string $pwd le mot de passe entré par un utilisateur.
     * @return PDOStatement les données trouvées par rapport au login et mot de passe.
     */
    public function trouverCompteUtilisateurParLoginMdp(PDO $pdo, string $login, string $pwd):PDOStatement
    {
        $sql = "SELECT idUtilisateur FROM Utilisateur WHERE login = ? AND mdp = ?";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->execute([$login, $pwd]);
        return $searchStmt;
    }

    /**
     * Insert un utilisateur dans la base de données afin de créer un compte.
    * @param PDO $pdo l'objet pdo
    * @param string $login le login choisi par l'utilisateur, doit être unique dans la
    * base de données.
    * @param string $mdp mot de passe entré par l'utilisateur.
    * @param string $nom nom entré par l'utilisateur.
    * @param string $prenom prenom entré par l'utilisateur.
    * @param string $email mail entré par l'utilisateur, doit être unique dans la base
    * de données.
    * @return boolean en fonction de la réussite de la requête
    */
    public function creerCompteUtilisateur(PDO $pdo, string $login, string $mdp, string $nom, string $prenom,
                                           string $email)
    {
        try {
            // Début de la transaction
            $pdo->beginTransaction();
            // Requête d'insertion
            $sql = "INSERT INTO Utilisateur (login, mdp, nom, prenom, mail) VALUES (?,?,?,?,?)";
            $searchStmt = $pdo->prepare($sql);
            $searchStmt->execute([$login, $mdp, $nom, $prenom, $email]);
            return true;
            // Fin de la transaction (enregistrement des modifications)
            $pdo->commit();
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Vérifie l'existence de l'email dans les utilisateurs
     * @param PDO $pdo l'objet pdo
     * @param string $email le mail
     * @return boolean true si l'email est déjà présent parmi les utilisateurs
     */
    function emailExisteDeja(PDO $pdo, string $email) {
        $sql = "SELECT COUNT(*) FROM Utilisateur WHERE mail = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
        return ($count > 0);
    }

    /**
     * Vérifie l'existence du login parmi les utilisateurs
     * @param PDO $pdo l'objet pdo
     * @param string $login le login que l'on cherche
     * @return boolean true si le login existe déjà dans la base
     */
    function loginExisteDeja(PDO $pdo, string $login) {
        $sql = "SELECT COUNT(*) FROM Utilisateur WHERE login = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$login]);
        $count = $stmt->fetchColumn();
        return ($count > 0);
    }

    /**
     * @param PDO $pdo l'objet pdo
     * @param string $login le login de l'utilisateur
     * @param string $mdp le mot de passe de l'utilisateur
     * @param string $nom le nom de l'utilisateur
     * @param string $prenom le prénom de l'utilisateur
     * @param string $email l'email de l'utilisateur
     * @return bool true si la modification a été faite
     */
    public function modifierCompteUtilisateur(PDO $pdo, string $login, string $mdp, string $nom, string $prenom,
                                              string $email) : bool {
        try {
            // Début de la transaction
            $pdo->beginTransaction();
            
            // Requête de mise à jour
            if ($mdp !== null && $mdp !== "" && strlen($mdp) !== 0) {
                $sql = "UPDATE Utilisateur SET mdp = ?, nom = ?, prenom = ?, login = ?, mail = ? WHERE idUtilisateur = ?";
                $updateStmt = $pdo->prepare($sql);
                $updateStmt->execute([$mdp, $nom, $prenom, $login, $email, $_SESSION['id_utilisateur']]);
            } else {
                $sql = "UPDATE Utilisateur SET nom = ?, prenom = ?, login = ?, mail = ? WHERE idUtilisateur = ?";
                $updateStmt = $pdo->prepare($sql);
                $updateStmt->execute([$nom, $prenom, $login, $email, $_SESSION['id_utilisateur']]);
            }
            // Fin de la transaction (enregistrement des modifications)
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Récupère les infos d'un utilisateur
     * @param PDO $pdo l'objet PDO
     * @param int $id l'identifiant de l'utilisateur
     * @return PDOStatement
     */
    public function recupererInformationsProfil(PDO $pdo, int $id) {
        $sql = "SELECT login, nom, prenom, mail, mdp 
                FROM Utilisateur 
                WHERE idUtilisateur = ?";
        $searchStmt = $pdo->prepare($sql);
        $searchStmt->execute([$id]);
        return $searchStmt;
    }

    /**
     * Supprime un compte
     * @param PDO $pdo l'objet PDO
     * @param int $idUtilisateur l'id de l'utilisateur à supprimer
     * @return bool true si la modification a été faite, false sinon
     */
    public function supprimerCompteUtilisateur(PDO $pdo, int $idUtilisateur) {
        // Supprimer de EquipeOrganisatrice
        $sqlUn = "DELETE FROM EquipeOrganisatrice 
                  WHERE idUtilisateur = ?";
        $deleteStmtUn = $pdo->prepare($sqlUn);
        $deleteStmtUn->execute([$idUtilisateur]);
    
        // Supprimer de SpectacleOrganisateur
        $sqlDeux = "DELETE FROM SpectacleOrganisateur 
                    WHERE idUtilisateur = ?";
        $deleteStmtDeux = $pdo->prepare($sqlDeux);
        $deleteStmtDeux->execute([$idUtilisateur]);
    
        // Supprimer de SpectacleDeFestival
        $sqlQuatre = "DELETE FROM SpectacleDeFestival 
                      WHERE idSpectacle IN (SELECT idSpectacle 
                                            FROM SpectacleOrganisateur 
                                            WHERE idUtilisateur = ?)";
        $deleteStmtQuatre = $pdo->prepare($sqlQuatre);
        $deleteStmtQuatre->execute([$idUtilisateur]);
    
        // Supprimer de SpectaclesJour
        $sqlCinq = "DELETE FROM SpectaclesJour 
                    WHERE idSpectacle IN (SELECT idSpectacle 
                                          FROM SpectacleOrganisateur 
                                          WHERE idUtilisateur = ?)";
        $deleteStmtCinq = $pdo->prepare($sqlCinq);
        $deleteStmtCinq->execute([$idUtilisateur]);

        // Supprimer de Jour
        $sqlNeuf = "DELETE FROM Jour 
                    WHERE idJour NOT IN (SELECT idJour 
                                         FROM SpectaclesJour)";
        $deleteStmtNeuf = $pdo->prepare($sqlNeuf);
        $deleteStmtNeuf->execute();

        // Supprimer de Grij
        $sqlHuit = "DELETE FROM Grij 
                    WHERE idGrij NOT IN (SELECT idGrij 
                                         FROM Jour)";
        $deleteStmtHuit = $pdo->prepare($sqlHuit);
        $deleteStmtHuit->execute();

        // Supprimer de Festival (où l'utilisateur est responsable)
        $sqlSix = "DELETE FROM Festival WHERE idFestival IN (SELECT idFestival FROM EquipeOrganisatrice WHERE idUtilisateur = ? AND responsable = 1)";
        $deleteStmtSix = $pdo->prepare($sqlSix);
        $deleteStmtSix->execute([$idUtilisateur]);

        // Supprimer de Spectacle (de l'utilisateur)
        $sqlOnze = "DELETE FROM Spectacle WHERE idSpectacle IN (SELECT idSpectacle FROM SpectacleOrganisateur WHERE idUtilisateur = ?)";
        $deleteStmtOnze = $pdo->prepare($sqlOnze);
        $deleteStmtOnze->execute([$idUtilisateur]);
    
        // Supprimer de Utilisateur
        $sqlDix = "DELETE FROM Utilisateur WHERE idUtilisateur = ?";
        $deleteStmtDix = $pdo->prepare($sqlDix);
        $deleteStmtDix->execute([$idUtilisateur]);
    
        return ($deleteStmtUn && $deleteStmtDeux && $deleteStmtQuatre && $deleteStmtCinq && $deleteStmtSix && $deleteStmtHuit && $deleteStmtNeuf && $deleteStmtDix);
    }
    
    
}