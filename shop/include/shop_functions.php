<?php

use App\CategoryRelations;
use App\Plugin\Shop\Client;
use App\Plugin\Shop\Commande;
use App\Plugin\Shop\CommandeDetails;
use App\Plugin\Shop\Product;
use App\Plugin\Shop\ProductContent;
use App\Plugin\Shop\ProductMeta;
use App\Plugin\Shop\ShopMedia;

/**
 * @param $amount
 * @return string
 */
function shop_financial($amount)
{
    return is_float($amount) ? number_format($amount, 2, '.', ' ') : $amount;
}

/**
 * @param string $slug
 * @param string $lang
 * @return Product|false
 */
function shop_getProductDetailsFromSlug(string $slug, string $lang = LANG): Product|false
{

    $Product = new Product();
    $Product->setSlug($slug);

    if ($Product->showBySlug()) {

        $ProductContent = new ProductContent($Product->getId(), $lang);
        $ProductMeta = new ProductMeta($Product->getId());
        $ProductMedia = new ShopMedia($Product->getId());
        $CategoryRelation = new CategoryRelations('SHOP', $Product->getId());

        $Product->setContent($ProductContent);
        $Product->setMeta(extractFromObjToSimpleArr($ProductMeta->getData(), 'meta_key', 'meta_value'));

        $Product->setMedia($ProductMedia->showFiles());
        $Product->setCategories(extractFromObjToSimpleArr($CategoryRelation->getData(), 'categoryId', 'name'));

        return $Product;
    }

    return false;
}

/**
 * @param null $idProduct
 * @param $lang
 * @return Product|array
 */
function shop_getProductDetails($idProduct = null, $lang = LANG)
{
    //clear incomplet commandes
    $Commande = new Commande();
    $Commande->clearIncompletCommandes();

    //get necessarily product infos
    $Product = new Product();
    $ProductContent = new ProductContent(null, $lang);
    $ProductMeta = new ProductMeta();
    $ProductMedia = new ShopMedia();

    if (!is_null($idProduct)) {
        $Product->setId($idProduct);
        if ($Product->show()) {

            $ProductContent->setIdProduct($Product->getId());
            $ProductContent->show();
            $Product->content = $ProductContent;

            $ProductMeta->setProductId($Product->getId());
            $ProductMeta->show();
            $Product->meta = extractFromObjToSimpleArr($ProductMeta->getData(), 'meta_key', 'meta_value');

            $ProductMedia->setTypeId($idProduct);
            $Product->media = $ProductMedia->showFiles();
        }

        return $Product;
    }

    $data = [];
    $allProducts = $Product->showAll();

    foreach ($allProducts as $product) {

        $productData = shop_getProductDetails($product->id);
        $data[] = $productData;
    }

    return $data;
}

/**
 * @return int
 */
function shop_getShoppingCardPoids()
{
    $poidsTotal = 0;
    if (!empty($_COOKIE['PRODUCT'])) {
        foreach ($_COOKIE['PRODUCT'] as $idProduct => $dataProduct) {
            $product = @unserialize(base64_decode($dataProduct));
            $poidsTotal += $product['totalPoids'];
        }
    }
    return $poidsTotal;
}

/**
 * @param bool $saveCommande
 * @return array
 */
