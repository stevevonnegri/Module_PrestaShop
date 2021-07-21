{extends file='customer/page.tpl'}

{block name='page_title'}
{/block}

{block name='page_content' append}

<form method="post">

    <div class="form-group">
        <label for="id_client">Id client</label>
        <input type="number" class="form-control" name='id' id="id_client">
    </div>
    <div class="form-group">
        <label for="formGroupExampleInput2">Code référence</label>
        <input type="text" class="form-control" name="reference" id="formGroupExampleInput2">
    </div>
    <br/>

    <button type="submit" name="mhsc_clubmembre" class="btn btn-primary">Envoyer</button>

</form>

{/block}