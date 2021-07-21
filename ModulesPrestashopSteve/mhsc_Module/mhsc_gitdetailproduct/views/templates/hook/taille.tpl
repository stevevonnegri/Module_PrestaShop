{if $product->mhsc_git > '0' && $product->mhsc_git < '7' && isset($groups['4']) || isset($groups['6']) || isset($groups['17']) || isset($groups['41']) }

    <p class="select-sizes-title">
        {l s="Sélectionnez une taille" mod="mhsc_gitdetailproduct"}
        <a href="#modal-sizes" data-toggle="modal" data-target="#modal-sizes">Guide des tailles
        </a>
    </p>



    {* Fenetre modal pour l'affichage des tailles en detail*}
    <div class="modal fade" id="modal-sizes">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>

                </div>
                <div class="modal-body">
                    <p>
                        image des tailles
                    </p>
                </div>

            </div>
        </div>
    </div>
{/if}