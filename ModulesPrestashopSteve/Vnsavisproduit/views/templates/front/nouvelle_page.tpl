{extends file="page.tpl"}

{block name="page_content"}

    <h2>Avis pour le produit {$product->name}</h2>
    
    <div>
        <ul id="avisProduit">
            {foreach from=$messagesProduct item='message'}
                <li>
                    Nom : {$message.nom} <br/>
                    Prenom : {$message.prenom} <br/>
                    Note : {$message.note} <br/>
                    Commentaire : {$message.avis} <br/>
                    Date : {$message.date_add}<br/>
                    <br/>
                </li>
            {/foreach}
        </ul>
    </div>

    <h2>Laissez un avis <h2>

    {block name='product_footer'}
        {hook h='displayCommentaireProduct' product=$product}
    {/block}

{/block}