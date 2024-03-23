<?php
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_connecte']) || $_SESSION['utilisateur_connecte'] == false) {
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Profil</title>
    <link href="static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/index.css" />
    <link href="static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>
<header>
    <div class="container-fluid header-blanc">
        <div class="row">
            <div class="col-3 col-md-2">
                <a href="index.php">
                    <img src="static/images/logo_blanc.png" alt="Logo Festiplan" class="logo-festiplan">
                </a>
            </div>
            <div class="offset-8 col-1 col-md-2 text-right"> <!-- Ajoutez la classe text-right pour aligner à droite -->
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

<body>
    <div class="container">
        <div class="cadreUtilisateur connexion">
            <div class="row">
                <h2 class="grand col-12">Informations sur le compte</h2>
            </div>
            <br>

            <div class="form-group">
                <div class="input-group">
                    <p>Prénom :
                        <?php echo $ancienPrenom; ?>
                    <p>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <p>Nom :
                        <?php echo $ancienNom; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <p>Login :
                        <?php echo $ancienLogin; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <p>Email :
                        <?php echo $ancienEmail; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>