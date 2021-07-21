<?php

require_once(_PS_ROOT_DIR_.'/modules/VnsavisProduit/classes/AvisProduit.php');


class VnsAvisProduit extends Module
{

    public function __construct() 
    {
        
        //Nom technique du module
        $this->name = 'Vnsavisproduit';        
        //Nom afficher dans le back office
        $this->displayName = 'Avis produits';    
        //Déclarer la categorie du module developper    
        $this->tab = 'front_office_features';  
        //Version du module            
        $this->version = '0.1.1';
        //auteur du module              
        $this->author = 'Von Negri Steve';      
        //description du module              
        $this->description = 'Modules sur la gestion des avis produits sur prestashop'; 
        //activation de bootstrap
        $this->bootstrap = true ;

        parent::__construct();
    }

    public function install() 
    {
        //registerHook permet de d'accrocher notre module a un hook
        //si celui-ci n'existe pas, il crée le hook automatiquement
        if( !parent::install() 
            || !$this->registerHook('displayFooterProduct') 
            || !$this->registerHook('displayCommentaireProduct')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->registerHook('displayProductListReviews')
            || !$this->installDb() )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    //Create BDD for module
    public function installDb()
    {
        //getInstance = objet PDO
        //cle primaire doit TJRS ETRE DE FORME ID_+nomTable
        $sql = Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'avis_produit (
                id_avis_produit INT UNSIGNED NOT NULL AUTO_INCREMENT,
                id_product INT(11) NOT NULL,
                note tinyint(1) NULL,
                avis text NOT NULL,
                date_add datetime NOT NULL,
                id_customer int(11) NULL,
                nom varchar(30) NULL,
                prenom varchar(20) NULL,
                email varchar(50) NULL,
                PRIMARY KEY (id_avis_produit)
            ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');

        return $sql;
    }

    public function uninstall()
    {
        Configuration::deleteByName('CONNEXTION_COMMENTER');
        Configuration::deleteByName('NOTE_PRODUIT');
        Configuration::deleteByName('COMMENTAIRE_PRODUIT');
        Configuration::deleteByName('CHAMP_IMAGE_AVIS');
        Configuration::deleteByName('NOMBRE_COMMENTAIRE_PRODUIT');

        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'avis_produit');
        return parent::uninstall();
    }

    public function getContent()
    {
        $output = NULL;


        //test so un formulaire est envoyé dans l'attribut name du bouton
        if(tools::isSubmit('submit_Vnsavisproduit'))
        {
            //Récupere les valeurs envoyés en post ou en get
            $connection = tools::getValue('CONNEXTION_COMMENTER');
            $note = tools::getValue('NOTE_PRODUIT');
            $commentaire = tools::getValue('COMMENTAIRE_PRODUIT');
            $image = Tools:: getValue('CHAMP_IMAGE_AVIS');
            $nombre_commentaire = Tools::getValue('NOMBRE_COMMENTAIRE_PRODUIT');


            //création ou mise a jour d'un champ de la table configuration
            //2 parametre, le nom du champ et sa valeur
            Configuration::updateValue('CONNEXTION_COMMENTER', $connection);
            Configuration::updateValue('NOTE_PRODUIT', $note);
            Configuration::updateValue('COMMENTAIRE_PRODUIT', $commentaire);
            Configuration::updateValue('NOMBRE_COMMENTAIRE_PRODUIT', $nombre_commentaire);

            if($image || !empty($image)) 
            {
                if(!move_uploaded_file($_FILES['CHAMP_IMAGE_AVIS']['tmp_name'], dirname(__FILE__).'/views/assets/img/'.$image))
                {
                    $output .= $this->displayError($this->l('Erreur lors du transfere de fichier'));
                } else
                {
                    Configuration::updateValue('CHAMP_IMAGE_AVIS', $image);
                    $output .= $this->displayConfirmation($this->l('Image enregistrée'));
                }
            }


            //tools::dieObject($text);      //permet de tester le contenu d'une variable en arrêtant l'executiuon de la page   

            $output = $this->displayConfirmation($this->l('Action effectuer'));

        } else 
        {
            //affiche un message d'erreur
            $output = $this->displayError($this->l('Echec'));

        }
        

        return $output.$this->displayForm();
    }


