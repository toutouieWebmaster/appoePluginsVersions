<?php

use App\Plugin\Shop\Stock;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (!empty($_POST['UPDATESTOCK'])
        && !empty($_POST['stock_id'])
        && !empty($_POST['product_id'])
        && (!empty($_POST['limit_quantity']) || !empty($_POST['date_limit']))) {

        $limitQuantity = !empty($_POST['limit_quantity']) ? $_POST['limit_quantity'] : NULL;
        $limitDate = !empty($_POST['date_limit']) ? $_POST['date_limit'] : NULL;

        //Update Stock
        $Stock = new Stock();
        $Stock->setProductId($_POST['product_id']);
        $Stock->setLimitQuantity($limitQuantity);
        $Stock->setDateLimit($limitDate);

        if ($Stock->update()) {
            setPostResponse('Le stock a été mis à jour', 'success');
        } else {
            setPostResponse('Un problème est survenu lors de la mise à jour du stock');
        }

    } else {
        setPostResponse('Tous les champs sont obligatoires');
    }
}