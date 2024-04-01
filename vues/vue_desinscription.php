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
    <title>Desinscription</title>
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
            <form action="index.php" method="post">
                <h2 class="grand">Désinscription</h2>
                <br>
                <p>Attention cette action supprimera toutes vos donnees !</p>
                <br>

                <input type="hidden" name="controller" value="UtilisateurCompte">
                <input type="hidden" name="action" value="supprimerProfil">

                <div class="form-group texteGauche">
                    <div class="input-group">
                        <input name="login" type="text"
                            class="form-control <?php echo (isset($loginOk) && !$loginOk) ? 'placeholder-invalid' : ''; ?>"
                            placeholder="<?php echo (isset($loginOk) && !$loginOk) ? 'Login invalide !' : 'LOGIN'; ?>" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fas fa-solid fa-user"></span></span>
                        </div>
                    </div>
                </div>
                <div class="form-group texteGauche">
                    <div class="input-group">
                        <input name="mdp" type="password"
                            class="form-control <?php echo (isset($mdpOk) && !$mdpOk) ? 'placeholder-invalid' : ''; ?>"
                            placeholder="<?php echo (isset($mdpOk) && !$mdpOk) ? 'Mot de passe invalide !' : 'MOT DE PASSE'; ?>"
                            required>
                        <div class="input-group-append">
                            <span class="input-group-text"><span class="fas fa-solid fa-lock"></span></span>
                        </div>
                    </div>
                    <br><br>
                    <div class="texteCentre">
                        <button type="submit" class="btn btn-danger fondRouge boutonTerminer">Se
                            désinscrire</button></a>
                    </div>
            </form>
        </div>
    </div>
</body>

</html>