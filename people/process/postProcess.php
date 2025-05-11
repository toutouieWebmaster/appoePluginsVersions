<?php

use App\Plugin\People\People;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDPERSON'])) {

        if (!empty($_POST['name']) && !empty($_POST['type'])) {

            $People = new People();

            //Add person
            $People->feed($_POST);
            if ($People->notExist()) {
                if ($People->save()) {

                    //Delete post data
                    unset($_POST);

                    setPostResponse('La personne a été enregistrée', 'success', ('<a href="' . getPluginUrl('people/page/update/', $People->getId()) . '">' . trans('Voir la personne') . '</a>'));

                } else {
                    setPostResponse('Un problème est survenu lors de l\'enregistrement de la personne');
                }
            } else {
                setPostResponse('Cette personne existe déjà');
            }
        } else {
            setPostResponse('Le type et le nom sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEPERSON'])) {

        if (!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['type'])) {

            $People = new People($_POST['id']);

            //Update Person
            $People->feed($_POST);
            if ($People->notExist(true)) {
                if ($People->update()) {

                    //Delete post data
                    unset($_POST);
                    setPostResponse('La personne a été mise à jour', 'succes');

                } else {
                    setPostResponse('Un problème est survenu lors de la mise à jour de la personne');
                }
            } else {
                setPostResponse('Cette personne existe déjà');
            }
        } else {
            setPostResponse('Le type, le nom et l\'adresse email sont obligatoires');
        }
    }
}