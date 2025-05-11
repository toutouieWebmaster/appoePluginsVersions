<?php

use App\Plugin\Traduction\Traduction;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDTRADUCTION'])) {
        if (!empty($_POST['metaKeySingle'])) {

            $Traduction = new Traduction();
            $Traduction->setMetaKey($_POST['metaKeySingle']);
            $Traduction->setMetaValue($Traduction->getMetaKey());
            $Traduction->setLang(APP_LANG);
            if ($Traduction->save()) {

                //Delete post data
                unset($_POST);

                setPostResponse('La nouvelle traduction a été enregistrée', 'success');
            } else {
                setPostResponse('Un problème est survenu lors de l\'enregistrement de la traduction');
            }
        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['ADDMULTIPLETRADS'])) {
        if (!empty($_POST['metaValue-fr'])) {

            $Traduction = new Traduction();

            foreach (LANGUAGES as $id => $lang) {
                if (!empty($_POST['metaValue-' . $id])) {
                    $Traduction->setMetaKey($_POST['metaValue-fr']);
                    $Traduction->setMetaValue($_POST['metaValue-' . $id]);
                    $Traduction->setLang($id);
                    if (!$Traduction->saveMultiple()) {

                        $success = false;
                        setPostResponse('Un problème est survenu lors de l\'enregistrement de la traduction');
                        break;
                    }
                }
            }

            //Delete post data
            unset($_POST);

            if (empty($success)) {
                setPostResponse('Les nouvelles traductions ont été enregistrées', 'success');
            }

        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }
}