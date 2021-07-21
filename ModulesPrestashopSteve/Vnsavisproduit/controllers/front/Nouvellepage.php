<?php

//Nom de la class pour un module overRide [nomTechnique][NomFichier]ModuleFrontController
class VnsAvisProduitNouvellePageModuleFrontController extends ModuleFrontController 
{

    //attribuer les variables smarty et charge le TPL
    public function initContent()
    {
        parent::initContent();

        $id_product = Tools::getValue('id_product');
        $id_lang = $this->context->language->id; 
        $product = new Product($id_product, true, $id_lang);

        // Tools::dieObject($product);

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('avis_produit');
        $sql->where('id_product = '.$id_product);
        $messagesProduct = Db::getInstance()->executeS($sql);


        $this->context->smarty->assign([
            'product' => $product,
            'messagesProduct' => $messagesProduct
        ]);

        //Fonciton pour appeler smarty
        //setTemplate   module:[nomModule]/views/templates/front/[nomFichier].tpl
        $this->setTemplate('module:Vnsavisproduit/views/templates/front/nouvelle_page.tpl');

    }



}