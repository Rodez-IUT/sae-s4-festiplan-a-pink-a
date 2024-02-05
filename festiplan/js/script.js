function majListe(checkbox,idFestival,pageActuelle,derniereRecherche,estChecked) {
    // Vérifier si la checkbox est cochée
    if (estChecked) {
        window.location.href = '?controller=Festival&action=ajouterSpectacleDeFestival&spectacle=' + checkbox +'&idFestival=' + idFestival + '&pageActuelle=' + pageActuelle + '&derniereRecherche=' + derniereRecherche;
    } else {
        window.location.href = '?controller=Festival&action=supprimerSpectacleDeFestival&spectacle=' + checkbox +'&idFestival=' + idFestival+ '&pageActuelle=' + pageActuelle + '&derniereRecherche=' + derniereRecherche;
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // Sélectionnez tous les boutons de suppression par leur classe
    var boutonsSuppression = document.querySelectorAll('.suppSpectacle');
    boutonsSuppression.forEach(function(bouton) {
        bouton.addEventListener('click', function() {
            // Récupérer l'ID du spectacle à partir de l'attribut data
            var idSpectacle = bouton.getAttribute('data-id-spectacle');

            // Afficher la boîte de dialogue de confirmation
            var confirmation = confirm("Si des festivals ont programmé votre spectacle, il sera supprimé de la liste des spectacles du festival. Voulez-vous vraiment supprimer ce spectacle ?");

            // Vérifier la réponse de l'utilisateur
            if (confirmation) {
                // L'utilisateur a cliqué sur "OK"
                alert("Spectacle supprimé !");
                // Rediriger vers la page de suppression avec l'ID du spectacle
                window.location.href = '?controller=Spectacle&action=supprimerSpectacle&idSpectacle=' + idSpectacle;
            } 
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    var boutonSuppressionFestival = document.getElementById('suppressionFestival');

    boutonSuppressionFestival.addEventListener('click', function() {

        // Récupérer l'ID du spectacle à partir de l'attribut data
        var idFestival = boutonSuppressionFestival.getAttribute('data-id-festival');

        // Afficher la boîte de dialogue de confirmation
        var confirmation = confirm("Souhaitez vous vraiment supprimer votre festival ?");

        // Vérifier la réponse de l'utilisateur
        if (confirmation) {
            // L'utilisateur a cliqué sur "OK"
            alert("Festival supprimé !");
            // Rediriger vers la page de suppression avec l'ID du spectacle
            window.location.href = '?controller=Festival&action=supprimerFestival&idFestival=' + idFestival;
        } 
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Sélectionnez tous les boutons de suppression par leur classe
    var boutonsSuppression = document.querySelectorAll('.suppIntervenant');

    boutonsSuppression.forEach(function(bouton) {
        bouton.addEventListener('click', function() {
            // Récupérer l'ID du spectacle à partir de l'attribut data
            var idSpectacle = bouton.getAttribute('data-id-spectacle');

            // Récupérer l'ID du spectacle à partir de l'attribut data
            var idIntervenant = bouton.getAttribute('data-id-intervenant');

            // Afficher la boîte de dialogue de confirmation
            var confirmation = confirm("Voulez-vous vraiment supprimer cet intervenant ?");

            // Vérifier la réponse de l'utilisateur
            if (confirmation) {
                // L'utilisateur a cliqué sur "OK"
                alert("Intervenant supprimé !");
                window.location.href = '?controller=Spectacle&action=supprimerIntervenant&idIntervenant='+ idIntervenant + '&idSpectacle=' + idSpectacle;
            }
        });
    });
});


