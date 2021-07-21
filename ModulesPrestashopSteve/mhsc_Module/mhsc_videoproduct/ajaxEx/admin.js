/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function(){

    // Customer Auto-complete
    $('#opart_devis_customer_autocomplete_input').autocomplete(
        ajaxUrl,
        {
            minChars: 1,
            autoFill: true,
            max: 200,
            matchContains: true,
            scroll: false,
            dataType: 'JSON',
            cacheLength: 0,
            extraParams: {
                ajax: true,
                action: 'SearchCustomer',
                token: token
            },
            parse: function(customers) {
                var formated_customers = new Array();

                for (var i = 0; i < customers.length; i++) {
                    formated_customers[i] = {
                        data: customers[i],
                        value: (
                            customers[i].id_customer
                            + ' - ' + customers[i].lastname
                            + ' - ' + customers[i].firstname
                            + ' - ' + customers[i].email
                        ).trim()
                    };
                }

                return formated_customers;
            },
            formatItem: function(data, i, max, value, term) {
                return value;
            }
        }
    ).result(function(e, customer){
        if (customer != undefined) {
            opartDevisAddCustomerToQuotation(
                customer['id_customer'],
                customer['lastname'],
                customer['firstname'],
                customer['email']
            );
        }

        $(this).val('');
    });


	// Product Auto-complete
    $('#opart_devis_product_autocomplete_input').autocomplete(
        ajaxUrl,
        {
            minChars: 3,
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
                id_customer: function () {
                    return $('#opart_devis_customer_id').val()
                }
            },
            parse: function(products) {
                var formated_products = new Array();

                for (var i = 0; i < products.length; i++) {
                    formated_products[i] = {
                        data: products[i],
                        value: (
                            products[i].id_product
                            + ' - ' + products[i].name
                            + ' - ' + products[i].price
                            + ' - ' + products[i].reduced_price
                        ).trim()
                    };
                }

                return formated_products;
            },
            formatItem: function(data, i, max, value, term) {
                return value;
            }
        }
    ).result(function(e, product){
        if (product != undefined) {
            opartDevisAddProductToQuotation(
                product.id_product,
                product.name,
                product.price,
                1,
                0,
                product.reduced_price,
                null,
                null,
                product.reduced_price,
                product.minimal_quantity
            );
        }

        $(this).val('');
    });

    $('#refreshvoucher').click(function(e) {
        e.preventDefault();

        refreshVoucherList();
    });

$('#opart_devis_refresh_carrier_list').click(function(e) {
        e.preventDefault();

        opartDevisLoadCarrierList();
});

    $('#opart_devis_refresh_adress_list').click(function(e) {
        e.preventDefault();

        returnadresses();
    });

    $('#opart_devis_refresh_total_quotation').click(function(e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) {
            return false;
        }

        $(this).addClass('disabled');

        opartDevisGetTotalCart();
    });

    $('#opart_devis_select_cart_rules').change(function(e) {
        e.preventDefault();

        $('#opartDevisCartRulesMsgError').hide('fast');

        if ($(this).val() == "-1") {
            return false;
        }

        if ($('#trCartRule_'+$(this).val()).length > 0) {
            $('#opartDevisCartRulesMsgError').html('This rule is already in cart');
            $('#opartDevisCartRulesMsgError').show('fast');

            return false;
        }

        var data = $('#opartDevisForm').serializeArray();

        data.push(
            {name: 'ajax', value: true},
            {name: 'action', value: 'AddCartRule'},
            {name: 'token', value: token},
            {name: 'id_cart_rule', value: $(this).val()}
        );

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            dataType: 'JSON',
            data: $.param(data),
            success: function(data) {
                console.log(data);
                if (!data.id) {
                    $('#opartDevisCartRulesMsgError').html(data);
                    $('#opartDevisCartRulesMsgError').show('fast');
                } else {
                    opartDevisAddRuleToQuotation(
                        data.id,
                        data.name[id_lang_default],
                        data.description,
                        data.code,
                        data.free_shipping,
                        data.reduction_percent,
                        data.reduction_amount,
                        '0',
                        data.gift_product
                    );
                }
            }, error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

        //opartDevisLoadCarrierList();
    });

  
    $('.delete_attachement').on('click', function(e) {
        e.preventDefault();

        opartDevisDeleteUploadedFile(this);
    });
});

