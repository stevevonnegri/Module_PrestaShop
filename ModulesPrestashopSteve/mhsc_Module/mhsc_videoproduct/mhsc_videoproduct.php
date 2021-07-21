<?php

require_once(_PS_ROOT_DIR_ . '/modules/mhsc_videoproduct/classes/mhsc_video.php');

class mhsc_videoproduct extends Module
{

    public function __construct() 
    {
        $this->name = 'mhsc_videoproduct';        
        $this->tab = 'administration';  
        $this->version = '0.1.0';
        $this->author = 'Von Negri Steve';      
        
        $this->displayName = 'Champ video au produit';
        $this->description = 'Ajout d\'un champ video au produit lors de l\'ajout ou de la modification'; 

        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        //registerHook permet de d'accrocher notre module a un hook
        //si celui-ci n'existe pas, il crée le hook automatiquement
        if( !parent::install()
            || !$this->installdb()
            || !$this->installTab('AdminCatalog', 'AdminVideoProduct', 'Ajout d\'une video produit') 
        )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Ajout de champ dans table product 
     * @return boolean
     */
    protected function installdb() {

        $sql = Db::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mhsc_video` (
                `id_mhsc_video` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_products` INT NOT NULL,
                `name` text NOT NULL,
                `active` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id_mhsc_video`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 ;    
        ");
        return $sql;
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

        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'mhsc_video');


        return parent::uninstall();

    }

}
