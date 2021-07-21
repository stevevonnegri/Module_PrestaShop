{if ($commentaire.connection == 1)}

    {if (!$customer.is_logged) }
        vous devez etre connecter
        <a href="{$urls.pages.authentication}">Se connecter</a>

    {else}

        <form method="POST">

            {if ($commentaire.note == 1)}

                <label>Note l'article</label>
                <select name="noteArticle" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select><br/>
                
            {/if}
        
            <label>Votre commentaire sur le produit</label>
            <textarea name="commentaireArticle" required></textarea>
            <input type="submit" name="note_article">
        </form> 
    {/if}

{else}

    {if (!$customer.is_logged) }

        <form method="POST">
            <label>Nom</label>
            <input type="text" name="name_avis" required>
            <label>prenom</label>
            <input type="text" name="prenom_avis" required><br/>
            <label>email</label>
            <input type="email" name="email_avis" required><br/>

    {else}
    
    <form method="POST">

    {/if}

    {if ($commentaire.note == 1)}

            <label>Note l'article</label>
            <select name="noteArticle" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select><br/>

    {/if}

    
    <label>Votre commentaire sur le produit</label>
    <textarea name="commentaireArticle" required></textarea>
    <input type="submit" name="note_article">

</form>  
{/if}