function opartDevisAddProductToQuotation(prodId, prodName, prodPrice, qty, idAttribute, specificPrice, yourPrice, customization_datas_json, total, minimal_quantity) {
    opartDevisToggleSubmitBtn(0);

    var id_attribute = (idAttribute == null) ? idAttribute : null;
    var specificPrice = (specificPrice != undefined) ? specificPrice : '';
    var specificQty = (specificQty != undefined) ? specificQty : '';
    var yourPrice = (yourPrice != undefined) ? yourPrice : '';

    randomId = new Date().getTime();

    var customization_datas = $.parseJSON(customization_datas_json);
    var displayedCustomizationDatas = '';
    var qtyInputType = 'text';
    var onChangeCustomizationPrice = '';
    var customPriceClass = ''

    if (customization_datas) {
        for (var i = 0; i < customization_datas.length; i++){
            displayedCustomizationDatas += '<tr class="trAdminCustomizationData"><td colspan="6" class="tdAdminCustomizationDataValue">';

            var customization_datas_array = customization_datas[i]['datas'][1];

            for (var j = 0; j < customization_datas_array.length; j++){
                var addBr = (j > 0) ? '<br />' : '';
                displayedCustomizationDatas += addBr + customization_datas_array[j]['name'] + ' : ' + customization_datas_array[j]['value'];
            }

            displayedCustomizationDatas += '<td class="tdAdminCustomizationDataQty"><input type="text" value="' + customization_datas[i]['quantity'] + '" name="add_customization[' + randomId + '][' + customization_datas[i]['datas']['1']['0']['id_customization'] + '][newQty]" /></td></td><td></td></tr>';
        }

        qtyInputType = 'hidden';
        customPriceClass = 'customprice_' + prodId + '_' + idAttribute;
        onChangeCustomizationPrice = 'onchange="opartDevisAutoChangePrice(this,\'' + customPriceClass + '\')"';
    }

        if (minimal_quantity) {
      minimal_quantity = minimal_quantity;

    }
    else {
        minimal_quantity = qty;
       
    }

    var newTr = '<tr id="trProd_' + randomId + '" style="display:none;">';
    newTr += '<td id="tdIdprod_' + randomId + '">' + prodId + '<input type="hidden" name="whoIs[' + randomId + ']" value="' + prodId + '" id="whoIs_' + randomId + '"/></td>';
    newTr += '<td>' + prodName + '</td>';
    newTr += '<td id="declinaisonsProd_' + randomId + '"></td>';
    newTr += '<td class="prodPrice" id="prodPrice_' + randomId + '">' + prodPrice + '</td>';
    newTr += '<td><input ' + onChangeCustomizationPrice + ' name="specific_price[' + randomId + ']" id="specificPriceInput_' + randomId + '" type="text" value="' + yourPrice + '" class="calcTotalOnChange ' + customPriceClass + '"/></td>';
    newTr += '<td class="prodPrice" id="prodReducedPrice_' + randomId + '">' + specificPrice + '</td>';

    newTr += '<td class="productPrice">';
    newTr += '<input id="inputQty_' + randomId + '" type="number" value="' + minimal_quantity + '" min="'+ minimal_quantity +'" step="1" name="add_prod[' + randomId + ']"  class="opartDevisAddProdInput calcTotalOnChange"/>';
    if (customization_datas) {
        newTr += '<span></span>';
    }
    newTr += '</td>';

    newTr += '<td class="prodPrice" id="prodTotal_' + randomId + '">' + total + '</td>';

    newTr += '<td>';
    if (!customization_datas) {
        newTr += '<a href="#" onclick="opartDevisDeleteProd(\'' + randomId + '\'); return false;"><i class="icon-trash"></i></a>';
    }
    newTr += '</td>';
    newTr += '</tr>';

    newTr += displayedCustomizationDatas;

    $('#opartDevisProdList').append(newTr);
    $('#trProd_'+randomId).show('fast');

    opartDevisLoadProductCombinations(randomId, idAttribute);
    opartBindOnChange();
}

function opartDevisAutoChangePrice(currentInput, inputClass) {
    $('.' + inputClass).each(function() {
        $(this).val(currentInput.value);
    });
}

function opartBindOnChange() {
    $('.calcTotalOnChange').unbind( "change" );
    $('.calcTotalOnChange').change(function() {
        var randomId = $(this).attr('id').substring($(this).attr('id').lastIndexOf('_')+1);

        if ($('#specificPriceInput_' + randomId).val()=='') {
            if ($('#last_selected_attribute_'+randomId).length) {
                var id_attribute = $('#last_selected_attribute_'+randomId).val();
            } else {
                var id_attribute = 0;
            }

            opartDevisDeleteSpecificPrice();

            var current_id_attribute = $('#select_attribute_' + randomId).val()

            $('#last_selected_attribute_' + randomId).val(current_id_attribute);
        }

        opartDevisGetReducedPrices();
    });
}

