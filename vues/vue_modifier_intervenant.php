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
    <title>Modifier un intervenant</title>
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
                <h2 class="texteCentre blanc bas">Modifier un intervenant</h2>
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
    <form action="index.php" method="post">

        <input type="hidden" name="controller" value="Spectacle">
        <input type="hidden" name="action" value="nouveauIntervenant">
        <?php
        if (isset($idSpectacle)) { echo "<input type='hidden' name='idSpectacle' value='$idSpectacle'>";}
        if (isset($idIntervenant)) { echo "<input type='hidden' name='idIntervenant' value='$idIntervenant'>";}
        ?>
        <input type="hidden" name="modifier" value="true">

        <div class="padding">
            <?php
            if (isset($existePas) && $existePas) {
                echo '<h3 id="invalide">Votre intervenant existe déja</h3>';
            }
            ?>
            <div class="row">
                <div class="col-12">
                    <label name="nom">Nom de l'intervenant :</label><br>
                    <input class="input-style" type="text" name="nom" <?php echo isset($nom) ? "value='$nom'" : "";?> required />
                    <br>
                </div>
                <div class="col-12">
                    <label name="nom">Prénom de l'intervenant :</label><br>
                    <input class="input-style" type="text" name="prenom" <?php echo isset($prenom) ? "value='$prenom'" : "";?> required />
                    <br>
                </div>
                <div class="col-12">
                    <label name="LabelEmail">Adresse mail :</label><br>
                    <input class="input-style" type="email" name="email" <?php echo isset($mail) ? "value='$mail'" : "";?> size="50" required />
                    <br>
                </div>
                <div class="col-12">
                    <label>Métier intervenant :</label><br>
                    <select class="input-style" name="metierIntervenant" required>
                        <option disabled value="0">Choisissez le métier de l'intervenant</option>
                        <?php
                            while (isset($searchStmt) && $row = $searchStmt->fetch()) { ?>
                                <option value="<?php echo $row['idMetierIntervenant']; ?>"
                                    <?php if (isset($ancienMetier) && $row['idMetierIntervenant'] == $ancienMetier) {
                                      echo 'selected';
                                  } ?>>
                                    <?php echo $row['metier']; ?>
                                </option>
                                <?php
                            }
                        ?>
                    </select>
                    <?php
                    while (isset($searchStmt) && $row = $searchStmt->fetch()) { ?>
                        <?php echo $row['idMetierIntervenant']; ?>
                        <?php
                    }
                    ?>
                    <br>
                </div>
                <?php if (isset($ancienSurScene)){ ?>
                <div class="col-12">
                    <label>Intervenant sur ou hors scène :</label><br>
                    <select class="input-style" name="categorieIntervenant" required>
                        <option value="<?php echo $ancienSurScene ?>" <?php if ($ancienSurScene == 0) {
                              echo 'selected';
                          } ?>>Sur Scène</option>
                        <option value="<?php echo $ancienSurScene ?>" <?php if ($ancienSurScene == 1) {
                              echo 'selected';
                          } ?>>Hors Scène</option>
                    </select>
                </div>
                <?php }?>
            </div>
        </div>
        <div class="container-fluid footer">
            <div class="row">
                <div class="col-6">
                    <?php
                    if (isset($idSpectacle)){
                        $url = "?controller=Spectacle&action=afficherIntervenant&idSpectacle=$idSpectacle"; ?>
                        <a href="<?php echo $url ?>">
                            <button type="button" class="btn btn-secondary btnModif fondGris">
                                <span class="fas fa-solid fa-arrow-left-long">
                            </button>
                        </a>
                    <?php }?>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-primary btnModif fondBleu"><span
                            class="fas fa-solid fa-check"></span></button>
                </div>
            </div>
        </div>
    </form>
</body>

</html>