<?php
// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] == false) {
    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="fr">

<hesad>
    <meta charset="UTF-8">
    <title>Intervenant</title>
    <link href="static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/index.css" />
    <link href="static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</hesad>
<!--En tête-->
<header>Z
    <div class="container-fluid header">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas">Intervenants</h2>
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
    <div class="container-fluid">
        <?php

        if (isset($search_stmt) && $search_stmt->rowCount() > 0) {

            ?>
            <div class="table-scrollable">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Métier</th>
                        <th>Statuts</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                    <?php while ($row = $search_stmt->fetch()) { ?>
                        <tr>
                            <td>
                                <?php echo $row['nom'] ?>
                            </td>
                            <td>
                                <?php echo $row['prenom'] ?>
                            </td>
                            <td>
                                <?php echo $row['metier'] ?>
                            </td>
                            <td>
                                <?php if ($row['surScene'] == 0) {
                                    echo "Sur scène";
                                } else {
                                    echo "Hors scène";
                                } ?>
                            </td>
                            <td><a
                                    href="?controller=Spectacle&action=modifierIntervenant&idIntervenant=<?php echo $row['idIntervenant']; ?>&idSpectacle=<?php echo $row['idSpectacle']; ?>"><span
                                        class="fas fa-solid fa-pen-to-square iconeGrandi iconeNoir"></span></a></td>
                            <td><a data-id-intervenant="<?php echo $row['idIntervenant']; ?>"
                                    data-id-spectacle="<?php echo $row['idSpectacle']; ?>" class="suppIntervenant"><span
                                        class="fas fa-solid fa-trash iconeGrandi iconeNoir"></span></a></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <?php
        } else {

            echo '<h3 class="centre">Vous n' . "'avez aucun intervenant pour ce spectacle</h3>";
            ?>

    <?php
        }
        ?>
    </div>
    <div class="container-fluid footer">
        <div class="row">
            <div class="col-4">
                <a href="?controller=Home"><button type="button" class="btn btn-secondary btnModif fondGris"><span
                            class="fas fa-solid fa-arrow-left-long"></span></button></a>
            </div>
            <div class="col-4">
                <?php if (isset($idSpectacle)){ ?>
                <a href="?controller=Spectacle&action=ajouterIntervenant&idSpectacle=<?php echo $idSpectacle; ?>">
                    <?php } ?>
                    <button
                        type="button" class="btn btn-success btnModif fondVert">
                        <span class="fas fa-solid fa-plus">
                        </span>
                        <b>Intervenant</b>
                    </button>
                    <?php if (isset($idSpectacle)){ ?>
                </a>
                <?php } ?>
            </div>
            <div class="col-4">
                <form action="index.php" method="post">
                    <button type="submit" class="btn btn-success btnModif fondVert"><span
                        class="fas fa-solid fa-plus"></span><b> Lot Intervenant</b></button>
                    <input type="hidden" name="action" value="ajouter10000Intervenant">
                    <input type="hidden" name="controller" value="Spectacle">
                    <input type="hidden" name="idSpectacle" value="<?php echo $idSpectacle; ?>">
                    
                </form>
               
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>

</html>