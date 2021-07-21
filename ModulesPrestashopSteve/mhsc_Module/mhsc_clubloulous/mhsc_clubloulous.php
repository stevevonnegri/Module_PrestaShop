<?php

class mhsc_clubloulous extends Module
{

    public function __construct() 
    {
        $this->name = 'mhsc_clubloulous';        
        $this->tab = 'administration';  
        $this->version = '0.1.0';
        $this->author = 'Von Negri Steve';      
        
        $this->displayName = 'Club des loulous';
        $this->description = 'Club des loulous'; 

        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        if( !parent::install()
            || !$this->installOrderState($this->l('Commande club des loulous validé'))
            || !$this->installOrderState2($this->l('formulaire loulou validé'))
            || !$this->registerHook('actionOrderStatusPostUpdate')
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
        return parent::uninstall();
        Configuration::deleteByName('MHSC_CODE_LOULOU_1');
        Configuration::deleteByName('MHSC_CODE_LOULOU_2');
        Configuration::deleteByName('MHSC_CODE_LOULOU_3');
        Configuration::deleteByName('MHSC_CODE_LOULOU_4');
        Configuration::deleteByName('MHSC_CODE_LOULOU_5');
        Configuration::deleteByName('MHSC_LIENS_LOULOU');
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
            $order->template = 'loulou';
            $order->module_name = 'mhsc_clubloulous';
            return $order->add();
        } else {
            return true;
        }
    }

    public function installOrderState2($name)
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
            $order->hidden = true;
            $order->module_name = 'mhsc_clubloulous';
            return $order->add();
        } else {
            return true;
        }
    }

    public function getContent()
    {
        
		if(Tools::isSubmit('mhsc_clubloulous')) {

			$output = NULL;
            // Recuperation des données saisie
            $code1 = Tools::getValue('MHSC_CODE_LOULOU_1');
            $code2 = Tools::getValue('MHSC_CODE_LOULOU_2');
            $code3 = Tools::getValue('MHSC_CODE_LOULOU_3');
            $code4 = Tools::getValue('MHSC_CODE_LOULOU_4');
            $code5 = Tools::getValue('MHSC_CODE_LOULOU_5');
            $liens = Tools::getValue('MHSC_LIENS_LOULOU');
            $vide = Tools::getValue('vider');
            // Vérification
            if (Validate::isUrl($liens)) {
                Configuration::updateValue('MHSC_CODE_LOULOU_1', $code1); 
                Configuration::updateValue('MHSC_CODE_LOULOU_2', $code2); 
                Configuration::updateValue('MHSC_CODE_LOULOU_3', $code3); 
                Configuration::updateValue('MHSC_CODE_LOULOU_4', $code4); 
                Configuration::updateValue('MHSC_CODE_LOULOU_5', $code5); 
                Configuration::updateValue('MHSC_LIENS_LOULOU', $liens); 
                $output .= $this->displayConfirmation('Modification effectuer');
                
                if($vide == 1) {
                    $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'customer_group WHERE id_group = 5');
                    foreach ($data as $item) {
                        $id_customer= new Customer($item['id_customer']);
                        $id_customer->cleanGroups();
                        $id_customer->addGroups(['3']);
                        $id_customer->id_default_group = '3';
                        $id_customer->update();
                    }
                    $output .= $this->displayConfirmation('Toutes les membres ont bien été supprimés');
                }
            } elseif($vide == 1) {
                $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'customer_group WHERE id_group = 5');
                foreach ($data as $item) {
                    $id_customer= new Customer($item['id_customer']);
                    $id_customer->cleanGroups();
                    $id_customer->addGroups(['3']);
                    $id_customer->id_default_group = '3';
                    $id_customer->update();
                }
                $output .= $this->displayConfirmation('Toutes les membres ont bien été supprimés');
            } else {
                $output .= $this->displayError('Erreur dans le liens');
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
					'label' => $this->l('1er code d\'activation'),
					'name' => 'MHSC_CODE_LOULOU_1', 
				],
                [
                    'type' => 'switch',
                    'label' => $this->l('Retirer toutes les membres des loulous'),
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
                        ] ] 
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
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->title = $this->displayName;
		$helper->submit_action = 'mhsc_clubloulous';

		$helper->fields_value['MHSC_CODE_LOULOU_1'] = Tools::getValue('MHSC_CODE_LOULOU_1',Configuration::get('MHSC_CODE_LOULOU_1'));
		$helper->fields_value['MHSC_CODE_LOULOU_2'] = Tools::getValue('MHSC_CODE_LOULOU_2',Configuration::get('MHSC_CODE_LOULOU_2'));
		$helper->fields_value['MHSC_CODE_LOULOU_3'] = Tools::getValue('MHSC_CODE_LOULOU_3',Configuration::get('MHSC_CODE_LOULOU_3'));
		$helper->fields_value['MHSC_CODE_LOULOU_4'] = Tools::getValue('MHSC_CODE_LOULOU_4',Configuration::get('MHSC_CODE_LOULOU_4'));
		$helper->fields_value['MHSC_CODE_LOULOU_5'] = Tools::getValue('MHSC_CODE_LOULOU_5',Configuration::get('MHSC_CODE_LOULOU_5'));
		$helper->fields_value['MHSC_LIENS_LOULOU'] = Tools::getValue('MHSC_LIENS_LOULOU',Configuration::get('MHSC_LIENS_LOULOU'));

		return $helper->generateForm($form_configuration);

	}

    public function hookActionOrderStatusPostUpdate($params)
    {       
        foreach ($params as $key => $order) {
            // changer avant de mettre sur beta
            if ($order->id == 22) {
                $result = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'orders WHERE id_order = 
                                                    '.$params['id_order']);

                $customer = new Customer($result['id_customer']);
                $customer->addGroups(['5']);
                $customer->id_default_group = '5';
                $customer->update();
            }
        }
    }

}