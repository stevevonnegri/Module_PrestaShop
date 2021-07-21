<?php

use PrestaShop\PrestaShop\Adapter\ServiceLocator;

require_once(_PS_ROOT_DIR_ . '/modules/mhsc_clubmembre/classes/Clubmembre.php');

//nom de la class [NomTechniqueModule][NomFichier]ModuleFrontController
class mhsc_clubmembreinscriptionModuleFrontController extends ModuleFrontController {

	//attribuer les variables smarty et charger le tpl
	public function initContent(){

		$this->setTemplate('module:mhsc_clubmembre/views/templates/front/inscription.tpl');

        return parent::initContent();

	}

	//methode qui recupère les informations envoyées depuis le controller
	public function postProcess(){

		if(Tools::isSubmit('mhsc_clubmembre')){

            $email = Tools::getValue('email');
            $password = Tools::getValue('password');
            $anniv = Tools::getValue('anniv');

            $dataA = explode('/', $anniv);
            $anniv = $dataA['2'].'-'.$dataA['1'].'-'.$dataA['0'];

            if (Validate::isDateFormat($anniv)) 
            {
                if (Validate::isEmail($email)) 
                {
                    if (Validate::isPlaintextPassword($password)) 
                    {
                        if (Validate::isBirthDate($anniv)) 
                        {                        
                        $result = Clubmembre::getItemByMail($email);

                        if (!empty($result)) {
                            
                            foreach ($result as $key => $item) {

                            if ($item['id_client'] == 0) {
                                
                                /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
                                $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');

                                $customer = new Customer();
                                $customer->firstname = $item['prenom'];
                                $customer->lastname = $item['nom'];
                                $customer->email = $item['email'];
                                $customer->passwd = $crypto->hash($password);
                                        $customer->birthday = $anniv;
                                        $customer->id_default_group = '6';                        
                                        $customer->add();
                                        $customer->addGroups(['3', '6']);

                                        $membre = new Clubmembre($item['id_clubmembre']);
                                        $membre->id_client = $customer->id;
                                        $membre->update();
                                        $this->success[] = $this->l('Vous avez avec finaliser votre compte');
                                        
                                        Tools::redirect(Context::getContext()->link->getPageLink(
                                            'Identity',
                                            true,
                                        ));
                                    } else {
                                        $this->errors[] = $this->l('Vous vous etes déjà enregistrer');
                                    }
                                }

                            } else {
                                $this->errors[] = $this->l('Addresse mail inconnu');
                            }
                        } else {
                            $this->errors[] = $this->l('Date d\'anniversaire invalide');
                        }
                    } else {
                        $this->errors[] = $this->l('Mot de passe incorrect');
                    }
                } else {
                    $this->errors[] = $this->l('Ceci n\'est pas une addresse mail valide');
                }
            } else {
                $this->errors[] = $this->l('Mauvais format date');
            }   
        }


	}


}