{if isset($noteMoyenne)}
    <div>
        {l s='Note Moyenne de l\'article :' mod='Vnsavisproduit'} {$noteMoyenne} / 5
    </div>
{/if}

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

{assign var="param" value=['id_product' => $id_produit]}

<a href="{$link->getModuleLink('Vnsavisproduit', 'Nouvellepage', $param)}">Tous les commentaires</a>



       