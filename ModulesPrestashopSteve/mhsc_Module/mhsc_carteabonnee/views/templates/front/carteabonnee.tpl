{extends file='customer/page.tpl'}

{block name='page_title'}
{/block}

{block name='page_content' append}

    <form method="post">
    
        <div class="form-group">
            <label for="numero">Numero d'abonné</label>
            <input type="text" name="numero" class="form-control" id="numero" value="{$carteabonnee->numero}">
        </div>

        <button type="submit" name="mhsc_carteabonnee" class="btn btn-primary">Envoyer</button>

    </form>

{/block}