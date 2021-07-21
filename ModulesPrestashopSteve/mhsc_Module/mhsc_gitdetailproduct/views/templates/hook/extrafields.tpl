<div class="mb-1 mt-1">

    <fieldset class="form-group">

        <div class="form-check">

            {*Champ Standard *}
            <h2 class="form-control-label ">{l s='Guide des tailles' mod='mhsc_gitdetailproduct'} :</h2>
         
                <select class="select2-selection select2-selection--single" name="mhsc_git">
                    <option selected>{l s='Open this select menu' mod='mhsc_gitdetailproduct'}</option>
                    <option value="1" {if $mhsc_git == '1'}selected{/if}>{l s='Tour de poitrine' mod='mhsc_gitdetailproduct'}</option>
                    <option value="2" {if $mhsc_git == '2'}selected{/if}>{l s='Tour de taille' mod='mhsc_gitdetailproduct'}</option>
                    <option value="3" {if $mhsc_git == '3'}selected{/if}>{l s='Tour de hanches' mod='mhsc_gitdetailproduct'}</option>
                    <option value="4" {if $mhsc_git == '4'}selected{/if}>{l s='Tour de taille' mod='mhsc_gitdetailproduct'}</option>
                    <option value="5" {if $mhsc_git == '5'}selected{/if}>{l s='Tour de hanches' mod='mhsc_gitdetailproduct'}</option>
                    <option value="6" {if $mhsc_git == '6'}selected{/if}>{l s='Longueur de jambe' mod='mhsc_gitdetailproduct'}</option>
                </select>
            <div>

    </fieldset>

    <div class="clearfix"></div>
</div>