function shop_getShoppingCard($saveCommande = false)
{
    $poidsTotal = 0;
    $totalProductsPrice = 0;
    $allDataProducts = [];
    $Product = new Product();

    //check if products is selected
    if (!empty($_COOKIE['PRODUCT'])) {
        foreach ($_COOKIE['PRODUCT'] as $idProduct => $dataProduct) {

            //check product data
            $Product->setId($idProduct);
            if ($Product->show()) {

                //check product limit date
                if (false !== $Product->getRemainingDate()
                    && (is_null($Product->getRemainingDate()) || $Product->getRemainingDate() > 0)
                ) {

                    //extract card infos into array
                    $product = @unserialize(base64_decode($dataProduct));

                    //check product stock availability
                    if (
                        (
                            false !== $Product->getRemainingQuantity()
                            && (is_null($Product->getRemainingQuantity()) || $Product->getRemainingQuantity() >= $product['quantity'])
                        )
                        ||
                        (
                            !empty($_SESSION['COMMANDE']) && shop_getCommandeDetails($_SESSION['COMMANDE'], $Product->getId())
                        )
                    ) {

                        //check card data
                        if (
                            shop_financial($Product->getPrice()) == $product['singlePrice']
                            && $product['quantity'] * $product['singlePrice'] == $product['totalPrice']
                            && $product['quantity'] * $Product->getPoids() == $product['totalPoids']
                        ) {

                            //get Product details
                            $product['product'] = shop_getProductDetails($product['id']);

                            //added price and weight for total
                            $poidsTotal += $product['totalPoids'];
                            $totalProductsPrice += $product['totalPrice'];

                            //put results into array
                            $allDataProducts[] = $product;
                        }
                    }
                }
            }
        }

        //Client Infos
        if (shop_checkExistClient()) {
            $Client = shop_getClientInfo();

            $transportCosts = $Client ? calculeTransport_laposte($Client->getCountry(), $poidsTotal) : 0;

            //check the delivery possibility
            if (is_numeric($transportCosts)) {

                $totalTransport = shop_financial($transportCosts);

                $allDataProducts['total'] = array(
                    'totalPrice' => $totalProductsPrice,
                    'totalPoids' => $poidsTotal,
                    'totalTransport' => $totalTransport
                );

                //add client to user Interface
                $allDataProducts['client'] = $Client;

                $totalShopping = (float) $totalProductsPrice + (float) $totalTransport;

                shop_setTotalShopping($totalShopping);

                $Commande = new Commande();
                if (empty($_SESSION['COMMANDE'])) {

                    //save commande
                    if ($saveCommande) {
                        $Commande->setClientId($Client->getId());
                        $Commande->setTotal(shop_getTotalShopping(true));
                        $Commande->setTotalTransport($totalTransport);
                        if ($Commande->save()) {

                            //add command to user interface
                            $_SESSION['COMMANDE'] = $Commande->getId();
                            $allDataProducts['commande'] = $Commande;

                            //save command details
                            saveCommandDetails($allDataProducts, $Commande);
                        }
                    }
                } else {

                    //show command
                    $Commande->setId($_SESSION['COMMANDE']);
                    if ($Commande->show() && $Commande->getOrderState() > 1) {

                        //add command to user interface
                        $allDataProducts['commande'] = $Commande;
                    } else {

                        shop_clearCard(true);
                        $allDataProducts['commande'] = null;
                        \App\Flash::setMsg('Votre commande à été annulé.');
                    }
                }
            } else {

                \App\Flash::setMsg($transportCosts);
            }
        }
    }

    return $allDataProducts;
}

/**
 * @param $data
 * @param $Commande
 */
function saveCommandDetails($data, $Commande)
{
    if ($data && $Commande) {

        $CommandDetails = new CommandeDetails();

        foreach ($data as $id => $product) {
            if (is_numeric($id)) {
                $CommandDetails->setCommandeId($Commande->getId());
                $CommandDetails->setProductId($product['id']);
                $CommandDetails->setPrice($product['totalPrice']);
                $CommandDetails->setQuantity($product['quantity']);
                $CommandDetails->setPoids($product['totalPoids']);
                $CommandDetails->save();
            }
        }
    }
}

/**
 * @param $data
 * @param string $noRemainingTxt
 * @return null|string
 */
function getRemainingProduct($data, $noRemainingTxt = '')
{

    if (false === $data || $data === 0) {
        return $noRemainingTxt;
    } elseif ($data > 0) {
        return $data;
    }

    return null;
}

/**
 * @param $total
 */
function shop_setTotalShopping($total)
{
    $_SESSION['totalPrice'] = $total;
}

/**
 * @param bool $forDB
 * @return bool|string
 */
function shop_getTotalShopping($forDB = false)
{
    return !empty($_SESSION['totalPrice']) ? number_format($_SESSION['totalPrice'], 2, '.', (!$forDB ? ' ' : '')) : false;
}

/**
 * @return bool
 */
function shop_checkExistClient()
{
    return !empty($_COOKIE['CLIENT']);
}

/**
 * @return Client|bool
 */
function shop_getClientInfo()
{
    if (!empty($_COOKIE['CLIENT'])) {
        $Client = new Client();
        $Client->setId($_COOKIE['CLIENT']);
        if ($Client->show()) {
            return $Client;
        }
    }

    return false;
}

/**
 * @param $idCommande
 * @return bool
 */
function shop_validateCommande($idCommande)
{
    if ($Commande = new Commande($idCommande)) {

        //if commande is already paid, the commande will be archived
        if ($Commande->getOrderState() == 2) {
            $Commande->setOrderState(3);
            if ($Commande->update()) {

                //Clear sessions
                unset($_SESSION['COMMANDE']);
                unset($_SESSION['totalPrice']);

                //Clear Shipping Card
                shop_clearCard();

                return true;
            }
        }
    }

    return false;
}