function opartDevisAddRuleToQuotation(ruleId, name, description, code, free_shipping, reduction_percent, reduction_amount, reduction_type, gift_product) {
    var gift_product_link=(gift_product==0)?'':gift_product;
    var newTr = '<tr id="trCartRule_' + ruleId + '" style="display:none;">';
    newTr += '<td>' + ruleId + '<input type="hidden" name="add_rule[]" value="' + ruleId + '" /></td>';
    newTr += '<td>' + name + '</td>';
    newTr += '<td>' + description + '</td>';
    newTr += '<td>' + code + '</td>';
    newTr += '<td>' + ((free_shipping==1) ? '<i class="icon-check"></i>' : '') + '</td>';
    newTr += '<td>' + reduction_percent + '</td>';
    newTr += '<td>' + reduction_amount + '</td>';
    newTr += '<td>' + reduction_type + '</td>';
    newTr += '<td>' + gift_product_link + '</td>';
    newTr += '<td><a href="#" onclick="opartDevisDeleteRule(\'' + ruleId + '\'); return false;"><i class="icon-trash"></i></a></td>';
    newTr += '</tr>';

    $('#opartDevisCartRuleList').append(newTr);
    $('#trCartRule_'+ruleId).show('fast');
}

function opartDevisLoadProductCombinations(randomId, idAttribute) {
    opartDevisToggleSubmitBtn(0);

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'LoadProductCombinations',
            token: token,
            id_product: $('#whoIs_' + randomId).val()
        },
        success: function(combinations){
            opartDevisPopulateDeclinaisons(
                combinations,
                randomId,
                idAttribute
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisPopulateDeclinaisons(combinations, randomId, idAttribute) {
    if (!combinations) {
        return false;
    }

    //select soit defaut soit selected
    var s = $('<select id="select_attribute_' + randomId + '" name="add_attribute[' + randomId + ']" class="calcTotalOnChange calcTotalOnChangeDec" />');
    for (var key in combinations) {
        var selected = "";
        if (idAttribute != 0 && key == idAttribute) {
            selected = "selected";
        } else if (idAttribute == 0 && combinations['default_on'] == 1) {
            selected = "selected";
        }

        s.append('<option ' + selected + ' value="' + key + '" title="' + combinations[key]['price'] + '">' + combinations[key]['attribute_designation'] + ' [' + combinations[key]['reference'] + '] (' + combinations[key]['price'] + ')</option>');
    }

    $('#declinaisonsProd_' + randomId).append(s);
    //add hidden field last id attribute
    var hidden_field_value = $('#select_attribute_' + randomId).val();
    var hidden_field = '<input type="hidden" value="' + hidden_field_value + '" id="last_selected_attribute_' + randomId + '" />';
    $('#declinaisonsProd_' + randomId).append(hidden_field);

    opartBindOnChange();
    opartDevisToggleSubmitBtn(1);
}

function opartDevisGetTotalCart() {
    opartDevisToggleSubmitBtn(0);

    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'GetTotalCart'},
        {name: 'token', value: token},
        {name: 'id_cart', value: $('#opart_devis_id_cart').val()}
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        success: function(data){
            $('#totalProductHt').html(data.total_products.toFixed(2));
            $('#totalDiscountsHt').html(data.total_discounts_tax_exc.toFixed(2));
            $('#totalShippingHt').html(data.total_shipping_tax_exc.toFixed(2));
            $('#totalTax').html(data.total_tax.toFixed(2));

            if (data.group_tax_method) {
                $('#totalTax').html('<strike>'+(data.total_tax.toFixed(2))+'</strike>');
                $('#totalQuotationWithTax').html((data.total_price-data.total_tax).toFixed(2));
            } else {
                $('#totalTax').html(data.total_tax.toFixed(2));
                $('#totalQuotationWithTax').html(data.total_price.toFixed(2));
            }

            $('#opart_devis_id_cart').val(data.id_cart);

            opartDevisToggleSubmitBtn(1);
            $('#opart_devis_refresh_total_quotation').removeClass('disabled');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
            $('#opart_devis_refresh_total_quotation').removeClass('disabled');
        }
    });
}

