function change_valeur() 
{
    select = document.getElementById("config");
    choice = select.selectedIndex;
    valeur = select.options[choice].value;
     
    valeur_chercher = select.options[choice].value; // Récupération du texte du <option> d'index
   
    collapseJs = document.querySelectorAll('.collapseJs')
    collapseJs.forEach(function (element) {
    
        element.classList.add('collapse');

    });
  
    if (valeur_chercher == 'prix') 
    {
        choix = document.getElementById('PrixModule');
        choix.classList.remove('collapse');
    }
    if (valeur_chercher == 'stock') 
    {
        choix = document.getElementById('StockModule');
        choix.classList.remove('collapse');
    }
    if (valeur_chercher == 'categories') 
    {
        choix = document.getElementById('CatModule');
        choix.classList.remove('collapse');
    }
    if (valeur_chercher == 'activation') 
    {
        choix = document.getElementById('ActivModule');
        choix.classList.remove('collapse');
    }
    if (valeur_chercher == 'marque') 
    {
        choix = document.getElementById('MarqueModule');
        choix.classList.remove('collapse');
    }
}

