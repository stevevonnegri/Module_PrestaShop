<?php

class mhsc_gitdetailproduct extends Module
{

    public function __construct() 
    {
        $this->name = 'mhsc_gitdetailproduct';        
        $this->tab = 'administration';  
        $this->version = '0.1.0';
        $this->author = 'Von Negri Steve';      
        
        $this->displayName = 'Champ "detail des tailles" au produit';
        $this->description = 'Ajout d\'un champ detail des tailles au produit lors de l\'ajout ou de la modification'; 

        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        //registerHook permet de d'accrocher notre module a un hook
        //si celui-ci n'existe pas, il crée le hook automatiquement
        if( !parent::install()
            || !$this->installdb()
            || !$this->registerHook(['displayAdminProductsMainStepLeftColumnBottom',
                                    'displayTailleProduit'])
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
            ALTER TABLE "._DB_PREFIX_."product
            ADD mhsc_git tinyint(1) UNSIGNED DEFAULT '0'"
        );
        return $sql;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * Suppression des champs
     * @return boolean
     */
    protected function _unInstallSql() {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
                 . "DROP mhsc_git";
          
         $returnSql = Db::getInstance()->execute($sqlInstall);
         
         return $returnSql;
     }


    public function hookDisplayAdminProductsMainStepLeftColumnBottom($params) 
    {

        $product = new Product($params['id_product']);

        // Tools::dieObject($product);

        $this->context->smarty->assign(array(
        'mhsc_git' => $product->mhsc_git,
        )
        );

        return $this->display(__FILE__, 'views/templates/hook/extrafields.tpl');
    }

    public function hookDisplayTailleProduit($params)
    {
        $product = new Product(intval($_GET['id_product']));

        $this->context->smarty->assign([
            'product' => $product,
        ]);
        
        return $this->display(__FILE__, 'views/templates/hook/taille.tpl');
    }
     

}
