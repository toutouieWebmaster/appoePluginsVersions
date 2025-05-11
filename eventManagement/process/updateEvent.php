<?php

use App\Plugin\EventManagement\Event;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (!empty($_POST['id'])
        && !empty($_POST['titre'])
        && !empty($_POST['auteurId'])
        && !empty($_POST['description'])
    ) {

        $Event = new Event();

        //Update Event
        $Event->feed($_POST);

        if (!empty($_FILES['image']['name'])) {
            $Event->uploadFile($_FILES['image']);
        }

        if ($Event->update()) {

            //Delete post data
            unset($_POST);
            setPostResponse('L\'évènement a été miseà jour', 'success');

        } else {
            setPostResponse('Un problème est survenu lors de la mise à jour de l\'évènement');
        }

    } else {
        setPostResponse('Le titre, la description, la durée et l\'auteur de l\'évènement sont obligatoires');
    }
}