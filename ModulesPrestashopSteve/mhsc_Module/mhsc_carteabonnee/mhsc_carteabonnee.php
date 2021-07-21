<?php

require_once(_PS_ROOT_DIR_.'/modules/mhsc_carteabonnee/classes/carteabonnee.php');

class mhsc_carteabonnee extends Module
{

    public function __construct() 
    {
        //Nom technique du module
        $this->name = 'mhsc_carteabonnee';
        //Nom afficher dans le back office
        $this->displayName = 'Carte d\'abonnement';
        //Déclarer la categorie du module developper
        $this->tab = 'front_office_features';
        //Version du module      
        $this->version = '0.1.0';
        //auteur du module
        $this->author = 'Von Negri Steve';
        //description du module
        $this->description = 'Module de gestion de carte d\'abonnement ';
        //activation de bootstrap
        $this->bootstrap = true ;

        parent::__construct();
    }


    public function install() 
    {
        //registerHook permet de d'accrocher notre module a un hook
        //si celui-ci n'existe pas, il crée le hook automatiquement
        if( !parent::install() 
            || !$this->installDb() 
            || !$this->installTab('AdminParentCustomer', 'AdminCarteAbonnee', 'Carte d\'abonnement')
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

    //Function qui s'execute que lors de la d'installation
    public function uninstall()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'carte_abonnee');

        return parent::uninstall();
    }

    //Create BDD for module
    public function installDb()
    {
        //getInstance = objet PDO
        //cle primaire doit TJRS ETRE DE FORME ID_+nomTable
        $sql = Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'carte_abonnee (
                id_carte_abonnee INT UNSIGNED NOT NULL AUTO_INCREMENT,
                email TEXT NOT NULL,
                numero TEXT NOT NULL,
                id_customer INT,
                PRIMARY KEY (id_carte_abonnee)
            ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');

        return $sql;
    }

    public function getContent()
    {

        $output = NULL;

        //test so un formulaire est envoyé dans l'attribut name du bouton
        if(tools::isSubmit('submit_mhsc_carteabonnee'))
        {
            $output = NULL;
            
            $email = Tools::getValue('email');
            $numero = Tools::getValue('numero');
            $file = Tools::getValue('file');
            $id_customer = Tools::getValue('id');
            $vider = Tools::getValue('vider');


            if(Validate::isEmail($email) && !empty($numero))
            {
                Db::getInstance()->insert('carte_abonnee', array(
                    'email' => pSQL($email),
                    'numero' => pSQL($numero),
                    'id_customer' => pSQL($id_customer)
                ));
                $output .= $this->displayConfirmation('Abonnée inscrit');
            } 
            elseif (pathinfo($file)['extension'] == 'csv')
            {
                if(!move_uploaded_file($_FILES['file']['tmp_name'],dirname(__FILE__).'/views/assets/fichier/'.$file)){

                    $output .= $this->displayError($this->l('Erreur lors du transfert de fichier'));
                }
                else
                {
                    
                    $lines = file(dirname(__FILE__).'/views/assets/fichier/'.$file);
                    foreach($lines as $n => $line){

                        $data = explode(";", $line);

                        $carte = new CarteAbonnee();

                        $n++;

                        if($carte->countCompteParNumero($data['1']) != 0)
                        {
                            $output .= $this->displayError($this->l('Erreur lors de l\'insersion dans la bdd, ligne '.$n.', ou le numero rentré est déjà présent dans la bdd'));
                        }
                        elseif($data['2'] != '\r\n')
                        {
                            Db::getInstance()->insert('carte_abonnee', array(
                            'email' => pSQL($data['0']),
                            'numero' => pSQL($data['1']),
                            'id_customer' => pSQL($data['2'])
                            ));
                        } 
                        else 
                        {
                            Db::getInstance()->insert('carte_abonnee', array(
                                'email' => pSQL($data['0']),
                                'numero' => pSQL($data['1']),
                            ));
                        }
                    }

                    unlink(dirname(__FILE__).'/views/assets/fichier/'.$file);

                    $output .= $this->displayConfirmation('Fichier d\'abonnées inscrits');
                    
                }
            }
            elseif ($vider == 1)
            {
                //Recuperer tout les entrees bdd
                $data = CarteAbonnee::getItems();

                //Foreach du tab pour delete ligne par ligne
                foreach ($data as $item) {
                    $carteAbonnee = new CarteAbonnee($item['id_carte_abonnee']);
                    
                    if($item['id_customer'] != 0)
                    {
                        $id_customer= new Customer($item['id_customer']);
                        $id_customer->cleanGroups();
                        $id_customer->addGroups(['3']);
                        $id_customer->id_default_group = '3';
                        $id_customer->update();
                    }
                    
                    $carteAbonnee->delete();

                }
                   
                $output .= $this->displayConfirmation('Toutes les cartes abonnées ont bien était supprimer');

            }
            else 
            {
                //affiche un message d'erreur
                $output .= $this->displayError($this->l('Erreur dans l\'extension du fichier (csv) ou champs email et numero vide'));
            }

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
                'title' => 'Inscription d\'abonnés ',
            ],
            'input' => 
            [
                [
                    'type' => 'text',
                    'label' => $this->l('email'),
                    'name' => 'email',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('numero'),
                    'name' => 'numero',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Id_client (id_customer)'),
                    'name' => 'id',
                    'html_content' => '<input type="number" name="id" id="id">'
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('file'),
					'name' => 'file',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Retirer toutes les cartes abonnées'),
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

        $helper->submit_action = 'submit_mhsc_carteabonnee';
  
        return $helper->generateForm($form_configuration);

    }

}