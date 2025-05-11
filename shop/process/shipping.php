<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    /**
     * Shopping Card Ajax
     */
    if (!empty($_POST['ADDPRODUCTTOCARD'])) {

        //checked if the customer does not have an order in progress
        if (empty($_SESSION['COMMANDE'])) {

            //Add one product to shopping card
            if (!empty($_POST['idProduct'])
                && !empty($_POST['name'])
                && !empty($_POST['quantity'])
                && !empty($_POST['singlePrice'])
                && !empty($_POST['totalPrice'])
                && isset($_POST['totalPoids'])) {

                $idProduct = $_POST['idProduct'];
                $name = $_POST['name'];
                $quantity = $_POST['quantity'];
                $singlePrice = $_POST['singlePrice'];
                $totalPrice = $_POST['totalPrice'];
                $totalPoids = $_POST['totalPoids'];

                if ((shop_getShoppingCardPoids() + $totalPoids) <= 30000) {

                    $Product = new \App\Plugin\Shop\Product();
                    $Product->setId($idProduct);

                    //check if product exist
                    if ($Product->show()) {

                        //check product stock availability
                        if (false !== $Product->getRemainingQuantity()
                            && (
                                is_null($Product->getRemainingQuantity())
                                || $Product->getRemainingQuantity() >= $quantity
                            )
                        ) {

                            //check product limit date
                            if (false !== $Product->getRemainingDate()
                                && (
                                    is_null($Product->getRemainingDate())
                                    || $Product->getRemainingDate() > 0
                                )) {

                                //prepare product cookie
                                $product = base64_encode(serialize(array(
                                    'id' => $idProduct,
                                    'name' => $name,
                                    'quantity' => $quantity,
                                    'singlePrice' => $singlePrice,
                                    'totalPrice' => $totalPrice,
                                    'totalPoids' => $totalPoids
                                )));

                                setcookie("PRODUCT[" . $idProduct . "]", $product, strtotime('+30 days'), WEB_DIR, '', false);

                                echo json_encode(true);

                            } else {
                                echo 'La date de vente de ce produit est dépassée';
                            }
                        } else {
                            echo $Product->getRemainingQuantity() > 0 || is_null($Product->getRemainingQuantity())
                                ? 'Il ne reste plus que ' . $Product->getRemainingQuantity() . ' exemplaires disponibles'
                                : 'Ce produit n\'est pas disponible';
                        }
                    }
                } else {
                    echo 'Le poids maximal autorisé pour une livraison est de 30 kilos !';
                }
            }
        } else {
            echo 'Vous avez une commande en cours. <a href="/commande/">validez ou annulez votre commande</a>';
        }

    }

    //Get count of shipping card
    if (isset($_POST['GETCOUNTSHIPPINGCARD'])) {
        echo shop_getCountShippingCard();
    }

    if (isset($_POST['GETLIMITSTOCK']) && !empty($_POST['idProduct'])) {
        $Product = new \App\Plugin\Shop\Product();
        $Product->setId($_POST['idProduct']);
        if ($Product->show()) {
            return $Product->getRemainingQuantity();
        }
    }

    //Clear product from shipping card
    if (isset($_POST['CLEARSHIPPINGPRODUCT']) && !empty($_POST['idProduct'])) {

        $idProduct = $_POST['idProduct'];
        setcookie("PRODUCT[" . $idProduct . "]", "", time() - 3600, WEB_DIR, '', false);
        unset($_COOKIE['PRODUCT[' . $idProduct . ']']);
        echo json_encode(true);
    }

    //Destroy all products
    if (isset($_POST['CLEARSHIPPINGCARD'])) {

        shop_clearCard(true);
        echo json_encode(true);
    }

    //Destroy commande
    if (isset($_POST['CLEARCOMMANDE'])) {

        if (shop_clearCommande()) {
            echo json_encode(true);
        }
    }

    //Validate commande
    if (isset($_POST['VALIDATECOMMANDE']) && !empty($_POST['idCommande'])) {

        if (shop_validateCommande($_POST['idCommande'])) {
            echo json_encode(true);
        }
    }

    //Cancel commande
    if (isset($_POST['CANCELCOMMANDE']) && !empty($_POST['commandeId'])) {

        if (shop_clearCommande($_POST['commandeId'])) {
            echo json_encode(true);
        }
    }

    /**
     * Update client
     */
    if (isset($_POST['UPDATEPERSON']) && !empty($_POST['id'])) {
        $Client = new \App\Plugin\Shop\Client();
        $Client->setId($_POST['id']);
        if ($Client->show()) {
            $Client->feed($_POST);
            if ($Client->notExist(true)) {
                if ($Client->update()) {
                    echo json_encode(true);
                }
            }
        }
    }

    /**
     * Checkout save client
     */
    if (isset($_POST['ADDPERSON'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['nature'])
            && !empty($_POST['firstName'])
            && !empty($_POST['tel'])
            && !empty($_POST['email'])
            && !empty($_POST['address'])
            && !empty($_POST['zip'])
            && !empty($_POST['city'])
            && !empty($_POST['country'])
            && !empty($_POST['options'])
        ) {

            $Client = new \App\Plugin\Shop\Client();

            //client password
            $hash_password = password_hash($_POST['options'], PASSWORD_DEFAULT);
            $options = array(
                'password' => $hash_password
            );

            //Add person
            $Client->feed($_POST);
            $Client->setOptions(serialize($options));

            if ($Client->notExist()) {
                if ($Client->save()) {

                    //Delete post data
                    unset($_POST);
                    setcookie("CLIENT", $Client->getId(), strtotime('+30 days'), WEB_DIR, '', false);

                    echo json_encode(true);

                } else {
                    echo trans('Un problème est survenu');
                }
            } else {
                echo trans('Cette adresse email est déjà utilisée');
            }
        } else {
            echo trans('Tous les champs sont obligatoires');
        }
    }

    /**
     * Checkout auth client
     */
    if (isset($_POST['AUTHCLIENT'])) {

        if (!empty($_POST['email']) && !empty($_POST['password'])) {

            $Client = new \App\Plugin\Shop\Client();
            $Client->setEmail($_POST['email']);
            $Client->setOptions($_POST['password']);

            if ($Client->authPeople()) {

                //Delete post data
                unset($_POST);
                setcookie("CLIENT", $Client->getId(), strtotime('+30 days'), WEB_DIR, '', false);

                echo json_encode(true);

            } else {
                echo trans('Impossible de vous identifier');
            }
        } else {
            echo trans('Tous les champs sont obligatoires');
        }
    }

}