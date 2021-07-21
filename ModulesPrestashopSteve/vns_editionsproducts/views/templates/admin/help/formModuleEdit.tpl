{if isset($produits)}
    <div>
        <form method="POST">
        
            <p>Modification disponible</p>
                {foreach from=$produits item="produit"}  
                    <input type="hidden" name="produits[]" value="{$produit}"/>
                {/foreach}

            <div class="row m-auto">


                <select name="config" id="config" onchange="change_valeur();">
                    <option value="">--Please choose an option--</option>
                    {if $infoPrix == 1}<option class="jsform" id="ModifPrix" value="prix">{l s="Prix" mod="vns_editionsproducts"}</option>{/if}
                    {if $infoStock == 1}<option class="jsform" id="ModifStock" value="stock">{l s="Stock" mod="vns_editionsproducts"}</option>{/if}
                    {if $infoCat == 1}<option class="jsform" id="ModifCat" value="categories">{l s="Categories" mod="vns_editionsproducts"}</option>{/if}
                    {if $infoMarque == 1}<option class="jsform" id="ModifMarque" value="marque">{l s="Marque" mod="vns_editionsproducts"}</option>{/if}
                    <option class="jsform" id="delete" value="delete">{l s="Supprimer" mod="vns_editionsproducts"}</option>
                </select>

                {if $infoPrix == 1}
                    <div class="collapse collapseJs" id="PrixModule">
                        {* Section prix *}

                        <div>
                            <label for="positif">{l s="Augmentation du prix" mod="vns_editionsproducts"}</label>
                            <input checked type="radio" id="positif" value="plus" name="VolumeP">
                            <input type="radio" id="negatif" value="moins" name="VolumeP">
                            <label for="negatif">{l s="Diminuer du prix" mod="vns_editionsproducts"}</label>
                        </div>

                        <div>
                            <label for="entier">{l s="En entier" mod="vns_editionsproducts"}</label>
                            <input checked type="radio" id="entier" value="euro" name="typeAugmentation">
                            <input type="radio" id="pourcentage" value="%" name="typeAugmentation">
                            <label for="pourcentage">{l s="En pourcentage" mod="vns_editionsproducts"}</label>
                        </div>

                        <div>
                            <label>{l s="Quantifier la modification" mod="vns_editionsproducts"}</label>
                            <input type="number" name="prixValeur" min="0" id="prixValue">
                        </div>
                    </div>
                {/if}

                {if $infoStock == 1}
                    <div class="collapse collapseJs" id="StockModule">
                        {* Section stock *}
                        
                        <div>
                            <label for="positif">{l s="Augmentation" mod="vns_editionsproducts"}</label>
                            <input checked type="radio" id="positif" value="plus" name="VolumeS">
                            <input type="radio" id="negatif" value="moins" name="VolumeS">
                            <label for="negatif">{l s="Diminuer" mod="vns_editionsproducts"}</label>
                        </div>
                        <div>
                            <label>{l s="Indiquer la quantiter" mod="vns_editionsproducts"}</label>
                            <input type="number" name="stockValeur" min="0" id="stockValue">
                        </div>
                    </div>
                {/if}


                {if $infoCat == 1}
                    <div class="collapse collapseJs" id="CatModule">
                        {* Section categories *}
                        
                        <div>
                            <select name="categ" id="categ" onchange="change_cat();">
                                {foreach from=$listCategories item='item'}
                                    <option id="{$item.infos.id_category}" value="{$item.infos.id_category}">{$item.infos.name}</option>
                                {/foreach}
                            </select>
                        </div>

                        {* Code pour les sous categories de produits *}
                        {* <div>
                            {foreach from=$listcat item='item' key="k" }
                                <select name="item">
                                    {foreach from=$item item='cat'}
                                        <option value="{$cat.infos.id_category}">{$cat.infos.name}</option>
                                    {/foreach}
                                </select>
                            {/foreach}
                        </div> *}
                    </div>
                {/if}

                {if $infoMarque == 1}
                    <div class="collapse collapseJs" id="MarqueModule">
                        {* Section marque *}
                        <div>
                            <select name="marque" id="marque">
                                {foreach from=$listmarques item='item'}
                                    <option value="{$item.id_manufacturer}">{$item.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}

                <input type="submit" value="Envoyer" name="submit-Module">
            </div>
        </form>
    </div>
{/if}
