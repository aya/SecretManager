
$(document).ready(function(){
    // Masque la modale quand on clique un objet de class "close"
     $(".close").on('click', function() {
        $('#afficherSecret').hide();
    });

    // Change couleur si vide ou plein des champs obligatoires
    $('input.obligatoire').focusout(function(){
        if ($(this).val() != '') {
            $(this).css("border", "1px solid #cdcdcd");
        } else {
            $(this).css("border", "2px solid #00608d");
        }
    });
});


// Traite l'affichage d'un secret.
function viewPassword( scr_id ){
    if ($('#inputlabel').val() != '') {
        $.ajax({
            url: '../../SM-secrets.php?action=SCR_V', // le nom du fichier indiqué dans le formulaire
            type: 'POST', // la méthode indiquée dans le formulaire (get ou post)
            data: $.param({'scr_id': scr_id}),
            dataType: 'json',
            success: function(reponse) {
                $('#afficherSecret').show('slow');

                var resultat = new Array();

                $.each(reponse, function(attribut, valeur) {
                    resultat[attribut]=valeur;
                });

                var statut = resultat['Statut'];

                if (statut == 'succes') {
                    $('#detailSecret').text(resultat['password']);
                }
                else if (statut == 'erreur') {
                    $('#detailSecret').text(resultat['Message']);
                }

            },
            error: function(reponse) {
                var resultat = new Array();

                $.each(reponse, function(attribut, valeur) {
                    resultat[attribut]=valeur;
                });

                alert('Erreur serveur : ' + resultat['responseText']);
            }
        });
    }
}