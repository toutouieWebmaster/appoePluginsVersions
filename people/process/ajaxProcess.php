<?php
require_once('../main.php');

use App\Plugin\People\People;

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (isset($_POST['archivePerson']) && !empty($_POST['idPersonArchive'])) {
            $People = new People($_POST['idPersonArchive']);
            $People->setStatus(0);
            if ($People->update()) {
                echo 'true';
            }
        }

        if(isset($_POST['unpackPerson']) && !empty($_POST['idUnpackPerson'])){
            $People = new People($_POST['idUnpackPerson']);
            $People->setStatus(1);
            if ($People->update()) {
                echo 'true';
            }
        }

        if(isset($_POST['deletePerson']) && !empty($_POST['idDeletePerson'])){
            $People = new People($_POST['idDeletePerson']);
            if ($People->delete()) {
                echo 'true';
            }
        }
    }
}