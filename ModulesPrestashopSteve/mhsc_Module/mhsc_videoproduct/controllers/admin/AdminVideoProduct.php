<?php


class AdminVideoProductController extends ModuleAdminController
{

    public function __construct()
    {

        $this->table = 'mhsc_video';
        $this->className = 'mhsc_video';

        parent::__construct();

        $this->fields_list = [
            'id_mhsc_video' => [
                'title' => $this->l('ID'), //nom de la colonne
            ],
            'produit_name' => [
                'title' => $this->l('Noms')
            ],
            'name' => [
                'title' => $this->l('Nom de la video')
            ],
        ];
        $this->bootstrap = true;

        
		$this->addRowAction('edit');
        $this->addRowAction('delete');

    }


    public function renderList()
    {

        $this->_select = "p.id_product as id_product, pl.name as produit_name";
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'product p ON (a.id_products = p.id_product) LEFT JOIN '._DB_PREFIX_.'product_lang pl on (p.id_product = pl.id_product AND pl.id_lang = '.$this->context->language->id.')';

        return parent::renderList();

    }


    // méthode pour le formulaire d'edition
	public function renderForm(){

        $id_lang = $this->context->language->id;

        $products = Product::getProducts($id_lang, '0', 'NO LIMIT', 'id_product', 'ASC' );

        
        $produit = new mhsc_video($_GET['id_mhsc_video']);

        // Tools::dieObject($this->context->link->getAdminLink('AdminVideoProduct'));
        
        $this->context->smarty->assign([
            'products' => $products,
            'href' => self::$currentIndex.'&token='.$this->token,
            'video' => $produit,
            'ajaxUrl' => $this->context->link->getAdminLink('AdminVideoProduct'),
            'token' => $this->token,
            ]);

            $this->addJS(_MODULE_DIR_ .'mhsc_videoproduct/views/js/admin.js');

            $this->addJS('https://code.jquery.com/ui/1.12.1/jquery-ui.js');

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'mhsc_videoproduct/views/templates/admin/tabs.tpl'
        );



	}

    //processUpdate et ProcessAdd : méthode qui s'executent avant la mise à jour ou l'ajout mais après child_validation
	public function postProcess()
    {

        if (Tools::isSubmit('submit_mhsc_video')) {

            $videoName = $_FILES['video']['name'];
            $id_product = Tools::getValue('id_product');
            $id = Tools::getValue('id_product_redirected');
            $actif = Tools::getValue('active');

            $video = new mhsc_video();

            if (Validate::isFloat($id_product) || Validate::isFloat($id)) 
            {

                //Test si il existe une video pour le produit, et la supprime si oui
                if (!empty($id_product)) {

                    $result = mhsc_video::getItemById_product($id_product);
                    $product = new Product($id_product);
                    if ($product->name == NULL) {
                        return $this->error =  Tools::displayError('Produit inconnu');
                    }
                    $video->id_products = $id_product;

                } elseif (!empty($id)) {

                    $result = mhsc_video::getItemById_product($id);
                    $product = new Product($id);
                    if ($product->name == NULL) {
                        return $this->error = Tools::displayError('Produit inconnu');
                    }
                    $video->id_products = $id;

                }

                if ($result != NULL) {

                    $ancien = new mhsc_video($result['0']['id_mhsc_video']);

                    if ($ancien->active != $actif && $actif != NULL && empty($_FILES['video'])) {

                        $ancien->active = $actif;
                        $ancien->updateMhsc($ancien);
                        echo 'Video désactiver';
                        return true;
                    } else {

                        unlink(_PS_MODULE_DIR_.'mhsc_videoproduct/views/assets/img/'.$ancien->name);
                        echo 'unlink    ';
                    }
                }
                
                if(!move_uploaded_file($_FILES['video']['tmp_name'], 
                    _PS_MODULE_DIR_.'mhsc_videoproduct/views/assets/img/'.$video->id_products.'-'.$videoName))
                {
                    return $this->error = Tools::displayError('erreur transfere de fichier');
                }
                else 
                {

                    $video->name = $video->id_products.'-'.$videoName;
                    $video->active = 1;

                    if ($actif != NULL) {
                        $video->active = $actif;
                    }

                    if(empty($result))
                    {
                        $video->add();
                        echo 'add';
                    }
                    else
                    {
                        $video->id_mhsc_video = $result['0']['id_mhsc_video'];
                        $video->updateMhsc($video);
                        echo ' update';
                    }

                }

            } else {

                echo 'erreur validation';
 
            }
        }
        

        parent::postProcess();
        
    }

    public function ajaxProcessSearchProduct()
    {
        $saisi = Tools::getValue('q');

        $sql = 
            'SELECT p.`id_product`, pl.`name`, p.`reference`
            FROM '._DB_PREFIX_.'product as p 
            LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.`id_product` = pl.`id_product` AND pl.id_lang = '
            .(int)$this->context->language->id.')
            WHERE (
                pl.name LIKE "%'.pSQL($saisi).'%"
                OR p.reference LIKE "%'.pSQL($saisi).'%"
                OR p.id_product LIKE "%'.pSQL($saisi).'%"
            )
            AND p.active = 1 AND pl.id_shop = 1 AND pl.id_lang = 1
            GROUP BY p.id_product';

        $products = Db::getInstance()->executeS($sql);

        $formated_array = array();

        foreach ($products as $product) {

            $product['name'] = $product['name'].' ['.$product['reference'].']';
            $formated_array[] = array(
                'id_product' => $product['id_product'],
                'name' => $product['name'],
                'reference' => $product['reference']
            );
        }

        die(Tools::jsonEncode($formated_array));
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJqueryPlugin(array('autocomplete'));
    }

    public function processDelete()
    {        
        $object = new mhsc_video($_GET['id_mhsc_video']);
        unlink(_PS_MODULE_DIR_.'mhsc_videoproduct/views/assets/img/'.$object->name);

        return parent::processDelete();
        
    }
}