<?php

class mhsc_pubcustomer extends Module
{

    public function __construct() 
    {
        
        //Nom technique du module
        $this->name = 'mhsc_pubcustomer';        
        //Nom afficher dans le back office
        $this->displayName = 'Bandeau pub dans l\'espace client';    
        //Déclarer la categorie du module developper    
        $this->tab = 'front_office_features';  
        //Version du module            
        $this->version = '0.1.0';
        //auteur du module              
        $this->author = 'Von Negri Steve';      
        //description du module              
        $this->description = 'Bandeau pub dans l\'espace client et customisable dans le back-office'; 
        //activation de bootstrap
        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        //registerHook permet de d'accrocher notre module a un hook
        //si celui-ci n'existe pas, il crée le hook automatiquement
        if( !parent::install() 
        || !$this->registerHook('displayPubCustomer')
            )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function uninstall()
    {
        Configuration::deleteByName('MHSC_TITRE_PUB_CUSTOMER');
        Configuration::deleteByName('MHSC_DESCRIPTION_PUB_CUSTOMER');
        Configuration::deleteByName('MHSC_IMAGE_PUB_CUSTOMER');
        Configuration::deleteByName('MHSC_LIENS_PUB_CUSTOMER');
        Configuration::deleteByName('MHSC_AFFICHE_PUB_CUSTOMER');


        return parent::uninstall();
    }

    public function getContent()
    {
        
		if(Tools::isSubmit('mhsc_pub_button')){ // test si un formulaire est envoyé donc l'attribut name du bouton = submit_llfirstmodule

			$titre = Tools::getValue('MHSC_TITRE');
            $description = Tools::getValue('MHSC_DESCRIPTION');
			$image = Tools::getValue('MHSC_IMAGE');
            $liens = Tools::getValue('MHSC_LIENS');
            $affiche = Tools::getValue('MHSC_AFFICHE');


			//faire des test de données avec la class Validate
			if(Validate::isUrl($liens) && !empty($titre) && !empty($description))
            {

				$output = NULL;

                if($image || !empty($image)){

                    // Tools::dieObject(dirname(__FILE__).'/views/assets/img/'.$image);

                    if(!move_uploaded_file($_FILES['MHSC_IMAGE']['tmp_name'],dirname(__FILE__).'/views/assets/img/'.$image))
                    {
                    $output = $this->displayError($this->l('Erreur lors du transfert de fichier'));
                    }
                    else 
                    {
                        // Supprime la derniere image du serveur
                        $lastImage = Configuration::get('MHSC_IMAGE_PUB_CUSTOMER');
                        unlink(dirname(__FILE__).'/views/assets/img/'.$lastImage);

                        Configuration::updateValue('MHSC_TITRE_PUB_CUSTOMER', $titre); 
                        Configuration::updateValue('MHSC_DESCRIPTION_PUB_CUSTOMER', $description); 
                        Configuration::updateValue('MHSC_LIENS_PUB_CUSTOMER', $liens); 
                        Configuration::updateValue('MHSC_AFFICHE_PUB_CUSTOMER', $affiche);

                        Configuration::updateValue('MHSC_IMAGE_PUB_CUSTOMER',$image);
                        $output = $this->displayConfirmation('Pub enregistrée');
                    }
    
                } 
                else 
                {
                    Configuration::updateValue('MHSC_TITRE_PUB_CUSTOMER', $titre); 
                    Configuration::updateValue('MHSC_DESCRIPTION_PUB_CUSTOMER', $description); 
                    Configuration::updateValue('MHSC_LIENS_PUB_CUSTOMER', $liens); 
                    Configuration::updateValue('MHSC_AFFICHE_PUB_CUSTOMER', $affiche);

                    $output = $this->displayConfirmation('Pub valeur enregistrée');

                }
			}
			else
            {
				$output = $this->displayError($this->l('Erreur dans la saisie des informations'));
			}

		}

		return $output.$this->displayForm();
    }


    public function displayForm(){

		$form_configuration['0']['form'] = [
			'legend' => [
				'title' => 'Configuration',
			],
			'input' => [
				[
					'type' => 'text', 
					'label' => $this->l('Titre / nom de la pub'),
					'name' => 'MHSC_TITRE', 
					'size' => 20,
					'required' => true
				],
                [
					'type' => 'text',
					'label' => $this->l('Description'),
					'name' => 'MHSC_DESCRIPTION', 
					'size' => 20,
                    'maxlength' => 100,
					'required' => true
				],
				[
					'type' => 'file',
					'label' => $this->l('Image'),
					'name' => 'MHSC_IMAGE',
				],
                [
					'type' => 'text', 
					'label' => $this->l('Liens'), 
					'name' => 'MHSC_LIENS', 
					'size' => 20,
					'required' => true 
				],
                [
					'type' => 'switch',
					'label' => $this->l('Afficher la pub'), 
					'name' => 'MHSC_AFFICHE', 
                    'is_bool' => true,
                    'required' => true,
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
                    ]				],

			],
			'submit' => [
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			]
		];

		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name; // genère le lien de action du formulaire, la page qui va traité les informations
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->title = $this->displayName;
		$helper->submit_action = 'mhsc_pub_button';

		$helper->fields_value['MHSC_TITRE'] = Tools::getValue('MHSC_TITRE_PUB_CUSTOMER',Configuration::get('MHSC_TITRE_PUB_CUSTOMER'));
        $helper->fields_value['MHSC_DESCRIPTION'] = Tools::getValue('MHSC_DESCRIPTION_PUB_CUSTOMER',Configuration::get('MHSC_DESCRIPTION_PUB_CUSTOMER'));
		$helper->fields_value['MHSC_LIENS'] = Tools::getValue('MHSC_LIENS_PUB_CUSTOMER',Configuration::get('MHSC_LIENS_PUB_CUSTOMER'));
        $helper->fields_value['MHSC_AFFICHE'] = Tools::getValue('MHSC_AFFICHE_PUB_CUSTOMER',Configuration::get('MHSC_AFFICHE_PUB_CUSTOMER'));


		return $helper->generateForm($form_configuration);

	}


    public function hookDisplayPubCustomer()
    {

        $titre = Configuration::get('MHSC_TITRE_PUB_CUSTOMER');
        $description = Configuration::get('MHSC_DESCRIPTION_PUB_CUSTOMER');
        $image = Configuration::get('MHSC_IMAGE_PUB_CUSTOMER');
        $liens = Configuration::get('MHSC_LIENS_PUB_CUSTOMER');
        $affiche = Configuration::get('MHSC_AFFICHE_PUB_CUSTOMER');

        $this->context->smarty->assign([
			'titre' => $titre,
			'image' => $image,
			'description' => $description,
            'liens' => $liens,
            'affiche' => $affiche
            ]);


        return $this->display(__FILE__, 'bandeauPub.tpl');
    }


}