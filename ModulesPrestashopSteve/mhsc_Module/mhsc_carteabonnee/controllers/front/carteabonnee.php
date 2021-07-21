<?php

//chemin vers ma classe
require_once(_PS_ROOT_DIR_.'/modules/mhsc_carteabonnee/classes/carteabonnee.php');

//nom de la class [NomTechniqueModule][NomFichier]ModuleFrontController
class mhsc_carteabonneecarteabonneeModuleFrontController extends ModuleFrontController {

	//attribuer les variables smarty et charger le tpl
	public function initContent(){

        $carte = new CarteAbonnee();
        $carte->id_customer = $this->context->customer->id;

        $carte = $carte->getItemById_customer($carte);

		parent::initContent();

		$this->context->smarty->assign(array(
			'carteabonnee' => $carte));

		$this->setTemplate('module:mhsc_carteabonnee/views/templates/front/carteabonnee.tpl');

	}

	//methode qui recupère les informations envoyées depuis le controller
	public function postProcess(){

		if(Tools::isSubmit('mhsc_carteabonnee')){

            $numero = Tools::getValue('numero');
            $email = $this->context->customer->email;

            $carteabonnee = new CarteAbonnee();

            $carteabonnee->email = $email;
            $carteabonnee->numero = $numero;

            if (!empty($numero) && $carteabonnee->countCompteParNumero($numero) == 1) 
            {
                $carteabonnee->id_customer = $this->context->customer->id;
                $carteabonnee->id = $carteabonnee->getid($carteabonnee);
                $carteabonnee->update();

                $customer = new Customer($this->context->customer->id);
                $customer->addGroups(['4']);
                $customer->id_default_group = '4';
                $customer->update();

                $this->success[] = $this->l('Modification effectuée');
            }
            else
            {
                $this->errors[] = $this->l('Numero non valide');
            }

		}


	}


}