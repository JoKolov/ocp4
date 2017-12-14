$(document).ready(function() {
    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    var $container = $('div#jni_ticketingbundle_invoice_visitors');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $container.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_visitor').click(function(e) {
      addVisitor($container);
      e.preventDefault(); // évite qu'un # apparaisse dans l'URL
      return false;
    });

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un.
    if (index == 0) {
      addVisitor($container);
    } else {
      // S'il existe déjà des visiteurs, on ajoute un lien de suppression pour chacune d'entre elles
      // et on réaffecte le bon texte aux label Visiteurs
      var visitorId;
      $container.children('div').each(function() {
        addDeleteLink($(this));
        visitorId = parseInt($(this).children('.visitor-del-btn + label').text()) + 1;
        $(this).children('.visitor-del-btn + label').html('Visiteur n°' + visitorId);
      });
    }

    // La fonction qui ajoute un formulaire VisitorType
    function addVisitor($container) {

      // Dans le contenu de l'attribut « data-prototype », on remplace :
      // - le texte "__name__label__" qu'il contient par le label du champ
      // - le texte "__name__" qu'il contient par le numéro du champ
      var template = $container.attr('data-prototype')
        .replace(/__name__label__/g, 'Visiteur n°' + (index+1))
        .replace(/__name__/g,        index)
      ;

      // On crée un objet jquery qui contient ce template
      var $prototype = $(template);
      // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
      addDeleteLink($prototype);
      // On ajoute le prototype modifié à la fin de la balise <div>
      $container.append($prototype);
      // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
      index++;
    }

    // La fonction qui ajoute un lien de suppression d'un visiteur
    function addDeleteLink($prototype) {
      // Création du lien
      var $deleteLink = $('<a href="#" class="btn btn-form-action visitor-del-btn pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>');

      // Ajout du lien
      $prototype.prepend($deleteLink);

      // Ajout du listener sur le clic du lien pour effectivement supprimer un visiteur
      $deleteLink.click(function(e) {
        $prototype.remove();
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
      });
    }
  });