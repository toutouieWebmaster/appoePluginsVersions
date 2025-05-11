function getEtatChoise(idCommande) {
    let $modalInfo = $('#modalInfo');
    let $modalInfoHeader = $('#modalInfo h5#modalTitle');
    let $modalInfoBody = $('#modalInfo div#modalBody');

    $modalInfoHeader.html('<h5>État de la commande</h5>');
    $modalInfoBody.html('<button class="changeCommandeEtat btn btn-warning mx-3 my-2" data-commandeid="' + idCommande + '" data-deliverystate="1">En préparation</button>' +
        '<button class="changeCommandeEtat btn btn-success mx-3 my-2" data-commandeid="' + idCommande + '" data-deliverystate="2">Envoyé</button>');

    $modalInfo.modal('show');
}

function shop_financial(x) {
    return Number.parseFloat(x).toFixed(2);
}

function changeCommandeDeliveryState(id_commande, deliveryState) {
    $.post(
        '/app/plugin/shop/process/ajaxProcess.php',
        {
            commandeChangeDeliveryState: 'OK',
            commandeID: id_commande,
            deliveryState: deliveryState
        },
        function (data) {
            if (data) {
                const deliveryStateData = {
                    1: 'En préparation',
                    2: 'Envoyé'
                };

                $('tr[data-commandeid="' + id_commande + '"]').find('.commandeEtat').text(deliveryStateData[deliveryState]);
            }
        });
}

function getCommandeDetails(id_commande, transport) {

    let $modalInfo = $('#modalInfo');
    let $modalInfoHeader = $('#modalInfo h5#modalTitle');
    let $modalInfoBody = $('#modalInfo div#modalBody');

    $.getJSON(
        '/app/plugin/shop/process/ajaxProcess.php',
        {
            GETCOMMANDDETAILS: 'OK',
            commandeID: id_commande
        },
        function (data) {
            if (data != 'undefined') {

                let totalPriceHt = 0;
                let totalPoids = 0;

                $modalInfoHeader.html('<h5>Commande du ' + data.commande.date + '</h5>');
                $modalInfoBody.html('<div class="bg-info text-white"><strong>Nom :</strong> ' + data.client.entitled + '<br><strong>Email :</strong> ' + data.client.email + '<br><strong>Téléphone :</strong> ' + data.client.tel + '<br><strong>Adresse :</strong> ' + data.client.address + '</div><hr>');

                $.each(data.product, function (key, val) {

                    totalPriceHt += parseFloat(val.price);
                    totalPoids += parseFloat(val.poids);

                    $modalInfoBody.append('<div class="bg-primary text-white"><strong>Produit :</strong> ' + val.name + '<br><strong>Quantité :</strong> ' + val.quantity + '<br><strong>Poids : </strong>' + val.poids + 'g<br><strong>Prix :</strong> ' + val.price + '€</div><div class="dividerPersoLittle"></div>');

                });

                let totalPrice = parseFloat(totalPriceHt) + parseFloat(transport);

                $modalInfoBody.append('<hr><p><strong>Total TTC :</strong> ' + parseFloat(totalPriceHt).toFixed(2) + '€<br>Total Poids : ' + totalPoids + 'g<br><strong>Transport :</strong> ' + transport + '€<br><strong>Total : ' + totalPrice.toFixed(2) + '€</strong></p>');

                $modalInfo.modal('show');
            }
        }
    );
}

//Get stock limit for product
function getStockLimit(idProduct) {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            GETLIMITSTOCK: 'OK',
            idProduct: idProduct
        });
}

//Add product to shopping card
function addToShoppingCard(Product) {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            ADDPRODUCTTOCARD: 'OK',
            idProduct: Product.idProduct,
            name: Product.name,
            quantity: Product.quantity,
            singlePrice: Product.price,
            totalPoids: Product.quantity * Product.poids,
            totalPrice: shop_financial(Product.quantity * Product.price)
        });
}

//Get count of shipping card
function getCountShippingCard() {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            GETCOUNTSHIPPINGCARD: 'OK'
        });
}

//Clear product from shopping card
function clearShoppingProduct(idProduct) {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            CLEARSHIPPINGPRODUCT: 'DESTROY',
            idProduct: idProduct
        });
}

//Clear shopping card
function clearShoppingCard() {

    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            CLEARSHIPPINGCARD: 'DESTROY_ALL'
        });
}

//validate order
function validateCommande(idCommande) {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            VALIDATECOMMANDE: 'OK',
            idCommande: idCommande
        });
}

//Clear Command
function clearCommande() {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            CLEARCOMMANDE: 'DESTROY'
        });
}

//Cancel Command
function cancelCommande(idCommande) {
    return $.post(
        '/app/plugin/shop/process/shipping.php',
        {
            CANCELCOMMANDE: 'OK',
            commandeId: idCommande
        });
}

//checkout save client
function saveClientInfos(data) {
    return $.post('/app/plugin/shop/process/shipping.php', data);
}

//checkout auth client
function authClient(data) {
    return $.post('/app/plugin/shop/process/shipping.php', data);
}

function addMetaProduct(data) {
    return $.post('/app/plugin/shop/process/ajaxProcess.php', data)
}

function deleteMetaProduct(idMetaProduct) {
    return $.post(
        '/app/plugin/shop/process/ajaxProcess.php',
        {
            DELETEMETAPRODUCT: 'OK',
            idMetaProduct: idMetaProduct
        }
    );
}