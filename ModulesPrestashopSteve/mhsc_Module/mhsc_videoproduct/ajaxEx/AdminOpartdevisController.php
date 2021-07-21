<?php
/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

require_once _PS_MODULE_DIR_ . 'opartdevis/models/OpartQuotation.php';

class AdminOpartdevisController extends ModuleAdminController
{
    /* @var Bool Is PS version >= 1.7 ? */
    private $isSeven;

    /* @var String html */
    private $html = '';

    public function __construct()
    {
        $this->table = 'opartdevis';
        $this->name = 'opartdevis';
        $this->className = 'OpartQuotation';
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;

        $this->context = Context::getContext();

        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;

        if (!(int) Configuration::get('PS_SHOP_ENABLE')) {
            $this->errors[] = Tools::displayError('Your shop is not enable: Carrier and customer list will not be loaded');
        }

        parent::__construct();

          // Sélection de la vue (et enregistrement)
        if (Tools::getIsset('nom_commercial')) {
            $commercial = Tools::getValue('nom_commercial');
            $this->context->cookie->commercial = $commercial;
        } 
        elseif (isset($this->context->cookie->commercial)) {
            $this->context->cookie->commercial = $this->context->cookie->commercial;
        }
        else{
            $this->context->cookie->commercial = "all";
        }

        if (Tools::getIsset('statut')) {
            $statut = Tools::getValue('statut');
        }
        else {
            $statut = 0;
        }

        if(Tools::getIsset('adresse')){
            $adresse = 'IS NULL';
        }
        else{
            $adresse = 'IS NOT NULL';
        }

        // custom confirmation message (see AdminController class)
        $this->_conf[101] = $this->l('The quotation has been sent to the customer');
        $this->_conf[102] = $this->l('The quotation has been sent to the administrator');
        $this->_conf[103] = $this->l('The quotation has been validated');

        $commercial = Tools::getValue('nom_commercial');
        $statut = Tools::getValue('statut');

        // custom error message (see AdminController class)
        $this->_error[101] = $this->l('You cannot edit an ordered quotation');

        $this->_select =
            'a.id_opartdevis id_quotation, a.id_customer as num_client, a.date_add AS date_devis, a.date_upd AS date_modif, a.id_cart company_name,
            CONCAT(c.firstname, \'. \', c.lastname) AS customer, c.panier_abandonne, ad.*, c.id_employe,c.date_add as date_client, ad.postcode';

        $this->_join =
            'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (
                c.id_customer = a.id_customer
            )
            LEFT JOIN `'._DB_PREFIX_.'cart` ca ON ca.`id_cart` = a.`id_cart`
            LEFT JOIN `'._DB_PREFIX_.'address` ad ON ca.`id_address_invoice` = ad.`id_address`';

            //adresse alsace
            //et pas afficher les devis en statut devis rejete, Actualité et frais de port
            // et commentaire ne doit pas ressembler à panier abandonné ou le champ client panier abandonne est égale à 0
            // ou SI
            //'id commercial = 5 (Felix), pareil pour les statuts

        if ($this->context->cookie->commercial == 'Felix' && $statut == 0 && $adresse != 'IS NULL'){
            $this->_where .= ' AND (LEFT(ad.`postcode`, 2)  IN (67,68) 
            AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7 AND a.`status` != 9) 
            AND (c.id_employe = 5 OR  c.id_employe = 1)
            AND (a.commentaire NOT LIKE "%Panier abandonné%" AND  c.panier_abandonne = 0))  
            OR  ( (c.id_employe = 5 OR c.id_employe = 100)  AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7 AND a.`status` != 9))';
        }
        // id_employe = 13
        // pas afficher les devis en statut devis rejete, Actualité et frais de port

        elseif ($this->context->cookie->commercial == 'Pierre' && $statut == 0 && $adresse != 'IS NULL') {
            $this->_where .= '  AND  (c.id_employe = 13 AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7 AND a.`status` != 9))';
        }

        //Commentaire ressemble panier abandonée ou champ panier abandonne = 1
        //et pas afficher les devis en statut devis rejete, Actualité et frais de port
        //et que l'id_employe = 1 ou 20
        elseif ($this->context->cookie->commercial == 'Olga' && $statut == 0 ) {
            $this->_where .= ' AND (a.commentaire  LIKE "%Panier abandonné%" OR c.panier_abandonne = 1) 
             AND (c.id_employe != 13 AND c.id_employe != 4 AND c.id_employe != 5 AND c.id_employe != 100) 
            AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7 AND a.`status` != 9) 
            OR ((c.id_employe = 20 OR c.id_employe = 15) AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7 AND a.`status` != 9) AND c.panier_abandonne = 0)';
        }

        // pas afficher les devis en statut devis rejete, Actualité et frais de port
        // et id_employe = 4
        // ou si adresse pas alsace
        //et même statut
        // et que pas de commercial choisi
        //et commentaire ne ressemble pas à panier abandonné
        elseif ($this->context->cookie->commercial == 'Bruno' && $statut == 0 && $adresse != 'IS NULL') {
            $this->_where .= '  AND ((a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7  AND a.`status` != 9) 
            AND (c.id_employe = 4)) 
            OR (LEFT(ad.`postcode`, 2) NOT IN (67,68)
            AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7 AND a.`status` != 9 ) 
            AND (c.id_employe = 1) 
            AND (a.commentaire NOT LIKE "%Panier abandonné%" AND  c.panier_abandonne = 0))';
        }


        elseif ($this->context->cookie->commercial == 'all') {
            $this->_where .= '  AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7  AND a.`status` != 9) ';
        }



        

        if ($statut == 2){
            $this->_where .= ' AND a.`status` = 2 ';
        }

        if ($statut == 8){
            $this->_where .= ' AND a.`status` = 8 OR a.`status` = 7 OR a.`status` = 9';
        }

         if ($statut == 456){
            $this->_where .= ' AND a.`status` = 4 OR a.`status` = 5 OR a.`status` = 6';
        }

         if ($statut == 9){
            $this->_where .= ' AND a.`status` = 9';
        }

         if ($statut == 1213){
            $this->_where .= ' AND a.`status` = 12 OR a.`status` = 13 ';
        }
        
        

        /*if ($adresse == 'IS NULL'){
            $this->_where .= ' AND ad.`id_address` IS NULL  AND (a.`status` != 2 AND a.`status` != 8 AND a.`status` != 7) AND c.panier_abandonne = 0 ';
        }*/

        $this->_orderBy = 'a.date_upd';
        $this->_orderWay = 'DESC';

         $this->statuses_array = OpartQuotation::getListStatut();




         


        $this->fields_list = array(
            'id_opartdevis' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'filter_key' => 'a!id_opartdevis'
            ),
             'company_name' => array(
                'title' => $this->l('Entreprise'),
                'width' => 'auto',
                'callback' => 'getCompanyName',
                'search' => false
            ),
            'num_client' => array(
                'title' => $this->l('Customer'),
                'width' => 150,
                'havingFilter' => true,
                'callback' => 'callbackCustomer',
                'filter_key' => 'customer'
            ),

           
            
            'id_customer_thread' => array(
                'title' => $this->l('Message'),
                'width' => 'auto',
                'callback' => 'showMessageLink',
                'search' => false
            ),
             'commentaire' => array(
                'title' => $this->l('Commentaires'),
                'width' => 300,
                'filter_key' => 'a!commentaire'
            ),
               'id_cart' => array(
                'title' => $this->l('Total TTC'),
                'width' => 'auto',
                'callback' => 'callbackOrderTotal',
                'search' => false
            ),
          /*  'id_country' => array(
                'title' => $this->l('Pays'),
                'width' => 'auto',
                'havingFilter' => true,
                'callback' => 'callbackPays',
                'filter_key' => 'ad!id_country'
            ), */
             'status' => array(
                'title' => $this->l('Status'),
                 'type' => 'select',
                 'filter_key' => 'a!status',
                    'filter_type' => 'int',
                'list' => $this->statuses_array,
                'width' => 150,
                'callback' => 'getStatusName'
            ),

              'id_employe' => array(
                'title' => $this->l('Commercial'),
                'filter_key' => 'c!id_employe',
                 'callback' => 'callbackCommercial'
            ),
           
            'date_client' => array(
                'title' => $this->l('Création Client'),
                'type' => 'date',
                'width' => 'auto',
                'filter_key' => 'date_client',
                'search' => false
            ),

            'date_modif' => array(
                'title' => $this->l('Modif Devis'),
                'type' => 'datetime',
                'width' => 'auto',
                'search' => false,
                'filter_key' => 'a!date_upd'
            )
           
           
           
        );

    $this->bulk_actions = array(
            'delete'=> [
                        'text' => $this->l('Supprimer la sélection'),
                        'confirm' => $this->l('Etes vous sur de vouloir supprimer la sélection ?'),
                    ],
        
        );


    }

     public function beforeList()
    {
        return $this->renderNotifications().$this->renderViews().$this->rappel();
    }

     public function afterList()
    {
        // Tools::dieObject($this->_listsql);
        return $this->renderBulkPanel();
    }

    public function renderNotifications(){




         return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/notifications.tpl'
        );
    }

    public function renderViews(){
         

         
        $nb_devis_prepare_pierre = OpartQuotation::getNombreDevisStatus(0, 13);
        $nb_devis_relance_pierre = OpartQuotation::getNombreDevisStatus(6, 13);
        $nb_devis_modifie_pierre = OpartQuotation::getNombreDevisStatus(10, 13);
        $nb_echantillions_pierre = OpartQuotation::getNombreDevisStatus(13, 13);
        $nb_echantillions_envoye_pierre = OpartQuotation::getNombreDevisStatus(12, 13) ;



        $nb_devis_prepare_felix = OpartQuotation::getNombreDevisStatusFelix(0);
        $nb_devis_relance_felix = OpartQuotation::getNombreDevisStatusFelix(6);
        $nb_devis_modifie_felix = OpartQuotation::getNombreDevisStatusFelix(10);
        $nb_echantillions_felix = OpartQuotation::getNombreDevisStatusFelix(13);
        $nb_echantillions_envoye_felix = OpartQuotation::getNombreDevisStatusFelix(12) ;

        $nb_devis_prepare_olga = OpartQuotation::getNombreDevisStatusOlga(0);
        $nb_devis_relance_olga = OpartQuotation::getNombreDevisStatusOlga(6);
        $nb_devis_modifie_olga = OpartQuotation::getNombreDevisStatusOlga(10);
        $nb_echantillions_lola = OpartQuotation::getNombreDevisStatusOlga(13);
        $nb_echantillions_envoye_lola = OpartQuotation::getNombreDevisStatusOlga(12);


        $nb_devis_prepare_bruno = OpartQuotation::getNombreDevisStatusBruno(0);
        $nb_devis_relance_bruno = OpartQuotation::getNombreDevisStatusBruno(6);
        $nb_devis_modifie_bruno = OpartQuotation::getNombreDevisStatusBruno(10);
        $nb_echantillions_bruno =  OpartQuotation::getNombreDevisStatusBruno(13);
        $nb_echantillions_envoye_bruno = OpartQuotation::getNombreDevisStatusBruno(12);

        $nb_devis_prepare_tous =  $nb_devis_prepare_pierre + $nb_devis_prepare_felix + $nb_devis_prepare_olga + $nb_devis_prepare_bruno;
        $nb_devis_relance_tous = $nb_devis_relance_pierre + $nb_devis_relance_felix + $nb_devis_relance_olga + $nb_devis_relance_bruno;
        $nb_devis_modifie_tous = $nb_devis_modifie_pierre + $nb_devis_modifie_felix + $nb_devis_modifie_olga + $nb_devis_modifie_bruno;
        $nb_echantillions_tous = $nb_echantillions_pierre + $nb_echantillions_felix + $nb_echantillions_lola + $nb_echantillions_bruno;
        $nb_echantillions_envoye_tous = $nb_echantillions_envoye_pierre + $nb_echantillions_envoye_felix + $nb_echantillions_envoye_lola + $nb_echantillions_envoye_bruno;

        $notifpierre = '';
        $notiffelix = '';
        $notifolga = '';
        $notifbruno = '';
        $notiftous = '';



        if($nb_devis_prepare_pierre > 0 ){
            $notifpierre .= '<p style="width: 20px;height: 20px;border-radius: 20px;background: #d6e700;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_prepare_pierre.'</p>';
        }
        if($nb_devis_modifie_pierre > 0 ){
            $notifpierre .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background: #ff6600;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_modifie_pierre.'</p>';
        }
        if($nb_echantillions_pierre > 0 ){
            $notifpierre .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#f7f733;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_pierre.'</p>';
        }
         if($nb_echantillions_envoye_pierre > 0 ){
            $notifpierre .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#fbb935;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_envoye_pierre.'</p>';
        }
        if($nb_devis_relance_pierre > 0 ){
            $notifpierre .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background:red;line-height:20px;font-weight: bolder;float:left">'.$nb_devis_relance_pierre.'</p>';
        }
         if($nb_devis_prepare_felix > 0 ){
            $notiffelix .= '<p style="width: 20px;height: 20px;border-radius: 20px;background: #d6e700;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_prepare_felix.'</p>';
        }
         if($nb_devis_modifie_felix > 0 ){
            $notiffelix .= '<p style="width: 20px;height: 20px;border-radius: 20px;background: #ff6600;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_modifie_felix.'</p>';
        }
        if($nb_echantillions_felix > 0 ){
            $notiffelix .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#f7f733;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_felix.'</p>';
        }
        if($nb_echantillions_envoye_felix > 0 ){
            $notiffelix .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#fbb935;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_envoye_felix.'</p>';
        }
        if($nb_devis_relance_felix > 0 ){
            $notiffelix .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background:red;line-height:20px;font-weight: bolder;float:left">'.$nb_devis_relance_felix.'</p>';
        }
          if($nb_devis_prepare_olga > 0 ){
            $notifolga .= '<p style="width: 20px;height: 20px;border-radius: 20px;background: #d6e700;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_prepare_olga.'</p>';
        }
         if($nb_devis_modifie_olga > 0 ){
            $notifolga .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background: #ff6600;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_modifie_olga.'</p>';
        }
        if($nb_echantillions_lola > 0 ){
            $notifolga .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#f7f733;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_lola.'</p>';
        }
        if($nb_echantillions_envoye_lola > 0 ){
            $notifolga .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#fbb935;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_envoyé_lola.'</p>';
        }
        if($nb_devis_relance_olga > 0 ){
            $notifolga .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background:red;line-height:20px;font-weight: bolder;float:left">'.$nb_devis_relance_olga.'</p>';
        }
        if($nb_devis_prepare_bruno > 0 ){
            $notifbruno .= '<p style="width: 20px;height: 20px;border-radius: 20px;background: #d6e700;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_prepare_bruno.'</p>';
        }
        if($nb_devis_modifie_bruno > 0 ){
            $notifbruno .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background: #ff6600;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_modifie_bruno.'</p>';
        }
        if($nb_echantillions_bruno > 0 ){
            $notifbruno .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#f7f733;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_bruno.'</p>';
        }
        if($nb_echantillions_envoye_bruno > 0 ){
            $notifbruno .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#f7f733;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_envoye_bruno.'</p>';
        }
        if($nb_devis_relance_bruno > 0 ){
            $notifbruno .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:red;line-height:20px;font-weight: bolder;float:left">'.$nb_devis_relance_bruno.'</p>';
        }
        if($nb_devis_prepare_tous > 0 ){
            $notiftous .= '<p style="width: 20px;height: 20px;border-radius: 20px;background: #d6e700;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_prepare_tous.'</p>';
        }
        if($nb_devis_modifie_tous > 0 ){
            $notiftous .= ' <p style="width: 20px;height: 20px;border-radius: 20px;background: #ff6600;line-height:20px;font-weight: bolder;float:left;margin-right:2px;">'.$nb_devis_modifie_tous.'</p>';
        }
        if($nb_echantillions_tous > 0 ){
            $notiftous .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#f7f733;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_tous.'</p>';
        }
         if($nb_echantillions_envoye_tous > 0 ){
            $notiftous .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:#fbb935;line-height:20px;font-weight: bolder;float:left;color:#000;">'.$nb_echantillions_envoye_tous.'</p>';
        }
        if($nb_devis_relance_tous > 0 ){
            $notiftous .= '<p style="width: 20px;height: 20px;border-radius: 20px;background:red;line-height:20px;font-weight: bolder;float:left">'.$nb_devis_relance_tous.'</p>';
        }
        


        $nb_devis_pierre = OpartQuotation::getNombreDevisCommerciaux(13);
        $nb_devis_felix = OpartQuotation::getNombreDevisFelix();
        $nb_devis_olga = OpartQuotation::getNombreDevisOlga();
         $nb_devis_bruno = OpartQuotation::getNombreDevisBruno();
         $relances = OpartQuotation::getNombreDevisRelances();
         $echantillons = OpartQuotation::getNombreDevisEchantillons();
         $port = OpartQuotation::getNombreDevisPort();

          $this->context->smarty->assign(array(
            'nb_devis_prepare'  => $nb_devis_prepare,
            'nb_devis_relance' => $nb_devis_relance
             ));

         
        $module_link = 'index.php?controller=AdminOpartdevis&token='
                .Tools::getAdminTokenLite('AdminOpartdevis');


        if($this->context->cookie->commercial == "Olga"){
            $this->context->cookie->commercial = "TW Online";
        }

        $content = '<div class="panel">
        <div class="panel-heading">'.$this->l('Choix de la vue ').$this->context->cookie->commercial.'</div>
        <a href="'.$module_link.'&nom_commercial=all" class="btn btn-primary btn-sm" style="padding:0px 20px;">Tous<br/><br/>'.$notiftous.'</a>
        <a href="'.$module_link.'&nom_commercial=Pierre" class="btn btn-primary btn-sm" style="padding:0px 20px;">Pierre ('.$nb_devis_pierre.')<br/><br/>'.$notifpierre.'</a>
        <a href="'.$module_link.'&nom_commercial=Felix" class="btn btn-primary btn-sm" style="padding:0px 20px;">Félix ('.$nb_devis_felix.')<br/><br/>'.$notiffelix.'</a>
        <a href="'.$module_link.'&nom_commercial=Olga" class="btn btn-primary btn-sm" style="padding:0px 20px;">Catherine ('.$nb_devis_olga.')<br/><br/>'.$notifolga.'</a>
        <a href="'.$module_link.'&nom_commercial=Bruno" class="btn btn-primary btn-sm" style="padding:0px 20px;">Bruno ('.$nb_devis_bruno.')<br/><br/>'.$notifbruno.'</a>
        <a href="'.$module_link.'&statut=456" class="btn btn-primary btn-sm">Relances ('.$relances.')</a>
        <a href="'.$module_link.'&statut=1213" class="btn btn-primary btn-sm">Echantillons ('.$echantillons.')</a>
        <a href="'.$module_link.'&statut=9" class="btn btn-primary btn-sm">Port 50 % ('.$port.')</a>
        <a href="'.$module_link.'&statut=8" class="btn btn-primary btn-sm">Rejetés</a>
        <a href="'.$module_link.'&statut=2" class="btn btn-primary btn-sm">Commandes</a>
        </div>';

        $content .= '<div class="panel rows">
        <h2 class="panel-heading">ATTRIBUTION ET RÉPARTITION DES PROPECTS MAIL ET TELEPHONE</h2>
        <div class="alert alert-info">
        <div class="col-md-6">
        <h3>ANNEES PAIRES</h3>
        <p><b>Pierre :</b><br/>
        <strong> Janvier - Mars - Mai - Juillet - Septembre - Novembre</strong> - Prospects B D F H J L N P R T V X Z<br/>
        <strong>Fevrier - Avril - Juin - Aout - Octobre - Décembre</strong> - Prospects A C E G I K M O Q S U W Y<br/>
        + clients historiques : Nadjar, Capinielli, El Gourch</p>
        <p><b>Félix :</b> <br/>
        <strong>Janvier - Mars - Mai - Juillet - Septembre - Novembre</strong> - Prospects A C E G I K M O Q S U W Y<br/>
        <strong>Fevrier - Avril - Juin - Aout - Octobre - Décembre</strong> - Prospects B D F H J L N P R T V X Z <br/>
        + clients du 67 & 68 et visiteurs du Showroom Floorz</p>
        </div>
        <div class="col-md-6">
        <h3>ANNÉES IMPAIRES</h3>
        <p><b>Pierre :</b><br/>
         <strong>Janvier - Mars - Mai - Juillet - Septembre - Novembre</strong> - Prospects  A C E G I K M O Q S U W Y<br/>
        <strong>Fevrier - Avril - Juin - Aout - Octobre - Décembre</strong> - Prospects B D F H J L N P R T V X Z<br/>
        + clients historiques : Nadjar, Capinielli, El Gourch</p>
        <p><b>Félix :</b><br/>
        <strong>Janvier - Mars - Mai - Juillet - Septembre - Novembre</strong> - Prospects B D F H J L N P R T V X Z<br/>
        <strong>Fevrier - Avril - Juin - Aout - Octobre - Décembre</strong> - Prospects A C E G I K M O Q S U W Y<br/>
        + clients du 67 & 68 et visiteurs du Showroom Floorz</p>
        </div>
        <p style="clear:both;"><b>Lola</b> : commandes automatiques + paniers abandonnés<br/>
        <b>Bruno</b> : apports d’affaires par réseau, arbitrages</p>
        </div>
        </div>
        ';


        return $content;
    }


    public function rappel() {


      
      $statut = Tools::getValue('statut');

      if(Tools::getValue('nom_commercial')){
        $commercial = Tools::getValue('nom_commercial');
      }
      elseif($this->context->cookie->commercial){
        $commercial = $this->context->cookie->commercial;
      }

      if($commercial == "Pierre"){
            $nb_rappel = OpartQuotation::getCountRappelPierre();
            $list_rappel = OpartQuotation::getListRappelPierre();
           
      }
      elseif($commercial == "Felix"){
         $nb_rappel = OpartQuotation::getCountRappelFelix();
            $list_rappel = OpartQuotation::getListRappelFelix();

      }
      elseif($commercial == "Olga"){
         $nb_rappel = OpartQuotation::getCountRappelOlga();
            $list_rappel = OpartQuotation::getListRappelOlga();
      }
      elseif($commercial == "all" OR (isset($commercial) AND empty($statut))){
        $nb_rappel = OpartQuotation::getCountRappel();
        $list_rappel = OpartQuotation::getListRappel();
      }

    if($nb_rappel > 0){
        $content = '<div class="panel row">
        <div class="text-uppercase alert alert-warning">
        Rappel pour '.$commercial.', tu as '.$nb_rappel.' devis en attente</div>
        <table class="table">
                <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Commentaires</th>
                        <th>Modif Devis</th>
                        <th>Commercial</th>
                        <th>Actions</th>
                </tr>';
        foreach ($list_rappel as $rappel ) {


            $employee = new Employee($rappel['id_employe']);
            $customer   = new Customer($rappel['id_customer']);

            if($employee->lastname == "Laubriat"){
                $panier = new Employee(20);
                $employee->firstname = '<i class="icon-shopping-cart"></i>';
                $employee->lastname = $panier->firstname;
            }


            if($rappel['status'] != 6){

                switch ($rappel['status']) {
                    case '0':
                        $style = "background-color:#d6e700";
                        break;
                    case '10':
                        $style = "background-color:#ff6600;color:#fff";
                        break;
                    case '12':
                        $style = "background-color:#fbb935";
                        break;
                    case '13':
                        $style = "background-color:#f7f733";
                        break;
                    
                }

                $content .= '<tr>
                                <td   style="'.$style.';">'.$rappel['id_opartdevis'].' '.$rappel['id_customer'].'</td>
                                <td   style="'.$style.';">'.$customer->firstname.' '.$customer->lastname.'</td>
                                <td   style="'.$style.';">'.$rappel['commentaire'].'</td>
                                <td   style="'.$style.';">'.$rappel['date_upd'].'</td>
                                <td   style="'.$style.';">'.$employee->firstname.' '.$employee->lastname.'</td>
                                <td   style="'.$style.';"><a href="index.php?controller=AdminOpartdevis&id_opartdevis='.$rappel['id_opartdevis'].'&updateopartdevis&token='.Tools::getAdminTokenLite('AdminOpartdevis').'" class="btn btn-default">Modifier</a></td>
                        </tr>';
            }
        }

         foreach ($list_rappel as $rappel ) {

              $employee = new Employee($rappel['id_employe']);
            $customer   = new Customer($rappel['id_customer']);

             if($employee->lastname == "Laubriat"){
                $panier = new Employee(20);
                $employee->firstname = '<i class="icon-shopping-cart"></i>';
                $employee->lastname = $panier->firstname;
            }


            if($rappel['status'] == 6){


                $content .= '<tr>
                                <td   style="background-color:red;color:#FFF;">'.$rappel['id_opartdevis'].'</td>
                                <td   style="background-color:red;color:#FFF;">'.$customer->firstname.' '.$customer->lastname.'</td>
                                <td   style="background-color:red;color:#FFF;">'.$rappel['commentaire'].'</td>
                                <td   style="background-color:red;color:#FFF;">'.$rappel['date_upd'].'</td>
                                <td   style="background-color:red;color:#FFF;">'.$employee->firstname.' '.$employee->lastname.'</td>
                                <td   style="background-color:red;color:#FFF;"><a href="index.php?controller=AdminOpartdevis&id_opartdevis='.$rappel['id_opartdevis'].'&updateopartdevis&token='.Tools::getAdminTokenLite('AdminOpartdevis').'" class="btn btn-default">Modifier</a></td>
                        </tr>';
            }
        }

        $content .= '</table>
        </div>';
    }

        return $content;

    }



       public function renderBulkPanel()
    {

         

            $devis_status = OpartQuotation::getListStatut();
            $commerciaux = Employee::getEmployeesCommercial();

              $this->context->smarty->assign(array(
            'devis_status'  => $devis_status,
            'commerciaux' => $commerciaux
             ));


         return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/render_bulk_panel.tpl'
        );
    }

    public function callbackCustomer($customer, $tr)
    {
        
        $cart = $tr['id_cart'];
        $cart = new Cart($tr['id_cart']);
        


        $delivery_address = new Address($cart->id_address_delivery);

        

        $customer = new Customer($customer);

        $iso = Country::getIsoById($delivery_address->id_country);

        $customer_name = Tools::strtoupper($customer->lastname).' '.Tools::ucfirst($customer->firstname);
        $customer_name_short = $customer_name;
        if (preg_match("/^[a-zA-ZÀ-ÖØ-öø-ÿœŒ'\ ]+$/", $customer_name)) {
            $customer_name = Tools::ucfirst($customer->firstname).' '.Tools::strtoupper($customer->lastname);
            $customer_name_short = Tools::strtoupper(Tools::substr($customer->firstname, 0, 1).'. '.
                $customer->lastname);
        }




        $this->context->smarty->assign(array(
            'iso'  =>
                (file_exists(_PS_MODULE_DIR_.'/dmulistecommandes/views/img/flags/'.Tools::strtolower($iso).'.gif') ?
                Tools::strtolower($iso) : '' ),
            'customer_name' => $customer_name,
            'customer_name_short' => $customer_name_short,
            'customer' => $customer,
            'ps7' => version_compare(_PS_VERSION_, '1.7', '>='),
            'delivery_address'  => $delivery_address,
            'address_format' => nl2br(AddressFormat::generateAddress($delivery_address))

        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/callback_customer.tpl'
        );
    }



    public function callbackCommercial($id_commercial, $tr){

         if ($tr['panier_abandonne'] == 1) {

            switch ($id_commercial) {
                case 20 :
                    $commercial .= '<i class="icon-shopping-cart"></i><br/>TW';
                    break;

                case 13 : 
                    $commercial .= '<i class="icon-shopping-cart"></i><br/>PW';
                    break;

                case 5 : 
                    $commercial .= '<i class="icon-shopping-cart"></i><br/>FM';
                    break;

                case 4 : 
                    $commercial .= '<i class="icon-shopping-cart"></i><br/>BM';
                    break;
                
                default:
                    $commercial .= '<i class="icon-shopping-cart"></i><br/>TW';
                    break;
            }
             


             return $commercial;
         }
         elseif ($id_commercial == 5) {

            $commercial .= '<br/>FM';
        }
        elseif ($id_commercial == 13) {

            $commercial .= '<br/>PW';
        }
        elseif ($id_commercial== 4) {

            $commercial .= '<br/>BM';
        }
        elseif ($id_commercial== 15) {

            $commercial .= '<br/>LR';
        }
        elseif ($id_commercial == 20) {

            $commercial .= '<br/>TW';
        }
        elseif ($id_commercial == 100) {

            $commercial .= '<br/>Showroom';
        }
        elseif((substr($tr['postcode'], 0,2) != 67 && substr($tr['postcode'], 0,2) != 68)){
             return  $commercial .= '<br/>Commercial : A definir';
       }
       else {
            return  $commercial .= '<br/>FM';
       }

            return $commercial;
            
        }

    public function callbackPays($id_country, $tr)
    {
        
       $country = new Country($id_country);
       $id_lang = $this->context->language->id;
       $pays = $country->getNameById($id_lang, $id_country);

       return $pays;

    }

    

     public function callbackOrderTotal($total, $tr)
    {
       
        $order = new Cart($tr['id_cart']);
        $products = $order->getProducts();


        $total = $order->getTotalCart($order->id);
       

        $products_qty = 0;
        if ($products) {
            $cumulation = 0; // modif faites : total_wt => total_price_tax_incl
            foreach ($products as $product) {
                $cumulation +=  isset($product['total_price_tax_incl']) ? $product['total_price_tax_incl'] : 0;
                $products_qty += isset($product['product_quantity']) ? $product['product_quantity'] : 0;
                ;
            }

            $rechercher = array(",", "€", " ");
            $remplacer = array(".", "", "");
            $total = str_replace($rechercher,$remplacer,$total);

            $rest = intval($total) - $cumulation;
        }

        $this->context->smarty->assign(array(
            'price_total'  => Tools::displayPrice($total, (int)$order->id_currency),
            'products_qty' => $products_qty,
            'products' => $products,
            'rest' => Tools::displayPrice($rest),
            'total' => $total

        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/callback_order_total.tpl'
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addJqueryPlugin(array('autocomplete'));



        $this->addCSS(__PS_BASE_URI__ . 'modules/opartdevis/views/css/opartdevis_admin.css');
        $this->addJS(__PS_BASE_URI__ . 'modules/opartdevis/views/js/backoffice.js');


    }



    public function renderList()
    {
        OpartQuotation::deleteQuotationsWithoutCart();
        OpartQuotation::checkAllQuotations();


        

        
        $this->addRowAction('Edit');
        $this->addRowAction('DevisPdf');
        //$this->addRowAction('ViewCustomer');
        $this->addRowAction('ViewOrder');
        $this->addRowAction('CreateOrder');
        $this->addRowAction('SendToCustomer');
        //$this->addRowAction('SendToAdmin');
       // $this->addRowAction('Validate');
        $this->addRowAction('Delete');

        return $this->beforeList().parent::renderList().$this->afterList();
    }

    public function getStatusName($status)
    {
        switch ($status) {
                case 0:
                    return "<span style='background-color:#d6e700;padding:10px;width:100%;display:block;text-align:center;'>Devis préparé</span>";
                    break;
                case 1:
                    return "<span style='background-color:#9afb00;padding:10px;width:100%;display:block;text-align:center;'>Devis envoyé</span>";
                    break;
                case 2:
                    return "<span style='background-color:#32cd32;padding:10px;width:100%;display:block;text-align:center;color:#FFF;'>Commande</span>";
                    break;
                case 3:
                    return "Expired";
                    break;
                case 4:
                    return "<span style='background-color:#00c73b;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>1<sup>ier</sup> relance</span>";
                    break;
                case 5:
                    return "<span style='background-color:#00a336;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>2eme relance</span>";
                    break;
                case 6:
                    return "<span style='background-color:red;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>relance rapide</span>";
                    break;
                case 7:
                    return "<span style='background-color:#3bb0c2;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>Actualité du projet</span>";
                    break;
                case 8:
                    return "<span style='background-color:#000;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>Devis rejet</span>";
                    break;
                case 9:
                    return "<span style='background-color:#3bb0c2;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>Frais de port -50%</span>";
                    break;
                case 10:
                    return "<span style='background-color:#ff6600;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>Devis à modifier</span>";
                    break;
                case 11:
                    return "<span style='background-color:#0098ca;color:#FFF;padding:10px;width:100%;display:block;text-align:center;'>Msge tel + sms</span>";
                    break;
                case 12:
                    return "<span style='background-color:#fbb935;color:#000;padding:10px;width:100%;display:block;text-align:center;'>Echantillon envoyé</span>";
                    break;
                case 13:
                    return "<span style='background-color:#f7f733;color:#000;padding:10px;width:100%;display:block;text-align:center;'>Echantillon demandé</span>";
                    break;
            }

           
    }

    public function displayExpirationDate($id_opartdevis)
    {
        if (!Configuration::get('OPARTDEVIS_VALIDITY')) {
            return "--";
        }

        $quotation = new OpartQuotation($id_opartdevis);

        $status = $quotation->getStatus();

        if ($status == OpartQuotation::VALIDATED || $status == OpartQuotation::EXPIRED) {
            return OpartQuotation::getExpirationDate($quotation->date_add);
        }
    }

    public function getTotalCart($id_cart)
    {
        $context = Context::getContext();
        $context->cart = new Cart($id_cart);

        if (!$context->cart->id) {
            return 'error';
        }

        $context->currency = new Currency((int)$context->cart->id_currency);
        $context->customer = new Customer((int)$context->cart->id_customer);

        return Cart::getTotalCart($id_cart, false, Cart::BOTH_WITHOUT_SHIPPING);
    }

    public static function getCompanyName($id_cart)
    {
        $cart = new Cart($id_cart);
        $address_invoice = new Address($cart->id_address_invoice);

        if ($address_invoice->company) {
            return $address_invoice->company;
        }

        return "--";
    }

    public function showMessageLink($id_customer_thread)
    {
        if ($id_customer_thread) {
            $token = Tools::getAdminToken('AdminCustomerThreads'
                .(int)Tab::getIdFromClassName('AdminCustomerThreads')
                .(int) $this->context->cookie->id_employee);
            $href = 'index.php?controller=AdminCustomerThreads&id_customer_thread='.$id_customer_thread.'&viewcustomer_thread&token='.$token;

            return '<a href="'.$href.'">'.$this->l('read').'</a>';
        }

        return '--';
    }

    public function displayEditLink($token, $id_opartdevis)
    {
        $quotation_status = (new OpartQuotation($id_opartdevis))
            ->getStatus();

        if ((int)$quotation_status === OpartQuotation::ORDERED) {
            return false;
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id_opartdevis.'&updateopartdevis&token='.($token ? $token : $this->token),
            'confirm' => null,
            'action' => $this->l('Edit')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_edit.tpl'
        );
    }

    public function displayViewCustomerLink($token, $id_opartdevis)
    {
        $token = Tools::getAdminToken('AdminCustomers'
            .(int)Tab::getIdFromClassName('AdminCustomers')
            .(int)$this->context->cookie->id_employee);

        $quotation = new OpartQuotation($id_opartdevis);

        $this->context->smarty->assign(array(
            'href' => "index.php?controller=AdminCustomers&id_customer={$quotation->id_customer}&viewcustomer&token={$token}",
            'confirm' => null,
            'action' => $this->l('View customer')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_view_customer.tpl'
        );
    }

    public function displayViewOrderLink($token, $id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if ($quotation->getStatus() != OpartQuotation::ORDERED || !$quotation->id_order) {
            return false;
        }

        $token = Tools::getAdminToken('AdminOrders'
            .(int)Tab::getIdFromClassName('AdminOrders')
            .(int)$this->context->cookie->id_employee);

        $quotation = new OpartQuotation($id_opartdevis);

        $this->context->smarty->assign(array(
            'href' => "index.php?controller=AdminOrders&id_order={$quotation->id_order}&vieworder&token={$token}",
            'confirm' => null,
            'action' => $this->l('View order')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_view_order.tpl'
        );
    }

    public function displayCreateOrderLink($token, $id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

      

        $token = Tools::getAdminToken('AdminOrders'
            .(int)Tab::getIdFromClassName('AdminOrders')
            .(int)$this->context->cookie->id_employee);

        $quotation = new OpartQuotation($id_opartdevis);

        $this->context->smarty->assign(array(
            'href' => "index.php?controller=AdminOrders&id_cart={$quotation->id_cart}&addorder&token={$token}",
            'confirm' => $this->l('Are you sure you want to create an order using this quotation ?'),
            'action' => $this->l('Create order')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_create_order_from_quotation.tpl'
        );
    }

    public function displayValidateLink($token, $id_opartdevis)
    {
        $quotation_status = (new OpartQuotation($id_opartdevis))
            ->getStatus();

        if ($quotation_status != OpartQuotation::NOT_VALIDATED) {
            return false;
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id_opartdevis.'&validate&token='.($token ? $token : $this->token),
            'confirm' => $this->l('Are you sure you want to validate this quotation ?'),
            'action' => $this->l('Validate')
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_validate_quotation.tpl'
        );
    }

    public function displaySendToCustomerLink($token, $id_opartdevis)
    {
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id_opartdevis.'&sendToCustomer&token='.($token ? $token : $this->token),
            'confirm' => $this->l('Are you sure you want to send this quotation to customer ?'),
            'action' => $this->l('Send to Customer'),
            'id_opartdevis' => $id_opartdevis
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_send_email.tpl'
        );
    }

     public function displayDevisPdfLink($token, $id_opartdevis)
    {
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id_opartdevis.'&devisPdf&token='.($token ? $token : $this->token),
            'id_opartdevis' => $id_opartdevis
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_download_pdf.tpl'
        );
    }

    public function displaySendToAdminLink($token, $id_opartdevis)
    {
        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id_opartdevis.'&sendToAdmin&token='.($token != null ? $token : $this->token),
            'confirm' => $this->l('Are you sure you want to send this quotation to admin ?'),
            'action' => $this->l('Send to admin'),
            'id_opartdevis' => $id_opartdevis
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'opartdevis/views/templates/admin/helpers/lists/list_action_send_email.tpl'
        );
    }

    public function renderForm()
    {
        if (!($quotation = $this->loadObject(true))) {
            return;
        }

        if ((int)$quotation->getStatus() === OpartQuotation::ORDERED && Tools::getIsset('updateopartdevis')) {
            Tools::redirectAdmin(self::$currentIndex.'&error=101&token='.$this->token);
        }

        if (isset($quotation->id_customer) && is_numeric($quotation->id_customer)) {
            $this->context->customer = new Customer($quotation->id_customer);
        }

        if (isset($quotation->id_cart) && is_numeric($quotation->id_cart)) {
            $this->context->cart = new Cart($quotation->id_cart);
            $products = $this->context->cart->getProducts();
            $customized_datas = Product::getAllCustomizedDatas($this->context->cart->id);
            $this->context->currency = new Currency((int)$this->context->cart->id_currency);
        }

        if (isset($products) && count($products)) {
            foreach ($products as &$product) {
                $yourPrice = $this->getYourPrice(
                    $quotation->id_cart,
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $quotation->id_customer,
                    true
                );

                $product['your_price'] = $yourPrice['price'];
                $product['specific_qty'] = $yourPrice['from_quantity'];

                $specific_price_output = null;

                //get catalog price
                $product['catalogue_price'] = Product::getPriceStatic(
                    $product['id_product'],
                    false,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    true,
                    1,
                    false,
                    null,
                    null,
                    null,
                    $specific_price_output,
                    false,
                    false,
                    null,
                    false
                );

                if ($yourPrice == $product['catalogue_price'] || !$yourPrice) {
                    $use_customer_price = false;
                } else {
                    $use_customer_price = true;
                }

                $product['specific_price'] = Product::getPriceStatic(
                    $product['id_product'],
                    false,
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    true,
                    $product['cart_quantity'],
                    false,
                    $this->context->cart->id_customer,
                    $this->context->cart->id,
                    null,
                    $specific_price_output,
                    false,
                    true,
                    $this->context,
                    $use_customer_price
                );

                switch (Configuration::get('PS_ROUND_TYPE')) {
                    case Order::ROUND_TOTAL:
                        $product['total'] = $product['specific_price'] * $product['cart_quantity'];
                        break;
                    case Order::ROUND_LINE:
                        $product['total'] = Tools::ps_round(
                            $product['specific_price'] * $product['cart_quantity'],
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        break;
                    case Order::ROUND_ITEM:
                    default:
                        $product['total'] = Tools::ps_round(
                                $product['specific_price'],
                                _PS_PRICE_COMPUTE_PRECISION_
                            ) * $product['cart_quantity'];
                        break;
                }

                $product['customization_datas_json'] = '';
            }
        }

        if (isset($customized_datas)) {
            foreach ($products as &$product) {
                if (!isset($customized_datas[$product['id_product']][$product['id_product_attribute']][$product['id_address_delivery']])) {
                    continue;
                }

                if ($this->isSeven) {
                    foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$product['id_address_delivery']] as $customized_data) {
                        if ($customized_data['datas'][1][0]['id_customization'] == $product['id_customization']) {
                            $product['customization_datas'][] = $customized_data;
                        }
                    }
                } else {
                    foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$product['id_address_delivery']] as $customized_data) {
                        $product['customization_datas'][] = $customized_data;
                    }
                }

                $product['customization_datas_json'] = Tools::jsonEncode($product['customization_datas']);
            }
        }

       


        $this->context->smarty->assign(array(
            'quotation' => $quotation,
            'customer' => (isset($this->context->customer)) ? $this->context->customer : null,
            'cart' => (isset($this->context->cart)) ? $this->context->cart : null,
            'summary' => (isset($this->context->cart)) ? $this->context->cart->getSummaryDetails() : null,
            'products' => (isset($products)) ? $products : null,
            'upload_url' => _MODULE_DIR_.'opartdevis/uploads/'.Tools::getValue('id_opartdevis'),
            'upload_path' => _PS_MODULE_DIR_.'opartdevis/uploads/'.Tools::getValue('id_opartdevis'),
            'cart_rules' => $this->getAllCartRules(),
            'statut' => $this->getStatusName($quotation->status),
            'id_lang_default' => $this->context->language->id,
            'href' => self::$currentIndex.'&AdminOpartdevis&addopartdevis&token='.$this->token,
            'hrefCancel' => self::$currentIndex.'&token='.$this->token,
            'opart_token' => $this->token,
            'currency_sign' => $this->context->currency->sign,
            'json_carrier_list' => (isset($this->context->cart)) ? Tools::jsonEncode($quotation->createCarrierList($this->context->cart)) : Tools::jsonEncode(array()),
            'ajax_url' => $this->context->link->getAdminLink('AdminOpartdevis'),
            'commerciaux' => Employee::getEmployeesCommercial()
        ));

        $this->addJS(_MODULE_DIR_ . $this->name . '/views/js/admin.js');

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->name.'/views/templates/admin/form_quotation.tpl'
        );
    }

    private function getAllCartRules()
    {

        $today = date("Y-m-d H:i:s"); 

        $sql =
            'SELECT c.id_cart_rule, c.code, c.description, cl.name, c.date_to
            FROM `'._DB_PREFIX_.'cart_rule` c
            LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` cl ON (
                c.id_cart_rule = cl.id_cart_rule
            )
            WHERE c.active = 1
            AND cl.id_lang = '.(int)$this->context->language->id.'
            AND c.date_to >= "'.$today.'"
            GROUP BY c.id_cart_rule ORDER BY c.id_cart_rule DESC';

        $rules = db::getInstance()->executeS($sql);


        return $rules;
    }

    public function getYourPrice($id_cart, $id_product, $id_product_attribute, $id_customer, $get_row = false)
    {
        $sql =
            'SELECT price,from_quantity
            FROM `'._DB_PREFIX_.'specific_price`
            WHERE id_cart = '.(int)$id_cart.'
                AND id_product = '.(int)$id_product.'
                AND id_product_attribute = '.(int)$id_product_attribute.'
                AND id_customer = '.(int)$id_customer;

        $row = db::getInstance()->getRow($sql);

        if ($get_row) {
            return $row;
        }

        return $row['price'];
    }


    private function postValidation()
    {
        if (Tools::isSubmit('submitAddOpartDevis')) {
            if (!Tools::getIsset('opart_devis_customer_id') || !Validate::isInt(Tools::getValue('opart_devis_customer_id'))) {
                $this->errors[] = Tools::displayError('Error : You have to choose a customer');
            }

            if (!Tools::getIsset('id_cart') || !Validate::isInt(Tools::getValue('id_cart'))) {
                $this->errors[] = Tools::displayError('Error : The cart id is not valid');
            }

            if (!Validate::isInt(Tools::getValue('id_opartdevis'))) {
                $this->errors[] = Tools::displayError('Error : The quotation id is not valid');
            }

            if (!Validate::isGenericName(Tools::getValue('quotation_name'))) {
                $this->errors[] = Tools::displayError('Error : The "Quotation Name" is not valid');
            }

            if (!Validate::isGenericName(Tools::getValue('devis_objet'))) {
                $this->errors[] = Tools::displayError('Error : L\'objet du mail est non valide');
            }

            if (!Validate::isCleanHtml(Tools::getValue('message_visible'))) {
                $this->errors[] = Tools::displayError('Error : The "Message Visible" is not valid');
            }

            if (isset($_FILES['fileopartdevis']) && ($_FILES['fileopartdevis']['name'][0] !== '')) {
                $count = count($_FILES['fileopartdevis']['name']);

                $file_max_size = 5242880;
                $allowed_extensions = array('.png', '.gif', '.jpg', '.jpeg', '.pdf',
                    '.doc', '.docx', '.txt', '.ppt', '.xls');

                for ($i = 0; $i < $count; $i++) {
                    $size = filesize($_FILES['fileopartdevis']['tmp_name'][$i]);
                    $extension = Tools::strtolower(strrchr($_FILES['fileopartdevis']['name'][$i], '.'));

                    if (!in_array($extension, $allowed_extensions)) {
                        $this->errors[] = sprintf(
                            Tools::displayError("Error : The type of the file %s is not valid"),
                            $_FILES['fileopartdevis']['name'][$i]
                        );
                    }

                    if ($size > $file_max_size) {
                        $this->errors[] = sprintf(
                            Tools::displayError('The %s file is too big'),
                            $_FILES['fileopartdevis']['name'][$i]
                        );
                    }
                }
            }
        }

        
    }

    private function uploadFiles($id_opartdevis)
    {
        $count = count($_FILES['fileopartdevis']['name']);
        $upload_dir = _PS_MODULE_DIR_.'opartdevis/uploads';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755);
        }

        $upload_dir .= DIRECTORY_SEPARATOR.$id_opartdevis;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755);
        }

        for ($i = 0; $i < $count; $i++) {
            $file = $_FILES['fileopartdevis']['name'][$i];
            if (isset($_FILES['fileopartdevis']['error'][$i])) {
                move_uploaded_file(
                    $_FILES['fileopartdevis']['tmp_name'][$i],
                    $upload_dir.DIRECTORY_SEPARATOR.$file
                );
            }
        }
    }

    private function saveOpartDevis()
    {
        if (Tools::isSubmit('submitAddOpartDevis')) {
            $customer = new Customer(Tools::getValue('opart_devis_customer_id'));

          

            $cart = OpartQuotation::createCart(Tools::getValue('id_cart'));
            

            

            $id_opartdevis = Tools::getValue('id_opartdevis');




            $quotation = OpartQuotation::createQuotation(
                $cart,
                $customer,
                $id_opartdevis,
                Tools::getValue('quotation_name'),
                Tools::getValue('devis_objet'),
                Tools::getValue('message_visible'),
                Tools::getValue('corps_mail'),
                Tools::getValue('devis_commentaire'),
                Tools::getValue('statut_devis'),
                false,
                true
            );

            if (isset($_FILES['fileopartdevis']) && ($_FILES['fileopartdevis']['name'][0] !== '')) {
                $this->uploadFiles($quotation->id);
            }

            // set confirmation message (3 for creation, 4 for update) - see AdminController class
            $conf = ($id_opartdevis) ? 4 : 3;

            Tools::redirectAdmin(self::$currentIndex.'&conf='.$conf.'&token='.$this->token);
        }


    }

      private function refreshVoucher()
    {
        if (Tools::isSubmit('submitRefreshVoucher')) {
            $customer = new Customer(Tools::getValue('opart_devis_customer_id'));
            $cart = OpartQuotation::createCart(Tools::getValue('id_cart'));
            $id_opartdevis = Tools::getValue('id_opartdevis');

            $quotation = OpartQuotation::createQuotation(
                $cart,
                $customer,
                $id_opartdevis,
                Tools::getValue('quotation_name'),
                Tools::getValue('devis_objet'),
                Tools::getValue('message_visible'),
                 Tools::getValue('corps_mail'),
                 Tools::getValue('devis_commentaire'),
                 Tools::getValue('statut_devis'),
                null,
                false,
                false
            );

            //$id_opartdevis = OpartQuotation::getLastDevis();

            if (isset($_FILES['fileopartdevis']) && ($_FILES['fileopartdevis']['name'][0] !== '')) {
                $this->uploadFiles($quotation->id);
            }

            // set confirmation message (3 for creation, 4 for update) - see AdminController class
            $conf = ($id_opartdevis) ? 4 : 3;

            Tools::redirectAdmin(self::$currentIndex.'&id_opartdevis='.$quotation->id.'&updateopartdevis&token='.$this->token);
        }


    }


    public function postProcess()
    {
        // save or update quotation
        if (Tools::isSubmit('submitAddOpartDevis')) {
            $this->postValidation();

            if (!count($this->errors)) {
                $this->saveOpartDevis();
            }

            return $this->renderForm();
        }

         if (Tools::isSubmit('submitRefreshVoucher')){
             $this->refreshVoucher();
        }

        if (Tools::isSubmit('submit_commentaire_devis')){
            $id_devis = Tools::getValue('id_opartdevis');
            $commentaire = Tools::getValue('devis_commentaire');
            $this->UpdateCommentaire($id_devis, $commentaire);

        }


        //mettre à jour le commercial pour ce client
          if (Tools::isSubmit('submit_commercial_client')){
            $id_client = Tools::getValue('id_client');
            $id_employe = Tools::getValue('commercial');
            $id_devis = Tools::getValue('id_opartdevis');
            $this->UpdateCustomer($id_client, $id_employe, $id_devis);

        }


        //enlever le client des paniers abandonnés
          if (Tools::isSubmit('submit_panier_abandonne_client')){
            $id_devis = Tools::getValue('id_opartdevis');

            $customer = new Customer(Tools::getValue('id_client')); 
            $customer->panier_abandonne = 0;
            $customer->update();
            Tools::redirectAdmin(self::$currentIndex.'&id_opartdevis='.$id_devis.'&updateopartdevis&token='.$this->token);

        }


        //ajout le client des paniers abandonnés
          if (Tools::isSubmit('submit_ajout_panier_abandonne_client')){
            $id_devis = Tools::getValue('id_opartdevis');

            $customer = new Customer(Tools::getValue('id_client')); 
            $customer->panier_abandonne = 1;
            $customer->update();
            Tools::redirectAdmin(self::$currentIndex.'&id_opartdevis='.$id_devis.'&updateopartdevis&token='.$this->token);

        }


        //enlever le client des paniers abandonnés
          if (Tools::isSubmit('submit_woodzine_client')){
            $id_devis = Tools::getValue('id_opartdevis');

            $customer = new Customer(Tools::getValue('id_client')); 
            $customer->tw_woodzine = 0;
            $customer->update();
            Tools::redirectAdmin(self::$currentIndex.'&id_opartdevis='.$id_devis.'&updateopartdevis&token='.$this->token);

        }




        if (Tools::isSubmit('submit_pdf_devis')){
            $this->processDevisPdf(Tools::getValue('id_opartdevis'));

        }

        // send quotation to Customer by e-mail
        if (Tools::isSubmit('sendToCustomer')) {
            $this->processSendToCustomer(Tools::getValue('id_opartdevis'));
        }

        // send quotation to administrator by e-mail
        if (Tools::isSubmit('sendToAdmin')) {
            $this->processSendToAdmin(Tools::getValue('id_opartdevis'));
        }

        // validate quotation
        if (Tools::isSubmit('validate')) {
            $this->processValidation(Tools::getValue('id_opartdevis'));
        }

        // view quotation file (PDF)
        if (Tools::isSubmit('devisPdf')) {
            $this->processDevisPdf(Tools::getValue('id_opartdevis'));
        }

        // view quotation file (PDF)
        if (Tools::isSubmit('submitBulkupdateOrderStatusorder')) {
           $status = Tools::getValue('status');
           $devis = Tools::getValue('opartdevisBox');
           $this->UpdateStatusDevis($status,$devis);
           if($status == 7 || $status == 9){
             foreach ($devis as $id_devis) {
                $quotation = new OpartQuotation($id_devis);
                $this->processSendToCustomer($quotation->id_opartdevis, 0);
            }
           }
           
        }


         //mise à jour commercial globale
        if (Tools::isSubmit('submitBulkupdateCommerciaux')) {
           $employe = Tools::getValue('employe');
           $devis = Tools::getValue('opartdevisBox');
           $this->UpdateCommercial($employe,$devis);
           
        }



            


        // Create quotation based on cart (from adminCarts controller)
        if (Tools::getIsset('transformThisCartId')) {
            $this->processTransformCartToQuotation(Tools::getValue('transformThisCartId'));
        }

        return parent::postProcess();
    }

    public function ajaxProcessLoadCarrierList()
    {
        die(Tools::jsonEncode(
            (new OpartQuotation)->getCarriers(true)
        ));
    }

    public function ajaxProcessSearchCustomer()
    {
        $query = Tools::getValue('q', false);

        $sql =
            'SELECT c.`id_customer`, c.`firstname`, c.`lastname`, c.`email`
            FROM `'._DB_PREFIX_.'customer` c
            WHERE (
                c.firstname LIKE "%'.pSQL($query).'%"
                OR c.lastname LIKE "%'.pSQL($query).'%"
                OR c.email LIKE "%'.pSQL($query).'%"
            )
            GROUP BY c.id_customer';

        $customers = Db::getInstance()->executeS($sql);

        die(Tools::jsonEncode(
            $customers
        ));
    }

    public function ajaxProcessSearchProduct()
    {
        $query = Tools::getValue('q', false);
        $id_customer = Tools::getIsset('id_customer') ? Tools::getValue('id_customer') : null;

        $sql =
            'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, p.`price`, pl.`name`, p.`minimal_quantity`
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                pl.id_product = p.id_product
                AND pl.id_lang = '.(int)$this->context->language->id.'
            )
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON sa.id_product = p.id_product
            WHERE (
                pl.name LIKE "%'.pSQL($query).'%"
                OR p.reference LIKE "%'.pSQL($query).'%"
                OR p.id_product LIKE "%'.pSQL($query).'%"
            )
            AND p.active = 1
            AND sa.quantity > 1
            AND pl.id_shop = 1
            AND pl.id_lang = 5
            GROUP BY p.id_product';

        $products = Db::getInstance()->executeS($sql);

        $formated_products = array();
        foreach ($products as $product) {
            $product['name'] = $product['name'].' ['.$product['reference'].']';

            $specific_price_output = null;

            $price = Product::getPriceStatic(
                $product['id_product'],
                false,
                null,
                4,
                null,
                false,
                true,
                1,
                false,
                null,
                null,
                null,
                $specific_price_output,
                false,
                true,
                null,
                false
            );

            $reduced_price = Product::getPriceStatic(
                $product['id_product'],
                false,
                null,
                4,
                null,
                false,
                true,
                1,
                false,
                $id_customer,
                null,
                0,
                $specific_price_output,
                false,
                true,
                $this->context,
                true
            );

            $formated_products[] = array(
                'id_product' => $product['id_product'],
                'name' => $product['name'],
                'minimal_quantity' => $product['minimal_quantity'],
                'price' => $price,
                'reduced_price' => $reduced_price
            );
        }

        die(Tools::jsonEncode(
            $formated_products
        ));
    }

    public function ajaxProcessAddCartRule()
    {
        $id_cart = (int)Tools::getValue('id_cart');
        $id_cart_rule = (int)Tools::getValue('id_cart_rule');

        $cart = OpartQuotation::createCart($id_cart);
        $cart->getProducts();

        $this->context->cart = $cart;

        $cart_rule = new CartRule($id_cart_rule);

        $isNotValid = $cart_rule->checkValidity($this->context);

        if ($isNotValid) {
            die(Tools::jsonEncode(
                $isNotValid
            ));
        } else {
            die(Tools::jsonEncode(
                $cart_rule
            ));
        }
    }

    public function ajaxProcessDeleteCartRule()
    {
        $id_cart = Tools::getValue('id_cart');
        $id_cart_rule = Tools::getValue('id_cart_rule');

        $cart = new Cart($id_cart);

        $cart->removeCartRule($id_cart_rule);

        die(Tools::jsonEncode(
            $cart->update()
        ));
    }

    public function ajaxProcessLoadProductCombinations()
    {
        $id_product = Tools::getValue('id_product');

        $product = new Product($id_product);
        $combinations = OpartQuotation::getAttributesResume(
            $product->id,
            $this->context->language->id
        );

        if (empty($combinations)) {
            die();
        }

        $formated_combinations = array();
        foreach ($combinations as $combination) {
            $formated_combinations[$combination['id_product_attribute']] = $combination;
        }

        die(Tools::jsonEncode(
            $formated_combinations
        ));
    }

    public function ajaxProcessGetTotalCart()
    {
        $cart = OpartQuotation::createCart((int)Tools::getValue('id_cart'));

        $summary = $cart->getSummaryDetails(null, true);
        $summary['id_cart'] = $cart->id;
        $summary["group_tax_method"] = false;

        $customer = new Customer($cart->id_customer);

        if (function_exists('getPriceDisplayMethod')) {
            $summary["group_tax_method"] = (bool)Group::getPriceDisplayMethod($customer->id_default_group);
        }

        die(Tools::jsonEncode(
            $summary
        ));
    }

    public function ajaxProcessDeleteUploadedFile()
    {
        $directory = Tools::getValue('upload_id');
        $file = Tools::getValue('upload_name');

        Tools::deleteFile($directory.'/'.$file);

        die(Tools::jsonEncode(
            "{$file} successfully deleted..."
        ));
    }

    public function ajaxProcessDeleteSpecificPrice()
    {
        $id_cart = Tools::getValue('id_cart');

        die(Tools::jsonEncode(
            OpartQuotation::deleteSpecificPrice($id_cart)
        ));
    }

    public function ajaxProcessGetAddresses()
    {
        $adresse_delivery = Tools::getValue('adresse_delivery');
        $adresse_invoice = Tools::getValue('adresse_invoice');
        $id_customer = Tools::getValue('id_customer', false);
        


        $sql =
            'SELECT  a.`alias`, a.`id_address`, a.`lastname`, a.`firstname`, a.`lastname`, a.`company`,
            a.`address1`, a.`address2`, a.`postcode`, a.`city`, cl.`name` as `country_name`
            FROM `'._DB_PREFIX_.'address` a
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (
                a.`id_country` = cl.`id_country`
                AND cl.id_lang = '.(int)$this->context->language->id.'
            )
            WHERE a.id_customer = '.(int)$id_customer.' AND a.deleted = 0 AND a.active = 1';

        $addresses = Db::getInstance()->executeS($sql);


        $sql_delivery = 'SELECT  a.`alias`, a.`id_address`, a.`lastname`, a.`firstname`, a.`lastname`, a.`company`,
            a.`address1`, a.`address2`, a.`postcode`, a.`city`, cl.`name` as `country_name`
            FROM `'._DB_PREFIX_.'address` a
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (
                a.`id_country` = cl.`id_country`
                AND cl.id_lang = '.(int)$this->context->language->id.'
            )
            WHERE a.id_address = '.(int)$adresse_delivery.' AND a.deleted = 0 AND a.active = 1';

            $adresse_delivery = Db::getInstance()->executeS($sql_delivery);

            $sql_invoice = 'SELECT  a.`alias`, a.`id_address`, a.`lastname`, a.`firstname`, a.`lastname`, a.`company`,
            a.`address1`, a.`address2`, a.`postcode`, a.`city`, cl.`name` as `country_name`
            FROM `'._DB_PREFIX_.'address` a
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (
                a.`id_country` = cl.`id_country`
                AND cl.id_lang = '.(int)$this->context->language->id.'
            )
            WHERE a.id_address = '.(int)$adresse_invoice.' AND a.deleted = 0 AND a.active = 1';

            $adresse_invoice = Db::getInstance()->executeS($sql_invoice);

        if (!count($addresses)) {
            die(Tools::jsonEncode(array(
                'return' => false,
                'error' => $this->module->l('No address found')
            )));
        }

        die(Tools::jsonEncode(array(
            'return' => true,
            'addresses' => $addresses,
            'adresse_delivery' =>$adresse_delivery,
            'adresse_invoice' =>$adresse_invoice
            
        )));
    }

    public function ajaxProcessGetReducedPrices()
    {
        $id_cart = (int)Tools::getValue('id_cart');
        $id_customer = (int)Tools::getValue('opart_devis_customer_id', false);
        $who_is_list = Tools::getValue('whoIs');
        $attribute_list = Tools::getValue('add_attribute');
        $qty_list = Tools::getValue('add_prod');
        $specific_price_list = Tools::getValue('specific_price');

        // get cart and currency
        $cart = OpartQuotation::createCart($id_cart);
        $this->context->currency = new Currency($cart->id_currency);

        if (empty($who_is_list)) {
            die(Tools::jsonEncode(array(
                'return' => false,
                'error' => $this->module->l('No product found')
            )));
        }

        $reduced_prices = array();
        foreach ($who_is_list as $key => $id_product) {
            $id_attribute = (isset($attribute_list[$key])) ? $attribute_list[$key] : 0;

            $qty = $qty_list[$key];

            $your_price = ($specific_price_list[$key]) ? $specific_price_list[$key] : $this->getYourPrice($id_cart, $id_product, $id_attribute, $id_customer);

            $specific_price_output = null;
            $price = Product::getPriceStatic(
                $id_product,
                false,
                $id_attribute,
                2,
                null,
                false,
                true,
                1,
                false,
                null,
                null,
                null,
                $specific_price_output,
                false,
                false,
                null,
                false
            );

            if ($your_price == $price || !$your_price) {
                $use_customer_price = false;
            } else {
                $use_customer_price = true;
            }

            $reduced_price = Product::getPriceStatic(
                $id_product,
                false,
                $id_attribute,
                2,
                null,
                false,
                true,
                $qty,
                false,
                $id_customer,
                $cart->id,
                0,
                $specific_price_output,
                false,
                true,
                $this->context,
                $use_customer_price
            );

            $computed_id = $id_product.'_'.$id_attribute;

            switch (Configuration::get('PS_ROUND_TYPE')) {
                case Order::ROUND_TOTAL:
                    $reduced_prices[$key]['total'] = $reduced_price * $qty;
                    break;
                case Order::ROUND_LINE:
                    $reduced_prices[$key]['total'] = Tools::ps_round(
                        $reduced_price * $qty,
                        _PS_PRICE_COMPUTE_PRECISION_
                    );
                    break;
                case Order::ROUND_ITEM:
                default:
                    $reduced_prices[$key]['total'] = Tools::ps_round(
                            $reduced_price,
                            _PS_PRICE_COMPUTE_PRECISION_
                        ) * $qty;
                    break;
            }

            $reduced_prices[$key]['computed_id'] = $computed_id;
            $reduced_prices[$key]['real_price'] = $price;
            $reduced_prices[$key]['reduced_price'] = $reduced_price;
            $reduced_prices[$key]['your_price'] = $your_price;
        }

        die(Tools::jsonEncode(array(
            'return' => true,
            'id_cart' => $cart->id,
            'reduced_prices' => $reduced_prices,
        )));
    }

    public function processSendToCustomer($id_opartdevis, $redirect = 1)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (!Validate::isLoadedObject($quotation)
            || !$quotation->sendToCustomer($quotation)
        ) {
            $this->errors[] = Tools::displayError('Error : An error occured while sending the quotation to the customer');
        }

        if($redirect == 1){
        Tools::redirectAdmin(self::$currentIndex.'?conf=101&token='.$this->token);
        }
    }

    public function processSendToAdmin($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (!Validate::isLoadedObject($quotation)
            || !$quotation->sendToAdmin()
        ) {
            $this->errors[] = Tools::displayError('Error : An error occured while sending the quotation to the administrator');
        }

        Tools::redirectAdmin(self::$currentIndex.'?conf=102&token='.$this->token);
    }

    public function processValidation($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (!Validate::isLoadedObject($quotation)
            || !$quotation->validate()
        ) {
            $this->errors[] = Tools::displayError('Error : An error occured while validating the quotation');
        }

        Tools::redirectAdmin(self::$currentIndex.'?conf=103&token='.$this->token);
    }

    public function processDevisPdf($id_opartdevis)
    {
        $quotation = new OpartQuotation($id_opartdevis);

        if (!Validate::isLoadedObject($quotation)) {
            $this->errors[] = Tools::displayError('Error : An error occured while loading the quotation');
        }

        $quotation->renderPdf(true);
    }

    public function processTransformCartToQuotation($id_cart)
    {
        $cart = new Cart($id_cart);
        $customer = new Customer($cart->id_customer);
        $customer->panier_abandonne = 1;
        $customer->id_employe = 1;
        $customer->update();

        Context::getContext()->cart = $cart;
        Context::getContext()->customer = $customer;

        $quotation = OpartQuotation::createQuotation($cart, $customer, null, 'devis '.$customer->lastname. ' '.$customer->firstname, 'Devis Tropical Woods ', '', '', 'Panier abandonné', 0);



        if (!Validate::isLoadedObject($quotation)) {
            $this->errors[] = Tools::displayError('Error : An error occured while loading the quotation');
        }

        Tools::redirectAdmin(self::$currentIndex.'&id_opartdevis='.$quotation->id.'&updateopartdevis&token='.$this->token);
    }

 

    public function processDelete()
    {
        if (Validate::isLoadedObject($quotation = $this->loadObject())) {
            $cart = new Cart($quotation->id_cart);

            $id_order = Order::getOrderByCartId($cart->id);

            if ($id_order) {
                $this->errors[] = Tools::displayError('Error : Can\'t delete this quotation because it has been ordered');
            }
        }

        return parent::processDelete();
    }

    //Mise à jour des status en masse
    public function UpdateStatusDevis($status,$devis){

         $now = new DateTime();

        foreach ($devis as $id_devis) {
            $quotation = new OpartQuotation((int)$id_devis);
            $quotation->status = $status;
            $quotation->date_upd = $now->format('Y-m-d H:i:s');
            $resultat = $quotation->update();

            if($resultat == true){
                $this->confirmations[] = "Status mise à jour avec succès";
            }
            else {
                $this->errors[] = Tools::displayError("Une erreur est survenue lors de la mise à jour");
            }
        }
    }

    //mise à jour du commentaire
    public function UpdateCommentaire($id_devis, $commentaire){


        Db::getInstance()->update('opartdevis', array('commentaire' => pSQL($commentaire)), 'id_opartdevis = '.$id_devis );
       
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);

    }


    //mise à jour du commercial du client
    public function UpdateCustomer($id_client, $id_employe, $id_devis){

    
        $customer = new Customer($id_client); 
        $customer->id_employe = $id_employe;
        $customer->update();
        Tools::redirectAdmin(self::$currentIndex.'&id_opartdevis='.$id_devis.'&updateopartdevis&token='.$this->token);

    }

    public function UpdateCommercial($id_commercial, $quotations){
        foreach ($quotations as $id_devis) {
            $devis = new OpartQuotation($id_devis);

            $customer = new Customer($devis->id_customer);
            $customer->id_employe = $id_commercial;
            $customer->update();
        }
    }
}
