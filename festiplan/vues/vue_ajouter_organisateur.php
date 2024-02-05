<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] == false) {
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter des Organisateurs :</title>
    <link href="festiplan/static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="festiplan/static/css/index.css"/>
    <link href="festiplan/static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>
<!--En tête-->
<header>
    <div class="container-fluid header">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="festiplan/static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas"> Ajouter des organisateurs : </h2>
            </div>
            <div class="col-1 col-md-2 text-right"> <!-- Ajoutez la classe text-right pour aligner à droite -->
                <!-- Icône utilisateur avec menu déroulant -->
                <div class="dropdown">
                    <span class="fas fa-solid fa-user dropdown-btn iconeBlanc icone-user"></span>
                    <div class="dropdown-content">
                        <a href="?controller=UtilisateurCompte&action=pageProfil">Profil</a>
                        <a href="?controller=UtilisateurCompte&action=pageModifierProfil">Modifier Profil</a>
                        <a href="?controller=UtilisateurCompte&action=pageDesinscription">Désinscription</a>
                        <a href="?controller=UtilisateurCompte&action=deconnexion">Déconnexion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<body class="body-blanc">
    <form action="index.php" method="post">

        <input type="hidden" name="controller" value="Festival">
        <input type="hidden" name="action" value="majOrganisateur">
        <input type="hidden" name="idFestival" value="<?php echo $idFestival?>">
        
        <br>
        <h1>Liste des Organisateurs : </h1><br>
        <?php
        // Charger tous les résultats de la liste des organisateurs dans un tableau
        $organisateurIDs = array();
        while ($row2 = $listeOrganisateur->fetch()) {
            $organisateurIDs[] = $row2['idUtilisateur'];
        }

        // Revenir au début de la liste des organisateurs
        $listeOrganisateur->execute();

        while ($row = $listeUtilisateur->fetch()) {
            ?>
            <div class="col-12">
                <input type="checkbox" class="checkBoxs" name="Utilisateurs[]" value="<?php echo $row['idUtilisateur']; ?>" <?php
                // Vérifier si l'utilisateur est dans la liste des organisateurs
                if (in_array($row['idUtilisateur'], $organisateurIDs)) {
                    echo 'checked';
                        
                }
                // Rend le responsable impossible a uncheck
                if ($row['idUtilisateur'] == $idResponsable) {
                    echo ' disabled';
                }
                ?>>
                <?php echo $row['nom']." ".$row['prenom']; ?>
                <br>
            </div>
            <?php
        }
        ?>
        <div class="footer">
            <button type="submit" class="btn btn-bleu">Confirmer</button>   
            <a href="?controller=Festival&action=afficherFestival&idFestival=<?php echo $idFestival;?>"><button type="button" class="btn btn-gris">Annuler</button></a>  
        </div>
    </form>
</body>
</html>