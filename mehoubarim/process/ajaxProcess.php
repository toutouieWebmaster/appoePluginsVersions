<?php
require_once('../header.php');
if (checkAjaxRequest()) {

    if (getUserIdSession() > 0) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['logoutUser'])) {
            mehoubarim_logoutUser($_POST['logoutUser']);
            echo 'true';
            exit();
        }

        if (!empty($_POST['freeUser'])) {
            mehoubarim_freeUser($_POST['freeUser']);
            echo 'true';
            exit();
        }

        if (!empty($_POST['checkUserStatus'])) {
            $userStatus = mehoubarim_getConnectedStatut();
            if ($userStatus && $userStatus != 'Déconnecté') {
                echo 'true';
            }
            exit();
        }
    }
}