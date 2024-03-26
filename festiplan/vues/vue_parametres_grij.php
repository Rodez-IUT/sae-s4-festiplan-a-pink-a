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
                <h2 class="texteCentre blanc bas"> Paramétrage de la planification </h2>
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
    <div class="padding">
        <div class="row">
            <?php
            if (isset($message)) {
                ?>
                <div class="col-12">
                    <p id="invalide">
                        <?php echo $message; ?>
                    <p>
                </div>
                <?php
            } ?>
            <div class="col-12">
                <form method="post" action="index.php">
                    <input type="hidden" name="controller" value="Grij" />
                    <input type="hidden" name="action" value="enregistrerGrij" />
                    <div class="row">
                        <?php if (isset($idFestival)){?>
                        <input type="hidden" name="idFestival" value="<?php echo $idFestival; ?>" />
                        <?php } ?>
                        <br>
                        <div class="col-12">
                            <label for="heureDebut">Heure de début :</label>
                        </div>
                        <div class="col-12">
                            <input type="time" name="heureDebut" value="<?php
                                if (isset($heureDebut)){
                                    echo $heureDebut;
                                }?>" class="input-style" />
                        </div>
                        <br>
                        <div class="col-12">
                            <label for="heureFin">Heure de fin :</label>
                        </div>
                        <div class="col-12">
                            <input type="time" name="heureFin" value="<?php
                                echo isset($heureFin) ? $heureFin : "";
                                ?>" class="input-style" />
                        </div>
                        <br>
                        <div class="col-12">
                            <label for="ecartEntreSpectacles">Écart entre chaque spectacle :</label>
                        </div>
                        <div class="col-12">
                            <input type="time" name="ecartEntreSpectacles" value="<?php
                            echo isset($ecartEntreSpectacles) ?
                                $ecartEntreSpectacles :
                                "";
                            ?>" class="input-style">
                        </div>
                    </div>
                    <div class="container-fluid footer">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <a href="?controller=Home"><button type="button"
                                        class="btn btnModif btn-secondary fondGris"><span
                                            class="fas fa-solid fa-arrow-left-long"></span></button></a>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="submit" class="btn btnModif btn-primary fondBleu"><span
                                        class="fas fa-solid fa-check"></span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>