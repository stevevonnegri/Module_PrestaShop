<?php

require_once(_PS_ROOT_DIR_ . '/modules/mhsc_clubmembre/classes/Clubmembre.php');

//nom de la class [NomTechniqueModule][NomFichier]ModuleFrontController
class mhsc_clubmembreclubmembreModuleFrontController extends ModuleFrontController {

	//attribuer les variables smarty et charger le tpl 
	public function initContent(){

		$this->setTemplate('module:mhsc_clubmembre/views/templates/front/clubmembre.tpl');

        return parent::initContent();

	}

	public function postProcess(){

		if(Tools::isSubmit('mhsc_clubmembre')){

            $id = Tools::getValue('id');
            $reference = Tools::getValue('reference');

            if (Validate::isFloat($id)) {

                $customer = new Customer($id);

                if ($customer->lastname == NULL) {
                    $this->errors[] = $this->l('Client inconnu');
                }
                $order = new Order();

                $liste = $order->getOrderByReference($reference);
                
                foreach ($liste as $key => $order) {
                    if ($order['id_customer'] == $id && $order['current_state'] == 21) {
                        $customer->addGroups(['6']);
                        $customer->id_default_group = '6';
                        $customer->update();
                        $this->success[] = $this->l('Vous venez de rejoindre le club des membres');
                    }
                }
                if (empty($this->success)) {
                    $this->errors[] = $this->l('Pas de correspondance entre la commande et l\'id client');
                }
            } else {
                $this->errors[] = $this->l('Id client doit etre que des chiffres');
            }

		}


	}


}