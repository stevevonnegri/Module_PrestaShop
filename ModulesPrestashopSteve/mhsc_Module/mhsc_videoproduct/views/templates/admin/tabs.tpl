<div class="mt-5"></div>
<div class="container-fluid">
    <form method='post' enctype='multipart/form-data' action="{$href|escape:'htmlall':'UTF-8'}">

        {if empty($video->name)}
            <div class="form-group">
                <h3>
                    <label class="col-lg-1" for="SearchChampProduit">
                        {l s='Products :' mod='videoproduct'}
                    </label>
                    <div class="col-lg-11">
                        <input type="hidden" value="" id="inputHiddenVideo" name="id_product_redirected" />
                        <div class="input-group">
                            <input type="text" id="SearchChampProduit" name="q"/>
                            <span class="input-group-addon"><i class="icon-search"></i></span>					
                        </div>
                    </div>
                    <p id="visionProd">
                    
                    </p>
                </h3>
            </div>
        {else}
        
        <label><h2>Id_product</h2></label><br/>
        <input type="number" name="id_product" {if !empty($video->id_products)} value="{$video->id_products}" {/if}/>

        <div>
            <h2>Activation de la video</h2>
            <div class="form-check">
                <input class="form-check-input" {if $video->active == 1} checked {/if} value='1' type="radio" name="active" id="actif1">
                <label class="form-check-label" for="actif1">
                    Activer la video
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" {if $video->active == 0} checked {/if} value='0' type="radio" name="active" id="actif2">
                <label class="form-check-label" for="actif2">
                    Désactiver la video
                </label>
            </div>
        </div>

        {/if}

        <br/>
        <label><h2>Vidéo</h2></label>
        <input type="file" name="video"/>
        <small>Changer de vidéo réactivera automatique les vidéos pour le produit</small>
        <br/>

        <br/>
        <input type="submit" name="submit_mhsc_video" value="envoyer"/>

    </form>


    {strip}
        {addJsDef ajaxUrl=$ajaxUrl}
        {addJsDef token=$token|escape:'htmlall':'UTF-8'}
    {/strip}
</div>