<?php
// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] == false) {
    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter des spectacles</title>
    <link href="static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/index.css" />
    <link href="static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>
<!--En tête-->
<header>
    <div class="container-fluid header">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas">Ajouter des spectacles</h2>
            </div>
            <div class="col-1 col-md-2 text-right"> <!-- Ajoutez la classe text-right pour aligner à droite -->
                <!-- Icône utilisateur avec menu déroulant -->
                <div class="dropdown">
                    <span class="fas fa-solid fa-user dropdown-btn iconeNoir icone-user"></span>
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
    </br>
    <form action="index.php" method="post">
        <input type="hidden" name="controller" value="Festival">
        <input type="hidden" name="action" value="rechercheSpectacle">
        <input type="hidden" name="idFestival" value="<?php echo $idFestival; ?>">
        <div class="col-12 barreRecherche">
            <input type="text" name="recherche" value="<?php echo $derniereRecherche; ?>">
            <input type="submit" value="Rechercher">
        </div>
    </form>

    <form action="index.php" method="post">

        <input type="hidden" name="controller" value="Festival">
        <input type="hidden" name="action" value="modifierListeSpectacle">

        <input type="hidden" name="idFestival" value="<?php echo $idFestival; ?>">
        <div class="col-12">

            <?php
            if ($listeSpectacles->rowCount() > 0) {
                // Charge tout les résultats de la liste des spectacles du fetival dans un tableau
                $spectacleIDs = array();
                while ($row = $listeSpectacleDeFestival->fetch()) {
                    $spectacleIDs[] = $row['idSpectacle'];
                }
                while ($spectacle = $listeSpectacles->fetch()) {
                    ?>
                    <div class="col-12">
                        <div class="centre">
                            <div class='cadreFestival'>
                                <?php
                                $idSpectacle = $spectacle['idSpectacle'];
                                echo $spectacle['titre'] . "</br>" . $spectacle['duree'];
                                ?>

                                <input type="checkbox" class="checkBoxs" name="spectacle"
                                    id="<?php echo $spectacle['idSpectacle']; ?>"
                                    onchange="majListe(<?php echo $spectacle['idSpectacle'] . ',' . $idFestival . ',' . $pageActuelle . ',\'' . $derniereRecherche . '\''; ?>,this.checked)"  <?php

                                      // Vérifier si le festival est deja dans la liste des festivals
                                      if (in_array($spectacle['idSpectacle'], $spectacleIDs)) {
                                          echo 'checked';

                                      }

                                      ?>>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="pagination">
                    <?php for ($page = 1; $page <= $nbPages; $page++) { ?>
                        <a
                            href="?controller=Festival&action=modifierListeSpectacleFestival&page=<?php echo $page; ?>&idFestival=<?php echo $idFestival; ?>&derniereRecherche=<?php echo $derniereRecherche; ?>">
                            <?php echo $page; ?>
                        </a>
                    <?php }
            } else {
                echo '<div class="col-12">';
                echo '<div class="centre">';
                echo "<h1>Il n'y a pas de spectacle correspondant a votre recherche.</h1>";
                echo '</div>';
                echo '</div>';
            } ?>
            </div>

        </div>
        <div class="footer">
            <a href="?controller=Festival&action=afficherFestival&idFestival=<?php echo $idFestival; ?>"><button
                    type="button" class="btn btn-gris">Retour</button></a>
        </div>
    </form>
    <script src="js/script.js"></script>
</body>

</html>