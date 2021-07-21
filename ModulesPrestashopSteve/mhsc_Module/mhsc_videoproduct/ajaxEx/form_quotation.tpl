{**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 *}

 <script src="https://cdn.tiny.cloud/1/4e9chzdyqwartx84o72y7w5g5u3tksuraabvns04sw3643nd/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>tinymce.init({ selector:'#message_visible' });</script>
   <script>tinymce.init({ selector:'#corps_mail' });</script>

<form action="{$href|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" id="opartDevisForm">	
    <input type="hidden" value="{if ($quotation->id_cart)}{$quotation->id_cart|escape:'htmlall':'UTF-8'}{/if}" name="id_cart" id="opart_devis_id_cart" />

    {if isset($quotation->id_opartdevis) && $quotation->id_opartdevis}
        <input type="hidden" value="{$quotation->id_opartdevis|escape:'htmlall':'UTF-8'}" name="id_opartdevis" />
    {/if}
    <div class="row">
    <!-- name -->
    <div class="panel col-lg-6">
        <div class="panel">
        <h3><i class="icon-list-alt"></i> {l s='Quotation' mod='opartdevis'}</h3>
        {if isset($quotation->date_add)}<h4 class="panel-heading">Date envoie devis : {$quotation->date_add|date_format:"%d-%m-%Y à  %H : %M"}</h4> {/if}
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Quotation name :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <input type="text" value="{if isset($quotation)}{$quotation->name|escape:'htmlall':'UTF-8'}{/if}" name="quotation_name" />
                </div>
            </div>
        </div>
         <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Objet du mail:' mod='opartdevis'} *
                </label>
                <div class="col-lg-6">
                    <input type="text" value="{if isset($quotation)}{$quotation->objet|escape:'htmlall':'UTF-8'}{/if}" name="devis_objet" required/>
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Message :' mod='opartdevis'}
                </label>
                <div class="col-lg-9">			
                    <textarea name="message_visible" id="message_visible">{if isset($quotation->message_visible) && $quotation->message_visible!=""}{$quotation->message_visible|escape:'htmlall':'UTF-8'}{else}EN STOCK, sauf vente entre temps, livraison sous 15 jours à réception de votre règlement. <br/>Nos stocks sont variables. Réservez votre parquet au plus vite ! <br/>Réservation / préparation / fabrication de votre commande avec un acompte de 30%<br/>Le solde avant départ net d'escompte{/if}</textarea>	
                    <p class="help-block">{l s='Visible on quotation.' mod='opartdevis'}</p>						
                </div>
            </div>
        </div>
         <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Message du mail :' mod='opartdevis'}
                </label>
                <div class="col-lg-9">          
                    <textarea name="corps_mail"  id="corps_mail">{if empty($quotation->corps_mail)}
        Chère Madame, Cher Monsieur,<br/><br/>
        Vous trouverez ci-joint notre offre de prix au format PDF.<br/><br/>
        EN STOCK, sauf vente entre temps, livraison sous 15 jours à réception de votre règlement.<br/><br/>
        Réservation / fabrication / préparation de votre commande avec un acompte de 30%. <br/>Le solde avant départ net d'escompte<br/>
        {else}
        {$quotation->corps_mail}
        {/if}</textarea>  
                    <p class="help-block">{l s='Message du mail personnalisable' mod='opartdevis'}</p>                        
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_product_autocomplete_input">
                    {l s='Attachments (5MB max) :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <div class="form-group">
                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
                        <input id="file-name" type="file" name="fileopartdevis[]" multiple enctype="multipart/form-data">
                    </div>
                    {if (is_dir($upload_path) && $quotation->id_opartdevis)}
                        {assign var=files value=opendir($upload_path)}
                        {while $file = readdir($files)}
                            {if $file != '.' AND $file != '..'}
                                <div class="">
                                    <a href="{$upload_url}/{$file|escape:'htmlall':'UTF-8'}" target="_blank">{$file|escape:'htmlall':'UTF-8'}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button type="button" class="delete_attachement" data-name="{$file|escape:'htmlall':'UTF-8'}" data-id="{$upload_path|escape:'htmlall':'UTF-8'}" style="background: transparent; border: 0px; padding: 0px; opacity:0.2px; -webkit-appearance: none;" data-dismiss="alert"><i class="icon-trash"></i></button>
                                </div>
                            {/if}
                        {/while}
                        {closedir($files)}{* can't escape *}
                    {/if}
                </div>
            </div>
        </div>
        </div>
           <!-- Commentaire -->
     <div class="panel">
        <h3><i class="icon-list-alt"></i> {l s='Message privé' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Commentaire :' mod='opartdevis'}
                </label>
                {if isset($cart)}
                <div class="col-lg-9">
                      <textarea name="devis_commentaire"  id="devis_commentaire" rows="6">{if empty($quotation->commentaire)}{else} {$quotation->commentaire} {/if}</textarea> 
                </div>
                <button id="submit_commentaire_devis" name="submit_commentaire_devis" class="btn btn-default pull-right" style="margin-top:10px;">
                    Enregistrer le commentaire
                </button>
                {else}
                <div class="alert alert-warning">
                    <p>La section commentaire est disponible seulement quand le devis a été enregistré une première fois (ex: statut devis préparé)</p>
                </div>
                {/if}
            </div>
        </div>
    </div>
    </div>
    <div class=" col-lg-6">
        <!-- user -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Customer' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group redirect_product_options redirect_product_options_product_choise">   
                <label class="control-label col-lg-3" for="opart_devis_customer_autocomplete_input">
                    {l s='choose customer:' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <input type="hidden" value="" name="id_product_redirected" />
                    <div class="input-group">
                        <input type="text" id="opart_devis_customer_autocomplete_input" name="opart_devis_customer_autocomplete_input" autocomplete="off" class="ac_input" />
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                    </div>
                    <p class="help-block">{l s='Start by typing the first letters of the customer\'s firstname or lastname, then select the customer from the drop-down list.' mod='opartdevis'}</p>                
                    <h2>
                        <i class="icon-user"></i> 
                        <span href="" id="opart_devis_customer_info"><span style="color:red">{l s='Please choose a customer' mod='opartdevis'}</span></span>
                    </h2>
                    {if isset($cart)}   
                    {assign var="test" value=$cart->getSummaryDetails()}
                    <h2><i class="icon-phone"></i> {$test.delivery->phone} {$test.delivery->phone_mobile}</h2>
                    {/if}
                </div>
                <div class="col-lg-3">
                        <span class="form-control-static">{l s='Or' d='Admin.Global'}&nbsp;</span>
                        <a class="fancybox_customer btn btn-default" href="{$link->getAdminLink('AdminCustomers', true, [], ['addcustomer' => 1, 'liteDisplaying' => 1, 'submitFormAjax' => 1])|escape:'html':'UTF-8'}#">
                            <i class="icon-plus-sign-alt"></i>
                            {l s='Add new customer' d='Admin.Orderscustomers.Feature'}
                        </a>
                    </div>
                <input type="hidden" name="opart_devis_customer_id" id="opart_devis_customer_id" value=""/>

            </div>
            

        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_customer_autocomplete_input">
                    {l s='Invoice address:' mod='opartdevis'}
                </label>
                <div class="col-lg-6">
                    <select id="opart_devis_invoice_address_input" name="invoice_address">{if isset($cart)}
                        {assign var="test" value=$cart->getSummaryDetails()}
                        <option class="test" value="{$cart->id_address_invoice}">[{$test.delivery->alias}] - {if !empty($test.delivery->company)}{$test.delivery->company} - {/if} {$test.delivery->firstname} {$test.delivery->lastname} - {$test.delivery->address1} -  {$test.delivery->postcode} - {$test.delivery->city} - {$test.delivery->country}</option>
                        {/if}
                    </select> 
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_customer_autocomplete_input">
                    {l s='delivery address:' mod='opartdevis'}
                </label>                    
                <div class="col-lg-6">
                    <select id="opart_devis_delivery_address_input" name="delivery_address"></select>               
                    <p class="help-block">{l s='First, you have to choose a customer and you will be able to choose one of his addresses.' mod='opartdevis'}</p>
                </div>          
            </div>
            <input type="hidden" name="selected_invoice" id="selected_invoice" value="{if isset($cart->id_address_invoice)}{$cart->id_address_invoice|escape:'htmlall':'UTF-8'}{/if}" />
            <input type="hidden" name="selected_delivery" id="selected_delivery" value="{if isset($cart->id_address_delivery)}{$cart->id_address_delivery|escape:'htmlall':'UTF-8'}{/if}" />
            <div class="row">
                    <input type="hidden" name="opart_adress_delivery" id="opart_adress_delivery" value="{if isset($cart)}{$cart->id_address_delivery}{else}0{/if}"/>
                  <input type="hidden" name="opart_adress_invoice" id="opart_adress_invoice" value="{if isset($cart)}{$cart->id_address_invoice}{else}0{/if}"/>
                {if isset($cart)}
                {assign var="test" value=$cart->getSummaryDetails()}
                    <div class="col-lg-3">
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <p><i class="icon-truck"></i> <strong>Adresse de livraison sélectionnée - {$cart->id_address_invoice}</strong><br/>
                                     {$test.delivery->firstname} {$test.delivery->lastname}<br/>
                                     {if !empty($test.delivery->company)}{$test.delivery->company}<br/>{/if}
                                     {$test.delivery->address1}<br/>
                                     {if !empty($test.delivery->address2)}{$test.delivery->address2}<br/>{/if}
                                     {$test.delivery->postcode} {$test.delivery->city}<br/>
                                     {$test.delivery->country}<br/>
                                     {$test.delivery->phone}  {$test.delivery->phone_mobile}
                                </p>
                                <a class="fancybox btn btn-default" href="{$link->getAdminLink('AdminAddresses', true, [], ['updateaddress' => 1])}&id_address={$cart->id_address_delivery}">Modifier cette adresse</a>
                                <p><small>Les changements seront visible seulement à l'enregistrement ou si vous rafraichissez la page</small></p>
                            </div>
                            <div class="col-lg-6">
                                <p><i class="icon-file-text"></i> <strong>Adresse de facturation sélectionnée</strong><br/>
                                     {$test.invoice->firstname} {$test.invoice->lastname}<br/>
                                     {if !empty($test.invoice->company)}{$test.invoice->company}<br/>{/if}
                                     {$test.invoice->address1}<br/>
                                     {if !empty($test.invoice->address2)}{$test.invoice->address2}<br/>{/if}
                                     {$test.invoice->postcode} {$test.invoice->city}<br/>
                                     {$test.invoice->country}<br/>
                                     {$test.invoice->phone}  {$test.invoice->phone_mobile}
                                    </p>
                                      <a class="fancybox btn btn-default" href="{$link->getAdminLink('AdminAddresses', true, [], ['updateaddress' => 1, 'realedit' => 1, 'liteDisplaying' => 1, 'submitFormAjax' => 1])}&id_address={$cart->id_address_invoice}#">Modifier cette adresse</a>
                                      <p><small>Les changements seront visible seulement à l'enregistrement ou si vous rafraichissez la page</small></p>
                                </p>
                            </div>
                        </div>
                    </div>
                {/if}
            <div class="col-lg-12">
                <a class="fancybox btn btn-default" id="new_address" href="{$link->getAdminLink('AdminAddresses', true, [], ['addaddress' => 1, 'id_customer' => 42, 'liteDisplaying' => 1, 'submitFormAjax' => 1])|escape:'html':'UTF-8'}#">
                    <i class="icon-plus-sign-alt"></i>
                    {l s='Add a new address' d='Admin.Orderscustomers.Feature'}
                </a>
            </div>
        </div>
          <div class="panel-footer">
            <button id="opart_devis_refresh_adress_list" class="btn btn-default pull-right">
                <i class="process-icon-refresh"></i>
                {l s='Rafraichir les addresses' mod='opartdevis'}
            </button>
        </div>
        </div>
    </div>
    <!-- Commercial -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Commercial' mod='opartdevis'}</h3>
        
        <div class="form-horizontal">
            <div class="form-group">
                <input type="hidden" value="{$customer->id}" name="id_client" />
                <label class="control-label col-lg-12" style="text-align:left;">
                    {l s='Attribuer un commercial à ce client :' mod='opartdevis'}

                </label>
                {if isset($cart)}
                  
                 <select name="commercial" id="commercial" class="col-lg-12">
                    <option></option>
                    {foreach from=$commerciaux item=commercial}
                        <option value="{$commercial.id_employee}" {if ($customer->id_employe  == $commercial.id_employee)}selected{/if}>{$commercial.firstname}</option>
                    {/foreach}
                    	 <option value="100" {if ($customer->id_employe  == 100)}selected{/if}>Showroom</option>             
                </select>
                <button id="submit_commercial_client" name="submit_commercial_client" class="btn btn-default pull-right" style="margin-top:10px;">
                    Choisir le commercial
                </button>
                {else}
                <div class="alert alert-warning">
                    <p>La section 'choisir un commercial' est disponible seulement quand le devis a été enregistré une première fois (ex: statut devis préparé)</p>
                </div>
                {/if}

            </div>
                        {if $customer}
                            {if $customer->panier_abandonne == 1 }    
                             <div class="form-group"><button id="submit_panier_abandonne_client" name="submit_panier_abandonne_client" class="btn btn-default pull-right" style="margin-top:10px;">
                                Enlever le client des paniers abandonnés
                            </button></div>
                            {else}
                            	<div class="form-group"><button id="submit_ajout_panier_abandonne_client" name="submit_ajout_panier_abandonne_client" class="btn btn-default pull-right" style="margin-top:10px;">
                                Ajouter le client dans les paniers abandonnés
                            	</button></div>
                            {/if}
                             {if $customer->tw_woodzine == 1 }    
                             <div class="form-group"><button id="submit_woodzine_client" name="submit_woodzine_client" class="btn btn-default pull-right" style="margin-top:10px;">
                                Enlever le client des woodzine
                            </button></div>
                            {/if}    
                     {/if}
        </div>
    </div>

   
</div>
</div>

  
     

    <!-- Cart -->
    <div class="panel">
        <h3><i class="icon-shopping-cart"></i> {l s='Cart' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="panel">
                <div class="form-group">
                    <h3>
                        <label class="col-lg-1" for="opart_devis_product_autocomplete_input">
                            {l s='Products :' mod='opartdevis'}
                        </label>
                        <div class="col-lg-11">
                            <input type="hidden" value="" name="id_product_redirected" />
                            <div class="input-group">
                                <input type="text" id="opart_devis_product_autocomplete_input" name="opart_devis_product_autocomplete_input" autocomplete="off" class="ac_input" />
                                <span class="input-group-addon"><i class="icon-search"></i></span>					
                            </div>
                             <p class="help-block">{l s='Recherche par nom ou ID produit. Il recherche seulement les produits actifs ou en stock' mod='opartdevis'}</p>
                        </div>
                    </h3>
                    <div class="col-lg-12">
                        <table class="table product" id="opartDevisProdList">
                            <thead>
                                <tr>
                                    <th style="width:5%">{l s='Id' mod='opartdevis'}</th>
                                    <th>{l s='Name' mod='opartdevis'}</th>
                                    <th>{l s='Attributes' mod='opartdevis'}</th>
                                    <th style="width:10%">{l s='Catalog price without tax' mod='opartdevis'}</th>
                                    <th style="width:10%">{l s='Your price' mod='opartdevis'}</th>
                                    <th style="width:10%">{l s='Reduced price without tax' mod='opartdevis'}</th>
                                    <th style="width:10%">{l s='Quantity' mod='opartdevis'}</th>
                                    <th style="width:10%">{l s='Total' mod='opartdevis'}</th>
                                    <th style="width:3%">&nbsp;</th>
                                </tr>	
                            </thead>
                        </table>	
                        <p class="help-block">{l s='Quand vous modifier la quantité, tapper sur entré pour lui permettre de récupérer son dégressif. Possible de définir un prix unitaire spécifique sans savoir besoin de créer un nouveau dégressif' mod='opartdevis'}</p>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div id="opartDevisCartRulesMsgError" style="display:none;"></div>
            </div>
            <div class="panel">
                <div class="form-group">
                    <h3>
                        <label class="col-lg-1" for="opart_devis_product_autocomplete_input">
                            {l s='Discounts :' mod='opartdevis'}
                        </label>
                        <div class="col-lg-1">
                            <div class="input-group">
                                <select id="opart_devis_select_cart_rules">
                                    {if count($cart_rules)>0}
                                        <option value="-1">--- {l s='cart rules' mod='opartdevis'} ---</option>
                                        {foreach $cart_rules as $rule}
                                            <option value="{$rule.id_cart_rule|escape:'htmlall':'UTF-8'}">{$rule.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    {else}						
                                        <option value="-1">--- {l s='no cart rules avaibles' mod='opartdevis'} ---</option>
                                    {/if}
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <span class="form-control-static">{l s='Or' d='Admin.Global'}&nbsp;</span>
                            <a class="fancybox btn btn-default" href="{$link->getAdminLink('AdminCartRules', true, [], ['addcart_rule' => 1, 'liteDisplaying' => 1, 'submitFormAjax' => 1])|escape:'html':'UTF-8'}#">
                                <i class="icon-plus-sign-alt"></i>
                                {l s='Add new voucher' d='Admin.Orderscustomers.Feature'}
                            </a>
                        </div>
                        <div class="panel-footer">
                                <button  name="submitRefreshVoucher" id="submitRefreshVoucher" class="btn btn-default pull-right"  type="submit" >
                                    <i class="process-icon-refresh"></i>
                                    {l s='Rafraichir les bons de réduction' mod='opartdevis'}
                                </button>
                    </div> 
                    </h3>
                    <div class="col-lg-12">
                        <table class="table product" id="opartDevisCartRuleList">
                            <thead>
                                <tr>
                                    <th style="width:5%">{l s='Id' mod='opartdevis'}</th>
                                    <th>{l s='Name' mod='opartdevis'}</th>
                                    <th>{l s='Description' mod='opartdevis'}</th>
                                    <th>{l s='Code' mod='opartdevis'}</th>
                                    <th>{l s='Free shipping' mod='opartdevis'}</th>
                                    <th>{l s='Reduction percent' mod='opartdevis'}</th>
                                    <th>{l s='Reduction amount' mod='opartdevis'}</th>
                                    <th>{l s='Reduction type' mod='opartdevis'}</th>
                                    <th>{l s='Gift product' mod='opartdevis'}</th>
                                    <th>&nbsp;</th>
                                </tr>	
                            </thead>
                        </table>	
                    </div>
                </div>
            </div>
		</div>
    </div>
    <!-- Shipping -->
    {if ($cart->id_carrier == 56)}
    <div class="panel" >
        <h3><i class="icon-truck"></i> {l s='Carriers' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="opart_devis_product_autocomplete_input">
                    {l s='Select a carrier :' mod='opartdevis'}
                </label>
                <div class="col-lg-6">			
                    <select id="opart_devis_carrier_input" name="opart_devis_carrier_input" onchange="$('#selected_carrier').val($(this).val())" class="calcTotalOnChange"></select>	
                    <p class="help-block">{l s='First you have to choose customer, addresses and all products then click on the reload button and you will be able to choose a carrier.' mod='opartdevis'}</p>				
                </div>
                <input type="hidden" name="selected_carrier" value="{if isset($cart->id_carrier)}{$cart->id_carrier|escape:'htmlall':'UTF-8'}{/if}" id="selected_carrier" />
            </div>
        </div>
        <div class="panel-footer">
            <button id="opart_devis_refresh_carrier_list" class="btn btn-default pull-right">
                <i class="process-icon-refresh"></i>
                {l s='Reload carrier list' mod='opartdevis'}
            </button>
        </div>
    </div>
    {/if}

    <!-- TOTAL -->
    <div class="panel">
        <h3><i class="icon-list"></i> {l s='Total' mod='opartdevis'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" style="padding-top:0">
                    {l s='Products (tax excl.)' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalProductHt"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3" style="padding-top:0">
                    {l s='Discounts (tax excl.)' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalDiscountsHt"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3" style="padding-top:0">
                    {l s='Shipping (tax excl.)' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalShippingHt"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3" style="padding-top:0">
                    {l s='Tax' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalTax"></span></div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3" style="padding-top:0; font-size:1.5em;">
                    {l s='Total (tax incl.)' mod='opartdevis'} :
                </label>
                <div class="col-lg-9"><span id="totalQuotationWithTax" style="color:red; font-weight:bold; font-size:1.5em;"></span></div>
            </div>
            <div class="panel-footer">
                <button id="opart_devis_refresh_total_quotation" class="btn btn-default pull-right">
                    <i class="process-icon-refresh"></i>
                    {l s='Refresh Total' mod='opartdevis'}
                </button>
                {if !empty($quotation->id)}
                <button id="submit_pdf_devis" name="submit_pdf_devis" class="btn btn-default pull-right">
                   <i class="material-icons dp48">file_download</i>
                    {l s='Télécharger le devis' mod='opartdevis'}
                </button>
                {/if}
              
            </div>
        </div>
    </div>

    <!--statut-->
        <div class="panel">
                <h3><i class="icon-list-alt"></i> {l s='Statut actuel  : ' mod='opartdevis'} {$statut}</h3>
            <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-lg-3" for="statut_devis">{l s='Modifier le statut :' mod='opartdevis'}</label>
                 <div class="col-lg-6">         
                    <select id="statut_devis" name="statut_devis">
                       {if isset($quotation->status)} <option value="{$quotation->status}">{$statut}</option>{else} <option value="0">Devis préparé</option>{/if}
                        <option value="0">Devis préparé</option>
                        <option value="1">Devis envoyé</option>
                        <option value="10">Devis à modifier</option>
                        <option value="11">Msge tel + sms</option>
                        <option value="13">Echantillon demandé</option>
                        <option value="12">Echantillon envoyé</option>
                        <option value="4">1<sup>ier</sup> relance</option>
                        <option value="5">2<sup>ème</sup> relance</option>
                        <option value="6">Relance rapide</option>
                         <option value="9">Frais de port -50%</option>
                        <option value="7">Actualité de votre projet</option>
                        <option value="8">Devis rejeté</option>
                    </select>    
                    <p class="help-block">{l s='Les statuts devis envoyé et actualité de votre projet envoient un mail' mod='opartdevis'}</p>              
                </div>
            </div>
            <div class="panel-footer">
                <a href="{$hrefCancel|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                    <i class="process-icon-cancel"></i>
                    {l s='cancel' mod='opartdevis'}
                </a>
                  <button id="opartBtnSubmit" disable="true" type="submit" name="submitAddOpartDevis" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='save' mod='opartdevis'}</button>     
            </div>
            </div>
        </div>
</form>

{strip}
    {addJsDef ajaxUrl=$ajax_url}
    {addJsDef token=$token|escape:'htmlall':'UTF-8'}
    {addJsDef id_lang_default=$id_lang_default|intval}
    {addJsDef currency_sign=$currency_sign}
    {addJsDefL name=specific_price_txt}{l s='Specific price' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=from_qty_text}{l s='from' js=1 mod='opartdevis'}{/addJsDefL}
    {addJsDefL name=qty_text}{l s='quantity' js=1 mod='opartdevis'}{/addJsDefL}
{/strip}

<script type="text/javascript">
    $(document).ready(function() {
        {if $customer}
            opartDevisAddCustomerToQuotation(
                {$customer->id|escape:'htmlall':'UTF-8'},
                '{$customer->firstname|escape:'htmlall':'UTF-8'}',
                '{$customer->lastname|escape:'htmlall':'UTF-8'}',
                '{$customer->email|escape:'htmlall':'UTF-8'}'
            );
        {/if}

        {if $cart}
            {foreach $products AS $product}
                opartDevisAddProductToQuotation(
                    {$product.id_product|escape:'htmlall':'UTF-8'},
                    '{$product.name|escape:'htmlall':'UTF-8'}',
                    '{$product.catalogue_price|escape:'htmlall':'UTF-8'}',
                    {$product.cart_quantity|escape:'htmlall':'UTF-8'},
                    {$product.id_product_attribute|escape:'htmlall':'UTF-8'},
                    '{$product.specific_price|escape:'htmlall':'UTF-8'}',
                    '{$product.your_price|escape:'htmlall':'UTF-8'}',
                    '{$product.customization_datas_json}',
                    '{$product.total}'
                );
            {/foreach}
        {/if}

        {if $cart && !empty($summary.discounts)}
            {foreach $summary.discounts AS $rule}
                {if $rule.reduction_product == -2}
                    reduction_type = "{l s='selected product' mod='opartdevis'}"
                {else if $rule.reduction_product == -1}
                    reduction_type = "{l s='cheapest product' mod='opartdevis'}"
                {else if $rule.reduction_product == 0}
                    reduction_type = "{l s='order' mod='opartdevis'}"
                {else}
                    reduction_type = "{l s='specific product' mod='opartdevis'} ({$rule.reduction_product})"
                {/if}

                opartDevisAddRuleToQuotation(
                    {$rule.id_cart_rule|escape:'htmlall':'UTF-8'},
                    '{$rule.name|escape:'htmlall':'UTF-8'}',
                    '{$rule.description|escape:'htmlall':'UTF-8'}',
                    '{$rule.code|escape:'htmlall':'UTF-8'}',
                    {$rule.free_shipping|escape:'htmlall':'UTF-8'},
                    '{$rule.reduction_percent|escape:'htmlall':'UTF-8'}',
                    '{$rule.reduction_amount|escape:'htmlall':'UTF-8'}',
                    reduction_type,
                    {$rule.gift_product|escape:'htmlall':'UTF-8'}
                );
            {/foreach}
        {/if}

        resetBind();

            function resetBind()
    {
        $('.fancybox').fancybox({
            'type': 'iframe',
            'width': '90%',
            'height': '90%',
        });

        $('.fancybox_customer').fancybox({
            'type': 'iframe',
            'width': '90%',
            'height': '90%',
        });
        /*$("#new_address").fancybox({
            onClosed: useCart(id_cart)
        });*/

    }


        opartDevisPopulateSelectCarrier({$json_carrier_list});
    });
</script>
