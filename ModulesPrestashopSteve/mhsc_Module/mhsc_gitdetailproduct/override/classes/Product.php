<?php

class Product extends ProductCore {
    
    public $mhsc_git;
    
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null) {
        //Définition des nouveaux champs
        self::$definition['fields']['mhsc_git'] = [
            'type' => self::TYPE_BOOL,
            'required' => false,
        ];
        
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }

}