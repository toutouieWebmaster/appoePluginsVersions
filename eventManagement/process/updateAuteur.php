<?php

use App\Plugin\EventManagement\Auteur;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (!empty($_POST['id']) && !empty($_POST['name'])) {

        $Auteur = new Auteur();

        //Update auteur
        $Auteur->feed($_POST);

        if ($Auteur->notExist()) {
            if ($Auteur->update()) {

                //Delete post data
                unset($_POST);
                setPostResponse('L\'auteur a été mis à jour', 'success');

            } else {

                setPostResponse('Un problème est survenu lors de la mise à jour de l\'auteur');
            }
        } else {

            setPostResponse('Le nom de l\'auteur est déjà utilisé');
        }

    } else {

        setPostResponse('Le nom de l\'auteur est obligatoire');
    }
}