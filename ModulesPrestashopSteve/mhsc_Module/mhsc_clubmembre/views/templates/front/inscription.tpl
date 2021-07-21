{if !$customer.is_logged}

    {extends file='customer/page.tpl'}

    {block name='page_title'}

    {/block}

    {block name='page_content'}
    <div class="container">
        <div class='row'>
            <form method='post'>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required/>
                </div>
                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <input type="password" class="form-control" id="mdp" name="password" required/>
                </div>
                <div class="form-group">
                    <label for="anniv">Date d'anniversaire</label>
                    <input type="text" class="form-control" id="anniv" name="anniv" placeholder="31/05/1970" required/>
                    <small>(ex: 31/05/1970)</small>
                </div>

                <input type='submit' name='mhsc_clubmembre'/>
            </form>
        </div>
    </div>
    {/block}

    {block name='my_account_links'}
    {/block}

{else}
    {$urls.pages.identity}
{/if}
