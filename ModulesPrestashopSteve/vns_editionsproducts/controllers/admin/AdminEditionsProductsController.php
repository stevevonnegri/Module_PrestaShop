<?php

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShop\PrestaShop\Adapter\Entity\StockAvailable;

require_once(_PS_ROOT_DIR_.'/modules/vns_editionsproducts/classes/editionsproducts.php');


//nom du fichier controller admin : AdminNomDuFichierController
//Class du meme nom que le fichier
class AdminEditionsProductsController extends ModuleAdminController
{

    public function __construct()
    {

        $this->table = 'product';
        $this->className = 'editionsproducts';

        parent::__construct();

        $this->fields_list = [
            'id_product' => [
                'title' => $this->l('ID'), //nom de la colonne
            ],
            'produit_name' => [
                'title' => $this->l('Noms')
            ],
            'marque' => [
                'title' => $this->l('Marques')
            ],
            'categorie' => [
                'title' => $this->l('Categories')
            ],
            'quantite' => [
                'title' => $this->l('Quantites')
            ],
            'price' =>[
                'title' => $this->l('Prix HT')
            ],
        ];
        $this->bootstrap = true;

        //Configurateur du module
        $infoModule['prix'] = Configuration::get('EDITIONSPRODPRIX');
        $infoModule['stock'] = Configuration::get('EDITIONSPRODSTOCKS');
        $infoModule['cat'] = Configuration::get('EDITIONSPRODCAT');
        $infoModule['activ'] = Configuration::get('EDITIONSPRODACTIV');
        $infoModule['marque'] = Configuration::get('EDITIONSPRODMARQUE');

        //Recuperation categories prod si options activer
        if ($infoModule['cat'] == 1) {
            $categorie = new Category();

            $tabCategorie = $categorie->getCategories();
            $tabCat2 = $tabCategorie['2'];

            //retire le element du tableau qui nous sert pas
            unset($tabCategorie['0']);
            unset($tabCategorie['1']);
            unset($tabCategorie['2']);

            $this->context->smarty->assign([
                'listcat' => $tabCategorie,
                'listCategories' => $tabCat2,
            ]);
        }

        //Recuperation categories prod si options activer
        if ($infoModule['marque'] == 1) {

            $manufacturer = new Manufacturer();
            $marque = $manufacturer->getManufacturers();
            
            $this->context->smarty->assign([
                'listmarques' => $marque,
            ]);
        }


        $this->context->smarty->assign(
            [
                'infoPrix' => $infoModule['prix'],
                'infoStock' => $infoModule['stock'],
                'infoCat' => $infoModule['cat'],
                'infoActiv' => $infoModule['activ'],
                'infoMarque' => $infoModule['marque'],

            ]
        );


        // bulk action
        $this->bulk_actions = array(
            'monaction' => array(
                'text' => $this->l('Mon action'),
                'icon' => 'icon-circle-blank',
                // 'confirm' => $this->l('Message de confirmation')
            )
        );

    }


    public function renderList(){

        $this->_select = "pl.name as produit_name, st.quantity as quantite, ma.name as marque, cat.name as categorie";
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'product_lang pl on (a.id_product = pl.id_product AND pl.id_lang = '.$this->context->language->id.') LEFT JOIN '._DB_PREFIX_.'stock_available st on (a.id_product = st.id_product AND st.id_product_attribute = 0) LEFT JOIN '._DB_PREFIX_.'manufacturer ma on (a.id_manufacturer = ma.id_manufacturer) LEFT JOIN '._DB_PREFIX_.'category_lang cat on (a.id_category_default = cat.id_category AND pl.id_lang = '.$this->context->language->id.')';

        // //Rajouter un btn action
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        

        $html = "";

        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'vns_editionsproducts/views/templates/admin/help/formModuleEdit.tpl');

