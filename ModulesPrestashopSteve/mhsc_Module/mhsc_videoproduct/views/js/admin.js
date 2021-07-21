$(document).ready(function(){

    $("#SearchChampProduit").autocomplete (
        ajaxUrl,
        {
            minChars: 0,
            autoFill: true,
            max: 200,
            matchContains: true,
            scroll: false,
            dataType: 'JSON',
            cacheLength: 0,
            extraParams: {
                ajax: true,
                action: 'SearchProduct',
                token: token,
            },
            parse: function(formated_array) {
                var formated_products = new Array();

                for (var i = 0; i < formated_array.length; i++) {
                    formated_products[i] = {
                        data: formated_array[i],
                        value: (
                            formated_array[i].id_product
                            + ' - ' + formated_array[i].name
                            + ' - ' + formated_array[i].reference
                        ).trim()
                    };
                }
                return formated_products;
            },
            formatItem: function(data, i, max, value, term) {
                return value;
            },
        }
    ).result(function(e, product){
        if (product != undefined) {
            getProductChoise(
                product.id_product,
                product.name,
            );
        }
        $(this).val('');
    });
});

function getProductChoise(prodId, prodName) 
{
    $("#inputHiddenVideo").val(prodId);
    $('#visionProd').append(prodName)
}
