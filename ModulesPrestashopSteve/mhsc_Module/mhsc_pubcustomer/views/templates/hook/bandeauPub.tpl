{if $affiche == 1}
    <div class="my-account-pub">
        <img class="my-account-pub-img" src="{$urls.base_url}/modules/mhsc_pubcustomer/views/assets/img/{$image}">
        <div class="my-account-pub-text">
            <p class="my-account-pub-title">{$titre}</p>
            <p class="my-account-pub-description">{$description}</p>
        </div>
        <div class="my-account-pub-wrap-btn">
            <a href="{$liens}" class="my-account-pub-btn">je découvre</a>
        </div>        
    </div>
{/if}