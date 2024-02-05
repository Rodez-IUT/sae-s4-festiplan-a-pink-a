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
    <title>Creer un Festival</title>
    <link href="festiplan/static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="festiplan/static/css/index.css"/>
    <link href="festiplan/static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>

<!-- En tête -->
<header>
    <div class="container-fluid header">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="festiplan/static/images/logo_noir.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="col-8">
                <h2 class="texteCentre blanc bas"> Créer un festival : </h2>
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
        <input type="hidden" name="action" value="nouveauOuModificationFestival">
        <input type="hidden" name="modifier" value="false">

        <div class="padding">
            <div class="row">
                <div class="col-12">
                    <div class="form-group texteGauche">
                        <label id="<?php if(!$nomOk){echo 'invalide';}?>">Nom :</label>
                        <br>
                        <input name="nom" type="text" placeholder="(35 caractères maximum)" value="<?php if($nomOk){echo $ancienNom;}?>" required class="input-style">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group texteGauche">
                        <label id="<?php if(!$descOk){echo 'invalide';}?>">Description :</label>
                        <br>
                        <textarea name="description" placeholder="(1000 caractères maximum)" required class="textarea-style"><?php if($descOk){echo $ancienneDesc;}?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group texteGauche">
                        <label id="<?php if(!$dateOk){echo 'invalide';}?>">Date de début :</label>
                        <br>
                        <input name="dateDebut" type="date" value="<?php if($dateOk){echo $ancienneDateDebut;}?>" required class="input-style">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group texteGauche">
                        <label id="<?php if(!$dateOk){echo 'invalide';}?>">Date de fin :</label>
                        <br>
                        <input name="dateFin" type="date" value="<?php if($dateOk){echo $ancienneDateFin;}?>" required class="input-style">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group texteGauche">
                        <label>Liste categorie :</label>
                        <br>
                        <select name="categorie" required class="input-style">
                            <?php
                            while ($row = $searchStmt -> fetch()) {?>
                                <option value="<?php echo $row['idCategorie'];?>"  <?php if ($row['idCategorie'] == $ancienneCategorie) { echo 'selected';}?>  ><?php echo $row['nom'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid footer">
        <div class="row">
                <div class="col-6">
                    <a href="?controller=Home"><button type="button" class="btn btnModif btn-secondary fondGris"><span class="fas fa-solid fa-arrow-left-long"></span></button></a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btnModif btn-primary fondBleu"><span class="fas fa-solid fa-check"></span></button>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
