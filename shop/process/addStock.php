<?php

use App\Plugin\Shop\Stock;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (!empty($_POST['ADDSTOCK'])
        && !empty($_POST['product_id'])
        && (!empty($_POST['limit_quantity']) || !empty($_POST['date_limit']))) {

        $Stock = new Stock();

        $limitQuantity = !empty($_POST['limit_quantity']) ? $_POST['limit_quantity'] : NULL;
        $limitDate = !empty($_POST['date_limit']) ? $_POST['date_limit'] : NULL;

        //Add Stock
        $Stock->setProductId($_POST['product_id']);
        $Stock->setLimitQuantity($limitQuantity);
        $Stock->setDateLimit($limitDate);

        if (!$Stock->exist()) {
            if ($Stock->save()) {

                unset($_POST);
                setPostResponse('Le stock a été enregistré', 'success');

            } else {
                setPostResponse('Un problème est survenu lors de l\'enregistrement du stock');
            }
        } else {
            setPostResponse('Ce stock existe déjà', 'warning');
        }
    } else {
        setPostResponse('Tous les champs sont obligatoires');
    }
}