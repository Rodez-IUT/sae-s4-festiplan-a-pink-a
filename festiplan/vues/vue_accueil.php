<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] == false) {
    header("Location: ../index.php");
}
// Règle par défaut :
if (!isset($afficherSpectacles)){
    $afficherSpectacles = false;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link href="static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/index.css" />
    <link href="static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>
<!--En tête-->
<header class="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas">
                    <?php if ($afficherSpectacles) {
                        echo 'Mes spectacles';
                    } else {
                        echo 'Mes festivals';
                    } ?>
                </h2>
            </div>
            <div class="col-1 col-md-2 text-right">
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
        // Affichage de la liste des spectacles
        if ($afficherSpectacles && isset($mesSpectacles)) {
            if ($mesSpectacles->rowCount() > 0) {
                ?>

                <?php
                while ($listeSpectacle = $mesSpectacles->fetch()) {
                    $idSpectacle = $listeSpectacle['idSpectacle'];
                    ?>
                    <div class="cadreFestival">
                        <div class="row">
                            <div class="col-3 col-sm-3 col-md-2 col-lg-2">
                                <div class="centreCadreSpectacle">
                                    <a
                                        href="?controller=Spectacle&action=afficherSpectacle&idSpectacle=<?php echo $idSpectacle; ?>">
                                        <?php
                                        $titre = $listeSpectacle['titre'];
                                        // Limiter le texte à 15 caractères avec une ellipse (...) à la fin
                                        echo strlen($titre) > 16 ? substr($titre, 0, 16) . '...' : $titre;
                                        ?>
                                    </a>
                                </div>
                            </div>
                            <div class="col-3 col-sm-3 col-md-2 col-lg-2 col-xl-1">
                                <div class="centreCadreFestival">

                                    <a class="centre" name="suppression"><span
                                            class="fas fa-solid fa-trash icone-calendar suppSpectacle"
                                            data-id-spectacle="<?php echo $idSpectacle; ?>"></span></a>
                                </div>
                            </div>
                            <div class="col-3 col-sm-3 col-md-2 col-lg-2 col-xl-1">
                                <div class="centreCadreFestival">
                                    <a class="centre"
                                        href="?controller=Spectacle&action=afficherIntervenant&idSpectacle=<?php echo $idSpectacle; ?>"><span
                                            class="fas fa-solid fa-users icone-calendar"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if (isset($nbPages)){
                ?>
                    <div class="pagination">
                        <?php for ($page = 1; $page <= $nbPages; $page++) { ?>
                            <a href="?controller=Home&page=<?php echo $page; ?>&afficher=<?php echo $afficherSpectacles; ?>">
                                <?php echo $page; ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php
                }
            } else {
                echo '<div class="col-12">';
                echo '<div class="centre">';
                echo '<h1>Pas de spectacle crée pour le moment</h1>';
                echo '</div>';
                echo '</div>';
            }
        } else if (isset($mesFestivals, $lesResponsables)){
            if ($mesFestivals->rowCount() > 0) {
                ?>

                <?php
                //affichage de la liste des festivals
                while ($festival = $mesFestivals->fetch()) {
                    $idFestival = $festival['idFestival'];
                    ?>
                    <div class="cadreFestival">
                        <div class="row">
                            <div class="col-4 col-sm-3 col-lg-2">
                                <a href="?controller=Festival&action=afficherFestival&idFestival=<?php echo $idFestival; ?>">
                                    <?php
                                    echo $festival['titre'] . "<br>";

                                    // Affiche le nom de l'utilisateur responsable
                                    if ($festival['responsable']) {
                                        echo "Responsable: " . $festival['nom'];
                                    } else {
                                        while ($responsable = $lesResponsables->fetch()) {
                                            if ($responsable['idFestival'] == $idFestival) {
                                                echo "Responsable: " . $responsable['nom'];
                                            }
                                        }
                                    }
                                    ?>
                                </a>
                            </div>
                            <div class="col-4 col-sm-3 col-lg-2 col-xl-1">
                                <div class="centreCadreFestival">
                                    <a class="centre" href='?controller=Grij&idFestival=<?php echo $idFestival; ?>'><span
                                            class="fas fa-solid fa-calendar-days icone-calendar"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                if (isset($nbPages)) {
                ?>
                    <div class="pagination">
                        <?php for ($page = 1; $page <= $nbPages; $page++) { ?>
                            <a href="?controller=Home&page=<?php echo $page; ?>&afficher=<?php echo $afficherSpectacles; ?>">
                                <?php echo $page; ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php
                }
            } else {
                echo '<div class="col-12">';
                echo '<div class="centre">';
                echo '<h1>Pas de festival crée pour le moment</h1>';
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
    </div>
    <div class="container-fluid footer">
        <div class="row">
            <div class="col-4">
                <a href="?controller=Spectacle"><button type="submit" class="btn btn-success btnModif fondVert"><span
                            class="fas fa-solid fa-plus"></span><b> Spectacle</b></button></a>
            </div>
            <div class="col-4">
                <a href="?controller=Accueil&action=<?php if ($afficherSpectacles) {
                    echo 'voirFestival';
                } else {
                    echo 'VoirSpectacle';
                } ?>"><button type="submit" class="btn btn-secondary btnModif fondGris">
                        <?php if ($afficherSpectacles) {
                            echo '<b>Voir mes festivals</b>';
                        } else {
                            echo '<b>Voir mes spectacles</b>';
                        } ?>
                    </button></a>
            </div>
            <div class="col-4">
                <a href="?controller=Festival"><button type="submit" class="btn btn-success btnModif fondVert"><span
                            class="fas fa-solid fa-plus"></span><b> Festival</b></button></a>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>

</html>