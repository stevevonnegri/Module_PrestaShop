<?php

require_once(_PS_ROOT_DIR_ . '/modules/mhsc_clubmembre/classes/Clubmembre.php');

class mhsc_clubmembre extends Module
{

    public function __construct() 
    {
        $this->name = 'mhsc_clubmembre';        
        $this->tab = 'administration';  
        $this->version = '0.1.0';
        $this->author = 'Von Negri Steve';      
        
        $this->displayName = 'Membre du MHSC';
        $this->description = 'Membre du MHSC'; 

        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        if( !parent::install()
            || !$this->installOrderState($this->l('Commande carte membre validé'))
            || !$this->installDb()
            )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    //Function qui s'execute que lors de la d'installation
    public function uninstall()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'clubmembre');

        return parent::uninstall();
    }

    //Create BDD for module
    public function installDb()
    {
        
        $sql = Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'clubmembre (
                id_clubmembre INT UNSIGNED NOT NULL AUTO_INCREMENT,
                prenom TEXT NOT NULL,
                nom TEXT NOT NULL,
                email TEXT NOT NULL,
                id_client INT,
                PRIMARY KEY (id_clubmembre)
            ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');

        return $sql;
    }

    public function installOrderState($name)
    {

        $orders = OrderState::getOrderStates($this->context->language->id);
        $array = NULL;

        //Vérifie si l'orderState existe
        for ($i=0; $i < count($orders); $i++) { 
            if(in_array($name, $orders[$i]) == true)
            {
                $array .= $i; 
            }
        }

        if (empty($array)) {
            $order = new OrderState();

            $languages = Language::getLanguages(false);

            foreach ($languages as $value) {
                $order->name[ $value['id_lang'] ] = $name;
            }

            $order->delivery = true;
            $order->pdf_delivery = true;
            $order->paid = true;
            $order->send_email = true;
            $order->template = 'membre';
            $order->module_name = 'mhsc_clubmembre';

          
            return $order->add();
        } else {
            return true;
        }
        
    }

    public function getContent()
    {
        
		if(Tools::isSubmit('mhsc_clubmembre')){ // test si un formulaire est envoyé donc l'attribut name du bouton = submit_llfirstmodule

            $output = NULL;

            $vide = Tools::getValue('vider');
            $file = Tools::getValue('file');


            if (pathinfo($file)['extension'] == 'csv')
            {
                if(!move_uploaded_file($_FILES['file']['tmp_name'],dirname(__FILE__).'/views/assets/fichier/'.$file)){

                    $output .= $this->displayError($this->l('Erreur lors du transfert de fichier'));
                }
                else
                {
                    
                    $lines = file(dirname(__FILE__).'/views/assets/fichier/'.$file);

                    foreach($lines as $n => $line){

                        $data = explode(';', $line);

                        // Suppr le \r\t a la fin de la ligne
                        $data['2'] = substr($data['2'], 0, -2);

                        $liste = Customer::getCustomersByEmail(trim($data['2']));
                        $listeM = Clubmembre::getItemByMail(trim($data['2']));

                        if ($listeM['0']['id_clubmembre'] == NULL ) 
                        {
                            $membre = new Clubmembre();
                            $membre->prenom = trim($data['0']);
                            $membre->nom = trim($data['1']);
                            $membre->email = trim($data['2']);

                            if($liste['0']['id_customer'] != NULL) 
                            {
                                $customer = new Customer($liste['0']['id_customer']);
                                $customer->addGroups(['6']);
                                $customer->id_default_group = '6';
                                $customer->update();

                                $membre->id_client = $customer->id;

                            } else {
                                $context = Context::getContext();
                                $lien = $context->link->getModuleLink('mhsc_clubmembre', 'inscription');
                                $dataM = [
                                    '{firstname}' => $membre->prenom,
                                    '{lastname}' => $membre->nom,
                                    '{lien}' => $lien
                                ];
                                Mail::send(
                                    (int)$this->context->language->id,
                                    'inscription',
                                    'Inscrition au club des membres MHSC',
                                    $dataM,
                                    $membre->email,
                                    $membre->prenom . ' ' . $membre->nom,
                                );
                            }
                            $membre->add();

                        } else {
                            $n++;
                            $output .= $this->displayWarning('Email déjà présent dans les membres du club, ligne '.$n);
                        }

                    }

                    $output .= $this->displayConfirmation('Fichier d\'abonnées inscrits');
                        
                }

                unlink(dirname(__FILE__).'/views/assets/fichier/'.$file);
                    
            } elseif($vide == 1) {
                $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'customer_group WHERE id_group = 6');
                foreach ($data as $item) {
                    $id_customer= new Customer($item['id_customer']);
                    $id_customer->cleanGroups();
                    $id_customer->addGroups(['3']);
                    $id_customer->id_default_group = '3';
                    $id_customer->update();
                }     
                $output .= $this->displayConfirmation('Toutes les membres ont bien été supprimés');
            } else {
                $output .= $this->displayError('Erreur dans la suppression');
            }
		}

                                    
        $context = Context::getContext();
        $lien = $context->link->getModuleLink('mhsc_clubmembre', 'inscription');

        $html = '<a href="'.$lien.'">Page redirect</a>';
		return $html.$output.$this->displayForm();
    }


    public function displayForm(){

		$form_configuration['0']['form'] = [
			'legend' => [
				'title' => 'Configuration',
			],
			'input' => [
                [
                    'type' => 'file',
                    'label' => $this->l('fichier csv'),
					'name' => 'file',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Retirer toutes les clients du club membre'),
					'name' => 'vider',
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
                            'label' => $this->l('Non'),
                            'defauld'
                        ]
                    ]
                    
                ]
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
		$helper->submit_action = 'mhsc_clubmembre';

		return $helper->generateForm($form_configuration);

	}

}
