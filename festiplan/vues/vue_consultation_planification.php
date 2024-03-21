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
    <title>Planification</title>
    <link href="static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/index.css" />
    <link href="static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>
<header>
    <div class="container-fluid header">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas"> Consultation de la planification </h2>
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
    <div class="container-fluid space">
        <div class="row">
            <div class="col-md-9 col-12">
                <div class="row">
                    <?php
                    // Liste des jours du festival
                    if (isset($listeJours)){
                        while ($jour = $listeJours->fetch()) {
                            ?>
                            <div class="col-xl-4 col-md-6 col-12 ">
                                <div class="fondJour">
                                    <h3>
                                        <?php echo $jour['dateJour']; ?>
                                    </h3>
                                    <?php
                                    // Liste des spetacles du jour du festival
                                    $listeTitres = explode(',', $jour['titres']);
                                    $listeId = explode(',', $jour['idSpectacles']);
                                    $listeHeureDebut = explode(',', $jour['heureDebut']);
                                    $listeHeureFin = explode(',', $jour['heureFin']);
                                    $i = 0;
                                    foreach ($listeTitres as $titreSpectacle) {
                                        ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <?php
                                                if (isset($idFestival)) {
                                                    $idSpectacle = $listeId[$i];
                                                    $url = "?controller=Grij&action=profilSpectacleJour&idFestival=$idFestival&idSpectacle=$idSpectacle";
                                                    echo "<a href='$url' class='square-link'>";
                                                }
                                                ?>
                                                    <div class="square">
                                                        <div class="content">
                                                            <h4>
                                                                <?php echo $titreSpectacle; ?>
                                                            </h4>
                                                            <p>De :
                                                                <?php echo $listeHeureDebut[$i]; ?>
                                                            </p>
                                                            <p>À :
                                                                <?php echo $listeHeureFin[$i]; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                <?php
                                                if (isset($idFestival))
                                                echo "</a>";
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-3 col-12">
                <?php
                if (isset($infosSpectacle, $profilSpectacle) && $profilSpectacle) {
                    $spectacle = $infosSpectacle->fetch();
                    ?>
                    <div class="row fondDescSpectacle">
                        <div class="col-12">
                            <h2 class="titreSpecDesc text-truncate">
                                <?php echo $spectacle['titre']; ?>
                            </h2>
                        </div>
                        <div class="col-12">
                            Heure de début -
                            <?php echo substr($spectacle['heureDebut'], 0, -3); ?><br />
                            Heure de fin -
                            <?php echo substr($spectacle['heureFin'], 0, -3); ?>
                        </div>
                        <div class="col-12">
                            <h4>Liste des scènes adéquates</h4>
                        </div>
                        <div class="col-12">
                            <?php
                            if (isset($listeScenes)){
                                while ($scene = $listeScenes->fetch()) {
                                    ?>
                                    <div class="row">
                                        <div class="col-4">
                                            <h5 class="titreSpecDesc text-truncate">
                                                <?php echo $scene['nomScene']; ?>
                                            </h5>
                                        </div>
                                        <div class="col-4">
                                            nombre de spectateurs<br>
                                            <?php echo $scene['nbSpectateurs']; ?>
                                        </div>
                                        <div class="col-4">
                                            <b>Longitude : </b>
                                            <?php echo $scene['longitude']; ?><br />
                                            <b>Latitude : </b>
                                            <?php echo $scene['latitude']; ?>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="fondCliquerInfoSpec">
                        <h2>Cliquer sur un spectacle pour plus d'information.</h2>
                    </div>
                    <?php
                }
                echo '<div class="col-12';
                if (isset($listeSpectacleNonPlace) && $specNonPlace = $listeSpectacleNonPlace->fetch()) {
                    echo '">';
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <h3><b>Liste des spectacles non placés</b></h3>
                        </div>
                        <div class="col-6">
                            <h4>Nom</h4>
                        </div>
                        <div class="col-3">
                            <h4>Durée</h4>
                        </div>
                        <div class="col-3">
                            <h4>Cause</h4>
                        </div>
                        <div class="col-12 fondSpecNonPlace">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="text-truncate titreSpecDesc">
                                        <?php echo $specNonPlace['titre']; ?>
                                    </h5>
                                </div>
                                <div class="col-3">
                                    <?php echo $specNonPlace['duree']; ?>
                                </div>
                                <div class="col-3">
                                    <?php echo $specNonPlace['causeNonPlace']; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        while ($specNonPlace = $listeSpectacleNonPlace->fetch()) {
                            ?>
                            <div class="col-12 fondSpecNonPlace">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="text-truncate titreSpecDesc">
                                            <?php echo $specNonPlace['titre']; ?>
                                        </h5>
                                    </div>
                                    <div class="col-3">
                                        <?php echo $specNonPlace['duree']; ?>
                                    </div>
                                    <div class="col-3">
                                        <?php echo $specNonPlace['causeNonPlace']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                    }
                } else {
                echo ' fondVertSpec">';
                    ?>
                        <h3><b>Tous les spectacles ont été placés</b></h3>

                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>
    </div>
    <div class="ecartBottom"></div>
    <?php if(isset($idFestival)){ ?>
    <div class="container-fluid footerGrij">
        <div class="row">
            <div class="col-6">
                <a href="?controller=Grij&idFestival=<?php echo $idFestival; ?>">
                    <button class="btnModifierGrij">
                        Modifier la planification
                    </button>
                </a>
            </div>
            <div class="col-6">
                <a href="?controller=Festival&action=afficherFestival&idFestival=<?php echo $idFestival; ?>">
                    <button type="submit" class="btn btn-primary fondBleu">
                        Modifier le Festival
                    </button>
                </a>
            </div>
        </div>
    </div>
    <?php } ?>
</body>

</html>