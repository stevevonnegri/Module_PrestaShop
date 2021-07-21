<?php

class vns_editionsproducts extends Module
{

    public function __construct() 
    {
        //Nom technique du module
        $this->name = 'vns_editionsproducts';        
        //Nom afficher dans le back office
        $this->displayName = 'Edition de produit en masse';
        //Déclarer la categorie du module developper    
        $this->tab = 'administration';  
        //Version du module            
        $this->version = '0.1.0';
        //auteur du module              
        $this->author = 'Von Negri Steve';      
        //description du module              
        $this->description = 'Modules d\'edition de masse pour les produits sur boutique prestashop'; 
        //activation de bootstrap
        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        //registerHook permet de d'accrocher notre module a un hook
        //si celui-ci n'existe pas, il crée le hook automatiquement
        if( !parent::install()
        || !$this->installTab('AdminCatalog', 'AdminEditionsProducts', 'Editions des produits') 
        )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    //fonction d'ajout d'un nouvelle ongle dans le backoffice
    //param: controller parent, nom du controller, nom public
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
        Configuration::deleteByName('EDITIONSPRODPRIX');
        Configuration::deleteByName('EDITIONSPRODSTOCKS');
        Configuration::deleteByName('EDITIONSPRODCAT');
        Configuration::deleteByName('EDITIONSPRODMARQUE');


        return parent::uninstall();
    }


    public function getContent()
    {

        $output = NULL;

        //test so un formulaire est envoyé dans l'attribut name du bouton
        if(tools::isSubmit('submit_Vns_editionsProducts'))
        {
            //Récupere les valeurs envoyés en post ou en get
            $EditionProd['prix'] = tools::getValue('EDITIONSPRODPRIX');
            $EditionProd['stock'] = tools::getValue('EDITIONSPRODSTOCKS');
            $EditionProd['cat'] = tools::getValue('EDITIONSPRODCAT');
            $EditionProd['marque'] = tools::getValue('EDITIONSPRODMARQUE');



            Configuration::updateValue('EDITIONSPRODPRIX', $EditionProd['prix']);
            Configuration::updateValue('EDITIONSPRODSTOCKS', $EditionProd['stock']);
            Configuration::updateValue('EDITIONSPRODCAT', $EditionProd['cat']);
            Configuration::updateValue('EDITIONSPRODMARQUE', $EditionProd['marque']);


            // Message de confirmation
            $output .= $this->displayConfirmation($this->l('Action effectuer'));

        } else 
        {
            //affiche un message d'erreur
            $output .= $this->displayError($this->l('Echec'));

        }
        return $output.$this->displayForm();
    }

    //method qui permet de créer un formulaire via HelperForm
    public function displayForm()
    {
        //tableau qui contient les informations du formulaire
        $form_configuration['0']['form'] = [
            'legend' => 
            [
                'title' => 'Configurateur de l\'éditeur de produit',
            ],
            'input' => 
            [
                [
                    'type' => 'switch',
                    'label' => $this->l('modifications des prix en HT'),
                    'name' => 'EDITIONSPRODPRIX',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Oui')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Non')
                        ]
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Augmenter ou diminuer les stocks des produits'),
                    'name' => 'EDITIONSPRODSTOCKS',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Oui')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Non')
                        ]
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Changer de categorie la selection de produit'),
                    'name' => 'EDITIONSPRODCAT',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Oui')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Non')
                        ]
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Changer la marque des produits'),
                    'name' => 'EDITIONSPRODMARQUE',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Oui')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Non')
                        ]
                    ]
                ],

            ],
            'submit' =>
            [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default btn-info pull-right'
            ]
        ];

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        //Genere le liens d'action du formulaire, la page qui traite les informations
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_langugage = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->title = $this->displayName;

        //genere l'attribut name du bouton action
        $helper->submit_action = 'submit_Vns_editionsProducts';

        //permet de recupere la valeur des mes champs inputs
        //autant de fields_value que de champs inputs
        $helper->fields_value['EDITIONSPRODPRIX'] = Tools::getValue('EDITIONSPRODPRIX', Configuration::get('EDITIONSPRODPRIX'));
        $helper->fields_value['EDITIONSPRODSTOCKS'] = Tools::getValue('EDITIONSPRODSTOCKS', Configuration::get('EDITIONSPRODSTOCKS'));
        $helper->fields_value['EDITIONSPRODCAT'] = Tools::getValue('EDITIONSPRODCAT', Configuration::get('EDITIONSPRODCAT'));
        $helper->fields_value['EDITIONSPRODMARQUE'] = Tools::getValue('EDITIONSPRODMARQUE', Configuration::get('EDITIONSPRODMARQUE'));


  
        return $helper->generateForm($form_configuration);

    }

}