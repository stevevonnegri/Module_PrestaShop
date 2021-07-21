function change_cat()
{
    select = document.getElementById("categ");
    choice = select.selectedIndex;
    valeur = select.options[choice].value;
     
    valeur_chercher = select.options[choice].value;
    console.log(valeur_chercher)

    //Renvoyer la valeur_chercher dans le smarty formModuleEdit et la mettre
    //en key pour le second foreach.

   
}
    