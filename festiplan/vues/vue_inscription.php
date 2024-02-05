<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Inscription</title>
    <link href="festiplan/static/bootstrap-4.6.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="festiplan/static/css/index.css"/>
    <link href="festiplan/static/fontawesome-free-6.2.1-web/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container centreVertical">
        <div class="cadreUtilisateur inscription">
            <form action="index.php" method="post">
                <input name="controller" type="hidden" value="UtilisateurCompte">
                <input name="action" type="hidden" value="creerCompteUtilisateur">
                <img src="festiplan/static/images/logo_blanc.png" alt="Festiplan Logo">
                <br><br>
                <h2 class="grand">Inscription</h2>
                <br><br>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group texteGauche">
                            <input name="nom" type="text" class="form-control <?php echo (!$nomOk) ? 
                            'placeholder-invalid' : ''; ?>" placeholder="<?php echo (!$nomOk) ? 
                            'Nom invalide !' : 'NOM'; ?>" value="" required>
                        </div>
                        <div class="form-group texteGauche">
                            <input name="prenom" type="text" class="form-control <?php echo (!$prenomOk) ? 
                            'placeholder-invalid' : ''; ?>" placeholder="<?php echo (!$prenomOk) ? 
                            'Prenom invalide !' : 'PRENOM'; ?>" value="" required>
                        </div>
                        <div class="form-group texteGauche">
                            <div class="input-group">
                                <input name="email" type="email" class="form-control <?php echo (!$emailOk) ? 
                                'placeholder-invalid' : ''; ?>" placeholder="<?php echo (!$emailOk) ? 
                                'Email invalide !' : 'ADRESSE MAIL'; ?>" value="" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-solid fa-envelope"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group texteGauche">
                            <input name="login" type="text" class="form-control <?php echo (!$loginOk) ? 
                            'placeholder-invalid' : ''; ?>" placeholder="<?php echo (!$loginOk) ? 
                            'Login invalide !' : 'LOGIN'; ?>" value="" required>
                        </div>
                        <div class="form-group texteGauche">
                            <div class="input-group">
                                <input name="mdp" type="password" class="form-control <?php echo (!$mdpOk) ? 
                                'placeholder-invalid' : ''; ?>" placeholder="<?php echo (!$mdpOk) ? 
                                'Mot de passe non conforme !' : 'MOT DE PASSE'; ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-solid fa-lock"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group texteGauche">
                            <div class="input-group">
                                <input name="confirmMdp" type="password" class="form-control <?php echo (!$confirmMdpOk) 
                                ? 'placeholder-invalid' : ''; ?>" placeholder="<?php echo (!$confirmMdpOk) ? 
                                'Mot de passe différent !' : 'CONFIRMER LE MOT DE PASSE'; ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><span class="fas fa-solid fa-lock"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="texteCentre">
                    <button type="submit" class="btn btn-primary boutonTerminer">Terminer</button>
                </div>
            </form>
            <br>
            <p class="texteCentre petit">Vous avez un compte ?  <a class="petit" href="?controller=Home">CONNECTEZ VOUS</a></p>
        </div>
    </div>
</body>
</html>
