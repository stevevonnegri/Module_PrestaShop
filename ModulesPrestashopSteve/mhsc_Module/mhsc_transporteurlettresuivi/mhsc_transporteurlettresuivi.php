<?php

class mhsc_transporteurlettresuivi extends Module
{

    public function __construct() 
    {
        //Nom technique du module
        $this->name = 'mhsc_transporteurlettresuivi';        
        //Nom afficher dans le back office
        $this->displayName = 'Affiche back office lettre suivi';
        //Déclarer la categorie du module developper    
        $this->tab = 'administration';  
        //Version du module            
        $this->version = '0.1.0';
        //auteur du module              
        $this->author = 'Von Negri Steve';      
        //description du module              
        $this->description = 'Affiche dans le back office toutes les commandes payées et qui ont choisi comme transporteur lettre suivi'; 
        //activation de bootstrap
        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        if( !parent::install()
        || !$this->installTab('AdminParentOrders', 'AdminTransporteurLettreSuivi', 'Commande en lettre suivi') 
        )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function installTab($parent, $classController, $name)
    {
        $tab = new Tab();
        //method qui renvoie l'id de l'ongle parent en fonciton du nom de la class        
        $tab->id_parent =  (int)Tab::getIdFromClassName($parent); 
        $tab->name = array();

        //method qui recupere les languages actives du presta pour lui fournir un nom pubmlic pour chaque langue
        foreach (Language::getLanguages(true) as $lang) { 
            $tab->name[$lang['id_lang']] = $name;
        }

        $tab->class_name = $classController;
        $tab->module = $this->name;
        $tab->active = 1;

        return $tab->add();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

}