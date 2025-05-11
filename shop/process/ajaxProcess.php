<?php

use App\Plugin\People\People;
use App\Plugin\Shop\Commande;
use App\Plugin\Shop\CommandeDetails;
use App\Plugin\Shop\Product;
use App\Plugin\Shop\ProductMeta;
use App\Plugin\Shop\ShopMedia;
use App\Plugin\Shop\Stock;
use App\Plugin\Traduction\Traduction;

require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        /**
         * Product Ajax
         */
        if (!empty($_POST['deletePRODUCT']) && !empty($_POST['idProductDelete'])) {

            $Product = new Product($_POST['idProductDelete']);

            if ($Product->delete()) {

                //Delete attachement files
                $File = new ShopMedia($_POST['idProductDelete']);
                $allProductFiles = $File->showFiles();
                foreach ($allProductFiles as $productFile) {
                    $File->setId($productFile->id);
                    $File->setName($productFile->name);
                    $File->delete();
                }

                echo 'true';

            }
        }

        if (!empty($_POST['idArchiveProduct'])) {

            $Product = new Product($_POST['idArchiveProduct']);
            $Product->setStatus(0);
            if ($Product->update()) {
                echo 'true';
            }
        }

        if (!empty($_POST['UNPACKPRODUCT']) && !empty($_POST['idUnpackProduct'])) {

            $Product = new Product($_POST['idUnpackProduct']);
            $Product->setStatus(1);
            if ($Product->update()) {
                echo 'true';
            }
        }

        if (!empty($_POST['UNPACKCOMMANDE']) && !empty($_POST['idUnpackCommande'])) {

            $Commande = new Commande($_POST['idUnpackCommande']);
            $Commande->setStatus(1);
            if ($Commande->update()) {
                echo 'true';
            }
        }

        if (!empty($_POST['idSpotlightProduct'])) {

            $Product = new Product($_POST['idSpotlightProduct']);
            $Product->setStatus($Product->getStatus() == 1 ? 2 : 1);
            if ($Product->update()) {
                echo 'true';
            }
        }

        /**
         * Meta Product
         */
        if (isset($_POST['DELETEMETAPRODUCT']) && !empty($_POST['idMetaProduct'])) {
            $ProductMeta = new ProductMeta();
            $ProductMeta->setId($_POST['idMetaProduct']);

            if ($ProductMeta->delete()) {
                echo json_encode(true);
            }
        }

        if (isset($_POST['ADDMETAPRODUCT'])
            && !empty($_POST['productId'])
            && !empty($_POST['metaKey'])
            && !empty($_POST['metaValue'])) {

            $ProductMeta = new ProductMeta($_POST['productId']);
            $ProductMeta->setMetaKey($_POST['metaKey']);
            $ProductMeta->setMetaValue($_POST['metaValue']);

            if (!empty($_POST['UPDATEMETAPRODUCT'])) {

                $ProductMeta->setId($_POST['UPDATEMETAPRODUCT']);
                if (!$ProductMeta->exist(true)) {
                    if ($ProductMeta->update()) {
                        echo json_encode(true);
                    }
                }

            } else {
                if (!$ProductMeta->exist()) {
                    if ($ProductMeta->save()) {
                        echo json_encode(true);
                    }
                }
            }

            //Add translation
            if (isset($_POST['addTradValue'])) {
                if (class_exists('App\Plugin\Traduction\Traduction')) {
                    $Traduction = new Traduction();
                    $Traduction->setLang(APP_LANG);
                    $Traduction->setMetaKey($ArticleMeta->getMetaValue());
                    $Traduction->setMetaValue($ArticleMeta->getMetaValue());
                    $Traduction->save();
                }
            }

        }

        /**
         * Commandes Ajax
         */

        if (!empty($_GET['GETCOMMANDDETAILS'])) {
            if (!empty($_GET['commandeID'])) {

                $Commande = new Commande();
                $Commande->setId(intval($_GET['commandeID']));
                if ($Commande->show()) {

                    //data to return
                    $data = [];

                    //get commande
                    $data['commande'] = array(
                        'date' => displayCompleteDate($Commande->getCreatedAt()),
                        'etat' => $Commande->getOrderState()
                    );

                    //get client details
                    $Client = new People($Commande->getClientId());
                    $data['client'] = array(
                        'entitled' => $Client->getEntitled(),
                        'email' => $Client->getEmail(),
                        'tel' => $Client->getTel(),
                        'address' => $Client->getAddress() . '<br>' . $Client->getZip() . ', ' . $Client->getCity() . '<br>' . getPaysName($Client->getCountry())
                    );

                    //get commande details
                    $CommandeDetails = new CommandeDetails($Commande->getId());
                    $productsData = $CommandeDetails->show();

                    foreach ($productsData as $productData) {

                        $Product = new Product($productData->product_id);
                        $data['product'][$Product->getId()] = array(
                            'name' => $Product->getName(),
                            'price' => $productData->price,
                            'quantity' => $productData->quantity,
                            'poids' => $productData->poids
                        );
                    }

                    echo json_encode($data);
                }
            }
        }


        if (!empty($_POST['commandeChangeDeliveryState'])) {
            if (!empty($_POST['commandeID']) && !empty($_POST['deliveryState'])) {

                $Commande = new Commande();
                $Commande->setId(intval($_POST['commandeID']));
                if ($Commande->show()) {

                    $Commande->setDeliveryState($_POST['deliveryState']);

                    if ($Commande->update()) {

                        echo json_encode(true);

                    } else {

                        echo json_encode(false);
                    }


                }
            }
        }

        /**
         * Check Progress Command
         */
        if (!empty($_POST['checkCommandProgress'])) {
            $Commande = new Commande();
            $data = $Commande->showProgressCommande();

            foreach ($data as $commande) {
                $CommandeDetails = new CommandeDetails($commande->id);
                $CommandeDetails->delete();
                $Commande->setId($commande->id);
                $Commande->delete();
            }
        }

        /**
         * Stock Ajax
         */

        //Delete Stock
        if (isset($_POST['DELETESTOCK']) && !empty($_POST['idDeleteStock'])) {
            $Stock = new Stock($_POST['idDeleteStock']);
            if ($Stock->delete()) {
                echo 'true';
            }
        }

        //Check stock
        if (!empty($_POST['checkStockCookies'])) {

            //Check valides Cookies
            if (!checkValidCookies()) {
                Flash::display();
            } else {
                echo 'true';
            }
        }

        /**
         * Product Media
         */
        if (isset($_POST['deleteImage']) && !empty($_POST['idImage'])) {

            $ProductMedia = new ShopMedia();
            $ProductMedia->setId($_POST['idImage']);
            if ($ProductMedia->show()) {
                if ($ProductMedia->delete()) {
                    echo 'true';
                }
            }
        }
    }
}