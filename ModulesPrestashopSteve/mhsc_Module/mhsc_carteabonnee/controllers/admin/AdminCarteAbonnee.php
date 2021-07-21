<?php

require_once(_PS_ROOT_DIR_.'/modules/mhsc_carteabonnee/classes/carteabonnee.php');

class AdminCarteAbonneeController extends ModuleAdminController
{

    public function __construct()
    {

        $this->table = 'carte_abonnee';
        $this->className = 'carteabonnee';

        parent::__construct();

        $this->fields_list = [
            'id_carte_abonnee' => [
                'title' => $this->l('ID'), 
            ],
            'email' => [
                'title' => $this->l('Email'), 
            ],
            'numero' => [
                'title' => $this->l('Numero'), 
            ],
            'id_customer' => [
                'title' => $this->l('id client'), 
            ]
        ];
        $this->bootstrap = true;

    }


    public function renderList(){

        // //Rajouter un btn action
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();

    }

    // méthode pour le formulaire d'edition
	public function renderForm(){

		$this->fields_form = array(
			'legend' => [
				'title' => 'Modification'
			],
			'input' => [
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
                    'html_content' => '<input type="number" name="id">'
                ],
			],
			'submit' => [
				'title' => $this->l('Save')
			]		
		);

		return parent::renderForm();

	}

}