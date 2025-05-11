<?php

use App\Plugin\EventManagement\Event;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (!empty($_POST['titre'])
        && !empty($_POST['auteurId'])
        && !empty($_POST['description'])
    ) {

        $Event = new Event();

        //Add Event
        $Event->feed($_POST);

        if ($Event->notExist()) {

            if (!empty($_FILES['image']['name'])) {
                $Event->uploadFile($_FILES['image']);
            }

            if ($Event->save()) {

                //Delete post data
                unset($_POST);
                setPostResponse('Le nouvel évènement a été enregistré', 'success', ('<a href="' . getPluginUrl('eventManagement/page/event/', $Event->getId()) . '">' . trans('Voir l\'évènement') . '</a>'));

            } else {
                setPostResponse('Un problème est survenu lors de l\'enregistrement de l\'évènement');
            }
        } else {
            setPostResponse('Cet évènement existe déjà', 'warning');
        }
    } else {
        setPostResponse('Le titre, la description, la durée et l\'auteur de l\'évènement sont obligatoires');
    }
}