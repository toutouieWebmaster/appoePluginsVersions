<?php

use App\Plugin\Traduction\Traduction;

require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (isset($_POST['web_traduction']) && !empty($_POST['id']) && !empty($_POST['metaKey']) && isset($_POST['metaValue'])) {

            $Traduction = new Traduction();
            $Traduction->feed($_POST);
            $Traduction->setLang(APP_LANG);

            if ($Traduction->update()) {
                echo 'true';
            }
            exit();
        }

        if (isset($_POST['web_itemGlue_traduction']) && !empty($_POST['metaKey']) && isset($_POST['metaValue'])) {

            $Traduction = new Traduction();
            $Traduction->feed($_POST);
            $Traduction->setLang(APP_LANG);

            if ($Traduction->notExist()) {
                if ($Traduction->save()) {
                    echo 'true';
                }
            } else {
                if ($Traduction->updateByMeta()) {
                    echo 'true';
                }
            }
            exit();
        }

        if (isset($_POST['deleteTrad']) && !empty($_POST['keytrad'])) {
            $Traduction = new Traduction();
            $Traduction->setMetaKey($_POST['keytrad']);

            if ($Traduction->deleteByKey()) {
                echo 'true';
            }
            exit();
        }
    }
}