function opartDevisAddCustomerToQuotation(customerId, firstname, lastname, email) {
    var newHtml = '(' + customerId + ') ' + lastname + ' ' + firstname + ' - ' + email;
        var customerid = customerId;
    $('#opart_devis_customer_info').html(newHtml);
    $('#opart_devis_customer_id').val(customerId);

    var adresse_delivery = document.getElementById("opart_adress_delivery").value;
    var adresse_invoice = document.getElementById("opart_adress_invoice").value;


     var idcustomer = customerId;
   console.log(idcustomer)
    var address_link = $('#new_address').attr('href');
      id_customer = customerId;
    $('#new_address').attr('href', address_link.replace(/id_customer=[0-9]+/, 'id_customer='+id_customer));

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'GetAddresses',
            token: token,
            id_customer: customerId,
            adresse_delivery : adresse_delivery,
            adresse_invoice : adresse_invoice,
        },
        success: function(data){
            if (data.return) {       
                opartDevisPopulateSelectAddress(data.addresses,data.adresse_delivery,data.adresse_invoice);
            } else {
                console.log(data.error);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function refreshVoucherList(){

    console.log('test')
    $.ajax({
        type:'POST',
        url:ajaxUrl,
        data:{
            ajax:true,
            action:'getAllCartRules',
            token:token
        },
        success: function(data){
            if (data.return) {
                console.log('reussi')
            } else {
                console.log(data.error);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }

    });

}

function returnadresses(customerId){

var customerId = $('#opart_devis_customer_id').val();
console.log(customerId)
 $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'GetAddresses',
            token: token,
            id_customer: customerId
        },
        success: function(data){
            if (data.return) {
                opartDevisPopulateSelectAddress(data.addresses, data.adresse_delivery, data.adresse_invoice);
                console.log(data.addresses)
            } else {
                console.log(data.error);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });


}


function opartDevisPopulateSelectAddress(addresses, adresse_delivery, adresse_invoice) {

    $.each(adresse_delivery, function(index, address_delivery){
       var id_address = address_delivery.id_address; 
        var alias = address_delivery.alias;
        var company = address_delivery.company; 
        var lastname = address_delivery.lastname;
        var firstname = address_delivery.firstname;
        var address1 = address_delivery.address1;
        var address2 = address_delivery.address2;
        var postcode = address_delivery.postcode;
        var city = address_delivery.city;
        var country_name = address_delivery.country_name;

        var deliverySelect = $('#opart_devis_delivery_address_input');
         deliverySelect.html('<option  value="' + id_address + '">'
            + '[' + alias + ']'
            + ' - ' + company
            + ' - ' + lastname + ' ' + firstname
            + ' - ' + address1
            + ' - ' + address2
            + ' - ' + postcode
            + ' - ' + city
            + ' - ' + country_name
            + '</option>');

    });

     $.each(adresse_invoice, function(index, address_invoice){
       var id_address = address_invoice.id_address; 
        var alias = address_invoice.alias;
        var company = address_invoice.company; 
        var lastname = address_invoice.lastname;
        var firstname = address_invoice.firstname;
        var address1 = address_invoice.address1;
        var address2 = address_invoice.address2;
        var postcode = address_invoice.postcode;
        var city = address_invoice.city;
        var country_name = address_invoice.country_name;

        var invoiceSelect = $('#opart_devis_invoice_address_input');
         invoiceSelect.html('<option  value="' + id_address + '">'
            + '[' + alias + ']'
            + ' - ' + company
            + ' - ' + lastname + ' ' + firstname
            + ' - ' + address1
            + ' - ' + address2
            + ' - ' + postcode
            + ' - ' + city
            + ' - ' + country_name
            + '</option>');

    });
    
    var invoiceSelect = $('#opart_devis_invoice_address_input');
    var deliverySelect = $('#opart_devis_delivery_address_input');

   

    $.each(addresses, function(index, address) {
        if ($('#selectedInvoice').val() == address.id_address) {
            var selectedInvoice = 'selected';
        } else {
            var selectedInvoice = '';
        }

        if ($('#selectedDelivery').val() == address.id_address) {
            var selectedDelivery = 'selected';
        } else {
            var selectedDelivery = '';
        }

      

        invoiceSelect.append(
            '<option ' + selectedInvoice + ' value="' + address.id_address + '">'
            + '[' + address.alias + ']'
            + ' - ' + address.company
            + ' - ' + address.lastname + ' ' + address.firstname
            + ' - ' + address.address1
            + ' - ' + address.address2
            + ' - ' + address.postcode
            + ' - ' + address.city
            + ' - ' + address.country_name
            + '</option>'
        );

        deliverySelect.append(
            '<option ' + selectedDelivery + ' value="' + address.id_address + '">'
            + '[' + address.alias + ']'
            + ' - ' + address.company
            + ' - ' + address.lastname + ' ' + address.firstname
            + ' - ' + address.address1
            + ' - ' + address.address2
            + ' - ' + address.postcode
            + ' - ' + address.city
            + ' - ' + address.country_name
            + '</option>'
        );
    });

    //opartDevisLoadCarrierList();
}

function opartDevisDeleteSpecificPrice() {
    var id_cart = $('#opart_devis_id_cart').val();

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'DeleteSpecificPrice',
            token: token,
            id_cart: id_cart,
        },
        success: function(data) {
            if (data) {
                console.log('Specific prices successfully deleted.');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisDeleteProd(idRandom) {
    $('#trProd_'+idRandom).hide("fast", function() {
        if ($('#select_attribute_'+idRandom).length) {
            var id_attribute = $('#select_attribute_'+idRandom).val();
        } else {
            var id_attribute = null;
        }

        opartDevisDeleteSpecificPrice();
        $('#trProd_'+idRandom).remove();
    });
}

function opartDevisDeleteRule(ruleId) {
    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: {
            ajax: true,
            action: 'DeleteCartRule',
            token: token,
            id_cart: function () {
                return $('#opart_devis_id_cart').val();
            },
            id_cart_rule: ruleId
        },
        cache: false,
        success: function(data){
            console.log(data);
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

    $('#trCartRule_'+ruleId).hide("fast", function() {
        $('#trCartRule_'+ruleId).remove();
    });
}

function opartDevisGetReducedPrices() {
    opartDevisToggleSubmitBtn(0);

    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'GetReducedPrices'},
        {name: 'token', value: token},
        {name: 'id_cart', value:
            function () {
                return $('#opart_devis_id_cart').val();
            }
        }
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        dataType: 'JSON',
        data: $.param(data),
        cache: false,
        success: function(data){
            if (data.return) {
                $('#opart_devis_id_cart').val(data.id_cart);

                $.each(data.reduced_prices, function(randomId, reduced_price) {
                    $('#prodPrice_' + randomId).html(reduced_price.real_price);
                    $('#prodReducedPrice_' + randomId).html(reduced_price.reduced_price);
                    $('#specificPriceInput_'+ randomId).val(reduced_price.your_price);
                    $('#prodTotal_'+ randomId).html(reduced_price.total);
                });
            } else {
                console.log(data.error);
            }

            opartDevisToggleSubmitBtn(1);
        }, error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisDeleteUploadedFile(element){
    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        data: {
            ajax: true,
            action: 'DeleteUploadedFile',
            token: token,
            upload_name: $(element).attr('data-name'),
            upload_id: $(element).attr('data-id')
        },
        success: function(data) {
            console.log(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}

function opartDevisToggleSubmitBtn(showMe) {
    if (showMe == 0) {
        $('#opartBtnSubmit').prop('disabled',true);
    } else {
        $('#opartBtnSubmit').prop('disabled',false);
    }
}

function opartDevisLoadCarrierList() {
    var data = $('#opartDevisForm').serializeArray();

    data.push(
        {name: 'ajax', value: true},
        {name: 'action', value: 'LoadCarrierList'},
        {name: 'id_cart', value:
            function () {
                return $('#opart_devis_id_cart').val();
            }
        }
    );

    data.splice(0, 1); // remove 'submitAddOpartDevis' from serialized data to prevent conflict

    $.ajax({
        type: 'POST',
        url: ajaxUrl,
        data: $.param(data),
        cache: false,
        dataType: 'JSON',
        success: function(data){
            $('#opart_devis_id_cart').val(data.id_cart);
            opartDevisPopulateSelectCarrier(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
}



function opartDevisPopulateSelectCarrier(data) {
    var carrierSelect = $('#opart_devis_carrier_input');
    carrierSelect.html('');

    if (data['prefered_order']) {
        // get prefered carrier order
        var order = data['prefered_order'].split(',');

        for (var k=0; k < order.length; k++) {
            if ($('#selected_carrier').val() == order[k]) {
                var selected = 'selected';
            } else {
                 var selected = '';
            }

            carrierSelect.append('<option value="' + order[k] + '" ' + selected + '>' + data[order[k]]['name'] + ' - ' + data[order[k]]['price'] + ' (' + data[order[k]]['taxOrnot'] + ')</option>');
        }
    }
}