/**
 * @param $idCommande
 * @param bool $productId
 * @return Commande|bool
 */
function shop_getCommandeDetails($idCommande, $productId = false)
{
    if ($Commande = new Commande($idCommande)) {

        if (!$productId) {
            return $Commande;
        }

        $CommandeDetails = new CommandeDetails($Commande->getId());
        if ($CommandeDetails->show($productId)) {
            return true;
        }
    }

    return false;
}

/**
 * @param null $idCommande
 * @return bool
 */
function shop_clearCommande($idCommande = null)
{
    $Commande = new Commande();

    if (!is_null($idCommande)) {

        $Commande->setId($idCommande);
        if ($Commande->show()) {

            //if commande is already paid, the commande will be archived
            if ($Commande->getOrderState() == 3) {
                $Commande->setStatus(0);
                if ($Commande->update()) {
                    return true;
                }
            } else {

                //the commande will be deleted
                if ($Commande->delete()) {
                    return true;
                }
            }
        }

    } elseif (is_null($idCommande) && !empty($_SESSION['COMMANDE'])) {

        $Commande->setId($_SESSION['COMMANDE']);
        if ($Commande->delete()) {
            unset($_SESSION['COMMANDE']);
            unset($_SESSION['totalPrice']);

            return true;
        }
    }

    return false;
}

/**
 * @param bool $clearCommand
 * @return bool
 */
function shop_clearCard($clearCommand = false)
{
    //clear all products
    foreach ($_COOKIE['PRODUCT'] as $idProduct => $dataProduct) {
        setcookie("PRODUCT[" . $idProduct . "]", "", time() - 3600, WEB_DIR, '', false);
        unset($_COOKIE['PRODUCT[' . $idProduct . ']']);
    }
    unset($_COOKIE['PRODUCT']);

    //clear commande
    if ($clearCommand) {
        shop_clearCommande();
    }

    return true;
}

/**
 * @return int
 */
function shop_getCountShippingCard()
{
    return !empty($_COOKIE['PRODUCT']) ? count($_COOKIE['PRODUCT']) : 0;
}

/**
 * @return bool
 */
// FAIT APPEL A DES PROPRIETES INEXISTANTES ?
//function shop_checkValidProductsCookies()
//{
//    if (isset($_COOKIE['PRODUCT'])) {
//
//        $totalPriceProducts = 0;
//        $totalDimension = 0;
//        $totalPoids = 0;
//
//        foreach ($_COOKIE['product'] as $idProduct => $dataProduct) {
//
//            $dataProduct = unserialize(base64_decode($dataProduct));
//
//            $Product = new \App\Plugin\Shop\Product($dataProduct['id']);
//
//            $totalDimension += $dataProduct['quantity'] * $Product->getDimension();
//            $totalPoids += $dataProduct['quantity'] * $Product->getPoids();
//            $totalPriceProducts += $dataProduct['quantity'] * $Product->getPrice();
//
//            if (false === $Product->getLimitQuantity()) {
//
//                Flash::setMsg($Product->getName() . ' n\'est plus disponible');
//
//            } elseif (!is_null($Product->getLimitQuantity()) && $Product->getLimitQuantity() < $dataProduct['quantity']) {
//
//                Flash::setMsg('Il ne reste plus que ' . $Product->getLimitQuantity() . ' exemplaire(s) de ' . $Product->getName());
//
//            } elseif (false === $Product->getLimitDate()) {
//
//                Flash::setMsg($Product->getName() . ' a expiré');
//
//            } elseif ($dataProduct['totalPrice'] != ((float)$Product->getPrice() * $dataProduct['quantity'])) {
//
//                setcookie("PRODUCT[" . $idProduct . "]", "", time() - 3600, '/', WEB_DIR, false);
//                Flash::setMsg('Les données de ' . $Product->getName() . ' ont été modifiées');
//            }
//
//            if (!is_null(Flash::getMsg())) {
//                return false;
//            }
//        }
//    }
//    return true;
//}

/**
 * @param $id
 * @return null
 */
function getCategoriesByProduct($id)
{
    //get product
    $Product = new Product($id);

    //get all categories in relation with article
    $CategoryRelation = new CategoryRelations('SHOP', $Product->getId());
    return $CategoryRelation->getData();
}