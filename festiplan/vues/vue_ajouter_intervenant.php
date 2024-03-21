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
    <title>Ajouter un intervenant</title>
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
                <h2 class="texteCentre blanc bas">Ajouter un intervenant</h2>
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

        <input type="hidden" name="controller" value="Spectacle">
        <input type="hidden" name="action" value="nouveauIntervenant">
        <?php if(isset($idSpectacle, $existePas)){ ?>
            <input type="hidden" name="idSpectacle" value="<?php echo $idSpectacle ?>">
            <input type="hidden" name="existePas" value="<?php echo $existePas ?>">
        <?php }?>
        <input type="hidden" name="modifier" value="false">
        <div class="padding">
            <?php
            if (isset($existePas) && $existePas) {
                echo '<h3 id="invalide">Votre intervenant existe déja</h3>';
            }
            ?>
            <div class="row">
                <div class="col-12">
                    <label name="nom">Nom de l'intervenant :</label><br>
                    <input class="input-style" type="text" name="nom" required />
                    <br>
                </div>
                <div class="col-12">
                    <label name="nom">Prénom de l'intervenant :</label><br>
                    <input class="input-style" type="text" name="prenom" required />
                    <br>
                </div>
                <div class="col-12">
                    <label name="LabelEmail">Adresse mail :</label><br>
                    <input class="input-style" type="email" name="email" size="50" required />
                    <br>
                </div>
                <div class="col-12">
                    <label>Choisissez le métier de l'intervenant :</label><br>
                    <select class="input-style" name="metierIntervenant" required>
                        <?php
                        if (isset($searchStmt)){
                            while ($row = $searchStmt->fetch()) { ?>
                                <option value="<?php echo $row['idMetierIntervenant']; ?>">
                                    <?php echo $row['metier']; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <br>
                </div>
                <div class="col-12">
                    <label>Choisissez le type d'intervenant :</label><br>
                    <select class="input-style" name="categorieIntervenant" required>
                        <option value="0" selected>Sur Scène</option>
                        <option value="1">Hors Scène</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="container-fluid footer">
            <div class="row">
                <div class="col-6">
                    <?php if(isset($idSpectacle)){ ?>
                    <a href="?controller=Spectacle&action=afficherIntervenant&idSpectacle=<?php echo $idSpectacle; ?>">
                        <button type="button" class="btn btn-secondary fondGris btnModif">
                            <span class="fas fa-solid fa-arrow-left-long"></span>
                        </button>
                    </a>
                    <?php } ?>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-primary fondBleu btnModif"><span
                            class="fas fa-solid fa-check"></span></button>
                </div>
            </div>
        </div>
    </form>
</body>

</html>