        return parent::renderList().$html;

    }


    //processUpdate et ProcessAdd : méthode qui s'executent avant la mise à jour ou l'ajout mais après child_validation
	public function postProcess()
    {

        if(Tools::isSubmit('submit-Module')){

            $produits  = Tools::getValue('produits');

            $config = Tools::getValue('config');

            //Gestion prix        
            if($config == 'prix'){

                //Recuperer le changement ( positif ou negatif)
                $prix = Tools::getValue('VolumeP');

                //Recuperer la valeur
                $prixAjout = Tools::getValue('prixValeur');

                //Recupere la modification à apporter
                $modif = Tools::getValue('typeAugmentation');

                foreach ($produits as $id) 
                {

                    $product = new Product($id);
                    $prixAvant = $product->price;

                    if ($prix == '+' ){

                        if ($modif == '%') 
                        {

                            $prixApres = $prixAvant * (100 + $prixAjout) / 100;

                        } elseif ($prix == 'entier') {
                            $prixApres = $prixAvant + $prixAjout;
                        }

                    } elseif ($prix == '-'){

                        if ($modif == '%') 
                        {

                            $prixApres = $prixAvant * (100 - $prixAjout) / 100;

                        } elseif ($prix == 'entier') {

                            $prixApres = $prixAvant - $prixAjout;

                        }
                    }

                    $product->price = $prixApres;
                    
                    $product->update();
                }

            // Gestion Stock
            } elseif ($config == 'stock') {

                //Recuperer le changement ( positif ou negatif)
                $volume = Tools::getValue('VolumeS');

                //Recuperer la valeur
                $quantite = Tools::getValue('stockValeur');

                foreach ($produits as $id) 
                {
                    $stock = new StockAvailable($id);

                    $volumeAvant = StockAvailable::getQuantityAvailableByProduct($id);

                    if ($volume == '+' ){
                        $volumeApres = $volumeAvant + $quantite;
                    } elseif ($volume == '-'){
                        $volumeApres = $volumeAvant - $quantite;
                    }

                    $stock->quantity = $volumeApres;
                    $stock->update();
                }

                // Geston de suppression
            } elseif ($config == 'categories') {

                $categ[] = Tools::getValue('categ');
                
                foreach ($produits as $id) 
                {
                    $product = new Product($id);
                    $product->updateCategories($categ);
                    $product->id_category_default = $categ[0];
                    $product->update();
                }

            // Gestion categorie
            } elseif ($config == 'marque') {

                $marque = Tools::getValue('marque');

                foreach ($produits as $id) 
                {
                    $product = new Product($id);
                    $product->id_manufacturer = $marque;
                    $product->update();
                }

            // Gestion delete
            } elseif ($config == 'delete') {

                foreach ($produits as $id) 
                {
                    $product = new Product($id);
                    $product->delete();
                }
            }

        }

        // categorie : objet $product function  updatecategory()
        // marque :

        parent::postProcess();
        
    }


    // Bulk pour recupere l'id des produits
    protected function processBulkMonAction()  { 

        $this->context->smarty->assign(array(
            'produits' => $this->boxes
        ));

    }

    public function initHeader() {
        
        //Tools::dieObject(_MODULE_DIR_.'templates/admin/help/test.tpl');
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'vns_editionsproducts/views/templates/admin/help/formModuleEdit.tpl').parent::initHeader();
    }

    public function initFooter() {
        
        //Tools::dieObject(_MODULE_DIR_.'templates/admin/help/test.tpl');
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'vns_editionsproducts/views/templates/admin/help/formModuleEdit.tpl').parent::initFooter();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        // $this->context->controller->addCSS(__PS_BASE_URI__ .'modules/everpspopup/views/css/everpspopup.css', 'all');
        $this->context->controller->addJS(__PS_BASE_URI__ .'modules/vns_editionsproducts/views/js/formEdit.js', 'all');
        $this->context->controller->addJS(__PS_BASE_URI__ .'modules/vns_editionsproducts/views/js/formEditCat.js', 'all');

    }

}