    //method qui permet de créer un formulaire via HelperForm
    public function displayForm()
    {

       $options = [
            [
                'value' => 1,
                'name' => $this->l('Oui')
            ],
            [
                'value' => 0,
                'name' => $this->l('Non')
            ]
        ];

        //tableau qui contient les informations du formulaire
        $form_configuration['0']['form'] = [
            'legend' => 
            [
                'title' => 'Configurateur',
            ],
            'input' => 
            [
                [
                    'type' => 'switch',       //type de champ
                    'label' => $this->l('Obligation d\'être connecter pour commenter'),   //$this->l = champs traduisible     
                    'name' => 'CONNEXTION_COMMENTER',      //Valeur de l'attribut name
                    'is_bool' => true,
                    'required' => true,      //Champ obligatoire. par defauts sur false
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
                    'type' => 'select',       //type de champ
                    'label' => $this->l('Note :'),   //$this->l = champs traduisible     
                    'name' => 'NOTE_PRODUIT',      //Valeur de l'attribut name
                    'required' => true,      //Champ obligatoire. par defauts sur false
                    'options' => [
                        'query' => $options,
                        'id' => 'value',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'textarea',       //type de champ
                    'label' => $this->l('Obligation d\'être connecter pour commenter'),   //$this->l = champs traduisible     
                    'name' => 'COMMENTAIRE_PRODUIT',      //Valeur de l'attribut name
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Nombre de commentaire à afficher sous chaque produit'),
                    'name' => 'NOMBRE_COMMENTAIRE_PRODUIT',
                    'required' => true,
                    'html_content' => '<input type="number" name="NOMBRE_COMMENTAIRE_PRODUIT">'
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'CHAMP_IMAGE_AVIS'
                ]

            ],
            'submit' =>
            [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default btn-info pull-right'
            ]
        ];

       //Tools::dieObject($form_configuration);

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        //Genere le liens d'action du formulaire, la page qui traite les informations
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_langugage = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->title = $this->displayName;

        //genere l'attribut name du bouton action
        $helper->submit_action = 'submit_Vnsavisproduit';

        //permet de recupere la valeur des mes champs inputs
        //autant de fields_value que de champs inputs
        $helper->fields_value['CONNEXTION_COMMENTER'] = Tools::getValue('CONNEXTION_COMMENTER', Configuration::get('CONNEXTION_COMMENTER'));
        $helper->fields_value['NOTE_PRODUIT'] = Tools::getValue('NOTE_PRODUIT', Configuration::get('NOTE_PRODUIT'));
        $helper->fields_value['COMMENTAIRE_PRODUIT'] = Tools::getValue('COMMENTAIRE_PRODUIT', Configuration::get('COMMENTAIRE_PRODUIT'));
        $helper->fields_value['NOMBRE_COMMENTAIRE_PRODUIT'] = Tools::getValue('NOMBRE_COMMENTAIRE_PRODUIT', Configuration::get('NOMBRE_COMMENTAIRE_PRODUIT'));


        return $helper->generateForm($form_configuration);

    }

    //function affichage commentaire article
    public function hookdisplayFooterProduct()
    {

        //Varible pour la function
        $id_product = Tools::getValue('id_product');

        $tempo['note'] = Configuration::get('NOTE_PRODUIT');
        $tempo['connection'] = Configuration::get('CONNEXTION_COMMENTER');
        $tempo['affichage_co'] = Configuration::get('NOMBRE_COMMENTAIRE_PRODUIT');

       //Tools::dieObject($tempo);

       $avisProduit = new AvisProduit();
       $messagesProduct = $avisProduit->getCommentaireAvis($id_product, $tempo['affichage_co']);


/*        $tab = array();

        foreach ($messagesProduct as $item) {
            if(empty($item['nom'])){
                
                $sql = new DbQuery();
                $sql->select('firstname,lastname, c.id_customer');
                $sql->from('customer', 'c');
                $sql->innerJoin('avis_produit', 'ap', 'c.id_customer = ap.id_customer');
                $infoAvis = Db::getInstance()->executeS($sql);

                // Tools::dieObject($item, false);
                // Tools::dieObject($infoAvis['0'] ['lastname']);

                $item['nom'] = $infoAvis['0']['lastname'];
                $item['prenom'] = $infoAvis['0']['firstname'];

            }

            $tab = array_merge($item, $tab);
            Tools::dieObject($tab, false);
            // Tools::dieObject($item, false);
        }*/

        // Tools::dieObject(count($messagesProduct));

        $product = new product();

        if( $product->getNombreCommentaire($id_product) <> 0)
        {
            echo 'test';
            $noteMoyenne = $product->getMoyenne($id_product);
           
            $this->context->smarty->assign(['noteMoyenne' => $noteMoyenne]);
        }

        //context est un registre qui stock les informations essentielles et qui est disponible sur toutes les pages
        $this->context->smarty->assign(
            [
                'commentaire' => $tempo,
                'messagesProduct' => $messagesProduct,
                'id_produit' => $id_product,
            ]);

        return $this->display(__FILE__, 'avis_produit.tpl');
    }


    //function form commentaire des articles
    public function hookdisplayCommentaireProduct()
    {

        $id_product = Tools::getValue('id_product');
        $temporaire['note'] = Configuration::get('NOTE_PRODUIT');
        $temporaire['connection'] = Configuration::get('CONNEXTION_COMMENTER');
        
        if(Tools::isSubmit('note_article'))
        {

            $idProduit = $id_product;
            $message = Tools::getValue('commentaireArticle');
            $date = date('Y-m-d H:i:s');

           //ID customer
            if (!empty($this->context->customer->id)) {
               $id_customer = $this->context->customer->id;
               $nom = NULL;
               $prenom = NULL;
               $email = NULL;

            } else {
               $id_customer = NULL;
               $nom = Tools::getValue('name_avis');
               $prenom = Tools::getValue('prenom_avis');
               $email = Tools::getValue('email_avis');
            }

           //Note article
            if (Tools::getValue('noteArticle')) {

                $note = Tools::getValue('noteArticle');

                $tab = [
                    'id_product' => ($idProduit), //pSQL evite les injections SQL
                    'note' => ($note),
                    'avis' => ($message),
                    'date_add' => ($date),
                    'id_customer' => ($id_customer),
                    'nom' => ($nom),
                    'prenom' => ($prenom),
                    'email' => ($email),
                ];

                $test = new AvisProduit();
                $test->hydrate($tab);
                $test->add();
                    
            } else {

               $tab = new AvisProduit();
               $tab->id_product = ($idProduit); //pSQL evite les injections SQL
               $tab->avis = ($message);
               $tab->date_add = ($date);
               $tab->id_customer = ($id_customer);
               $tab->nom = ($nom);
               $tab->prenom = ($prenom);
               $tab->email = ($email);

               $tab->add();
           }

           $id_lang = $this->context->language->id; 
           $product = new Product($idProduit, true, $id_lang);

           $link = new Link();
           Tools::Redirect($link->getproductLink($product));

       }

       //context est un registre qui stock les informations essentielles et qui est disponible sur toutes les pages
       $this->context->smarty->assign(
           [
               'commentaire' => $temporaire,
               'id_produit' => $id_product,
           ]);

       return $this->display(__FILE__, 'commentaire_produit.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet('module-VnsavisProduit-style', 'modules/'.$this->name.'/views/assets/css/AvisModules.css');
    }

    public function hookdisplayProductListReviews($produit)
    {
        
        $id_produit = $produit['product']->id_product;
        $product = new product();
        $note = $product->getMoyenne($id_produit);
        $nbr_commentaire = $product->getNombreCommentaire($id_produit);
        
        $this->context->smarty->assign(
            [
                'note' => $note,
                'nbr_commentaire' => $nbr_commentaire,
        ]);


        return $this->display(__FILE__, 'complement_catalog.tpl');


    }

}