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
    <title>Creer un spectacle</title>
    <link href="static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/index.css" />
    <link href="static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>

<!-- En tête -->
<header>
    <div class="container-fluid header">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas"> Créer un spectacle : </h2>
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
        <input type="hidden" name="action" value="nouveauSpectacle">
        <input type="hidden" name="modifier" value="false">

        <div class="padding">
            <div class="row">
                <div class="col-12">
                    <label id="<?php if (isset($titreOk) && !$titreOk) {
                        echo 'invalide';
                    } ?>">Titre :</label>
                    <br>
                    <input type="text" id="titre" name="titre" placeholder="(35 caractères maximum)"
                        value="<?php if (isset($titreOk, $ancienTitre) && $titreOk) {
                            echo $ancienTitre;
                        } ?>" required class="input-style">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label id="<?php if (isset($descOk) && !$descOk) {
                        echo 'invalide';
                    } ?>">Description :</label>
                    <br>
                    <textarea name="description" placeholder="(1000 caractères maximum)" required
                        class="textarea-style"><?php if (isset($descOk, $ancienneDesc) && $descOk) {
                            echo $ancienneDesc;
                        } ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label id="<?php if (isset($dureeOk) && !$dureeOk) {
                        echo 'invalide';
                    } ?>">Duree du spectacle :</label>
                    <br>
                    <input id="duree" name="duree" type="time" value="<?php if (isset($dureeOk, $ancienneDuree) && $dureeOk) {
                        echo $ancienneDuree;
                    } ?>"
                        required class="input-style">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label id="<?php if (isset($categorieOk) && !$categorieOk) {
                        echo 'invalide';
                    } ?>">Liste catégorie :</label><br>
                    <select id="categorie" name="categorie" required class="input-style">
                        <option disabled value="0">Choisissez une catégorie de spectacle</option>
                        <?php
                        while (isset($searchStmt) && $row = $searchStmt->fetch()) { ?>
                            <option value="<?php echo $row['idCategorie']; ?>"
                                <?php if (isset($ancienneCategorie) && $row['idCategorie'] == $ancienneCategorie) {
                                  echo 'selected';
                              } ?>>
                                <?php echo $row['nomCategorie']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label id="<?php if (isset($tailleOk) && !$tailleOk) {
                        echo 'invalide';
                    } ?>">Taille de surface requise :</label><br>
                    <select id="taille" name="taille" required class="input-style">
                        <option disabled value="0">Choisissez une taille de scène</option>
                        <?php
                        while (isset($search_stmt) && $row = $search_stmt->fetch()) { ?>
                            <option value="<?php echo $row['idTaille']; ?>"
                                <?php if (isset($ancienneTaille) && $row['idTaille'] == $ancienneTaille) {
                                  echo 'selected';
                              } ?>>
                                <?php echo $row['nom']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="container-fluid footer">
            <div class="row">
                <div class="col-6">
                    <a href="?controller=Home"><button type="button" class="btn btnModif btn-secondary fondGris"><span
                                class="fas fa-solid fa-arrow-left-long"></span></button></a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btnModif btn-primary fondBleu"><span
                            class="fas fa-solid fa-check"></span></button>
                </div>
            </div>
        </div>
        </div>
        </div>
    </form>
</body>

</html>