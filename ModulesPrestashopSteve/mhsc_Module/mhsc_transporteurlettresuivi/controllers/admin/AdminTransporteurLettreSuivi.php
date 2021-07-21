<?php

class AdminTransporteurLettreSuiviController extends ModuleAdminController
{

    public function __construct()
    {

        $this->table = 'order';
        $this->className = 'Order';

        parent::__construct();

        $this->fields_list = [
            'reference' => [
                'title' => $this->l('Réference commande'), 
            ],
            'lastname' => [
                'title' => $this->l('Nom'), 
            ],
            'firstname' => [
                'title' => $this->l('Prènom'), 
            ],
            'company' => [
                'title' => $this->l('Société'), 
            ],
            'address' => [
                'title' => $this->l('Addresse'), 
            ],
            'address2' => [
                'title' => $this->l('Addresse 2'), 
            ],
            'postcode' => [
                'title' => $this->l('Code postal'), 
            ],
            'countryname' => [
                'title' => $this->l('Pays'), 
            ],
            'phone' => [
                'title' => $this->l('Téléphone'), 
            ],
            'email' => [
                'title' => $this->l('Email'), 
            ],
        ];
        $this->bootstrap = true;

    }


    public function renderList(){

        $this->_select = "a.reference as reference, ad.lastname as lastname, ad.firstname as firstname,ad.company as company, ad.address1 as address, ad.address2 as address2, ad.postcode as postcode, ad.city as city, ad.id_country as id_country, ad.phone as phone, ad.id_customer as id_customer, ad.id_address as id_address, c.email as email, cl.name as countryname";
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'address ad on (ad.id_address = id_address_delivery) LEFT JOIN '._DB_PREFIX_.'country_lang cl on (ad.id_country = cl.id_country) LEFT JOIN '._DB_PREFIX_.'customer c on (ad.id_customer = c.id_customer)';
        $this->_where = ' AND current_state = 2 OR current_state = 9 OR current_state = 11';

        $html ='';

        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'mhsc_transporteurlettresuivi/views/templates/admin/help/export.tpl');

        return parent::renderList().$html;

    }

    public function postProcess()
    { 
        if (Tools::isSubmit('dl-csv')) {

            $this->exportDataToCsv();

            $filename = 'LettreSuivi-' . date('Y-m-d');

            $full_path = getcwd().'/toto.csv'; 
            
            ini_set('zlib.output_compression', 0);
            
            header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');
            
            header('Content-Tranfer-Encoding: binary');
            header('Content-type: text/csv; charset=utf-8');
            header('Content-Length: '.filesize($full_path));
            header('Content-MD5: '.utf8_encode($full_path));
            
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv";');
            header('Expires: 0');
            header('Pragma: no-cache');    
            
            readfile($full_path);
            exit;
        }

    }

    public function exportDataToCsv()
    {
        
        //Recupere les infos a exportes
        $sql = new DbQuery();
        $sql->select('ad.company as company, c.id_gender as genre, ad.lastname as lastname, ad.firstname as firstname, ad.address1 as address, ad.address2 as address2, ad.postcode as postcode, ad.city as city, ad.phone as phone, c.email as email, a.reference as reference');
        $sql->from('orders', 'a');
        $sql->join('LEFT JOIN '._DB_PREFIX_.'address ad on (ad.id_address = a.id_address_delivery) LEFT JOIN '._DB_PREFIX_.'country_lang cl on (ad.id_country = cl.id_country) LEFT JOIN '._DB_PREFIX_.'customer c on (ad.id_customer = c.id_customer)');
        $sql->where('current_state = 2 OR current_state = 9 OR current_state = 11');

        $data = Db::getInstance()->executeS($sql);

        // Debut de la saisi des données
        $fh = fopen('toto.csv', 'w');
        $delimiter = ";";

        $line = 
        [
            'Raison sociale',
            'SIREN',
            'Civilité',
            'Nom',
            'Prénom',
            'Identité / Service / Etage',
            'Bâtiment / Immeuble',
            'N° et libellé de voie',
            'Lieu-dit / BP',
            'Code Postal',
            'Ville',
            'Mobile',
            'Téléphone',
            'Email',
            'Référence Déstination',
        ];
        
        fputcsv($fh, $line, $delimiter);
        unset($line);
        $line = [];
        fputcsv($fh, $line, $delimiter);

        foreach ($data as $key => $fields) {

            $line = 
            [
                'company' => '',
                'SIREN' => '',
                'genre' => '',
                'lastname' => '',
                'firstname' => '',
                'Identité / Service / Etage' => '',
                'Bâtiment / Immeuble' => '',
                'address' => '',
                'address2' => '',
                'postcode' => '',
                'city' => '',
                'Mobile' => '',
                'phone' => '',
                'email' => '',
                'reference' => '',
            ];

            $line = array_merge($line, $fields);

            if($line['genre'] == 1) 
            {
                $line['genre'] = 'Monsieur';
            }
            elseif($line['genre'] == 2)
            {
                $line['genre'] = 'Madame';
            }

            fputcsv($fh, $line, $delimiter);
        }

        fclose($fh);
        $csv = ob_get_clean();     

        return $csv;

    }

}