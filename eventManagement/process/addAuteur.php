<?php

use App\Plugin\EventManagement\Auteur;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (!empty($_POST['name'])) {

        $Auteur = new Auteur();

        //Add Auteur
        $Auteur->feed($_POST);

        if ($Auteur->notExist()) {
            if ($Auteur->save()) {

                //Delete post data
                unset($_POST);
                setPostResponse('Le nouvel auteur a été enregistré', 'success', ('<a href="' . getPluginUrl('eventManagement/page/auteur/', $Auteur->getId()) . '">' . trans('Voir l\'auteur') . '</a>'));

            } else {
                setPostResponse('Un problème est survenu lors de l\'enregistrement de l\'auteur');
            }
        } else {
            setPostResponse('Cet auteur existe déjà', 'warning');
        }
    } else {
        setPostResponse('Le nom de l\'auteur est obligatoire');
    }
}