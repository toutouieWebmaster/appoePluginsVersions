<?php
require('main.php');
if (isUserAuthorized('shop')) {
    $Commande = new \App\Plugin\Shop\Commande();
    $allCommandes = $Commande->showAll(0, true, true);
    $allCancelCommandes = $Commande->showAll(0, true, true, 1);
    $allLiveCommandes = $Commande->showAll(0, true, true, 2);
    $allConfirmCommandes = $Commande->showAll(0, true, true, 3);

    $Product = new \App\Plugin\Shop\Product();
    $allProducts = $Product->showAll(true);

    $Menu = new \App\Menu();
    $menuCommandData = $Menu->displayMenuBySlug('commandes');
    $menuProductData = $Menu->displayMenuBySlug('products');

    if (false !== $allCommandes) {
        echo json_encode(
            array(
                1 => array(
                    'name' => trans($menuCommandData->name),
                    'count' => $allCommandes,
                    'url' => WEB_PLUGIN_URL . 'shop/page/commandes/',
                    'html' => '<span>Confirmées: ' . $allConfirmCommandes . '</span><span>En cours: ' . $allLiveCommandes . '</span><span>Annulées: ' . $allCancelCommandes . '</span>'
                ),
                2 => array(
                    'name' => trans($menuProductData->name),
                    'count' => $allProducts,
                    'url' => WEB_PLUGIN_URL . 'shop/page/products/'
                )
            ), JSON_UNESCAPED_UNICODE
        );
    }
}