<?php

use App\Plugin\Shop\Product;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDPRODUCT'])
        && !empty($_POST['type'])
        && !empty($_POST['name'])
        && !empty($_POST['slug'])
        && !empty($_POST['price'])
        && isset($_POST['poids'])
        && isset($_POST['dimension'])
        && isset($_POST['status'])) {

        $Product = new Product();

        //Add Produit
        $Product->feed($_POST);

        if (!$Product->exist()) {
            if ($Product->save()) {

                //Categories
                $CategoryRelation = new \App\CategoryRelations('SHOP', $Product->getId());

                if (!empty($_POST['categories'])) {
                    foreach ($_POST['categories'] as $chosenCategory) {
                        $CategoryRelation->setCategoryId($chosenCategory);
                        $CategoryRelation->save();
                    }
                }

                //Add Translation
                if (class_exists('App\Plugin\Traduction\Traduction')) {
                    $Traduction = new \App\Plugin\Traduction\Traduction();
                    $Traduction->setLang(APP_LANG);
                    $Traduction->setMetaKey($Product->getName());
                    $Traduction->setMetaValue($Product->getName());
                    $Traduction->save();
                }

                //Delete post data
                unset($_POST);
                setPostResponse('Le produit a été enregistré', 'success', ('<a href="' . getPluginUrl('cms/page/updateProductData/', $Product->getId()) . '">' . trans('Voir les détails du produit') . '</a>'));

            } else {
                setPostResponse('Un problème est survenu lors de l\'enregistrement du produit');
            }
        } else {
            setPostResponse('Ce produit existe déjà', 'warning');
        }
    } else {
        setPostResponse('Tous les champs sont obligatoires');
    }
}