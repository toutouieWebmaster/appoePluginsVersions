<?php

use App\Plugin\MessagIn\MessagIn;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDMESSAGE'])
        && !empty($_POST['toUser'])
        && !empty($_POST['text'])
    ) {

        $MessagIn = new MessagIn();

        //Add Projet
        $MessagIn->feed($_POST);
        $MessagIn->setFromUser(getUserIdSession());

        if ($MessagIn->save()) {

            //Delete post data
            unset($_POST);
            setPostResponse('Le message a été envoyé', 'success');

        } else {
            setPostResponse('Un problème est survenu lors de l\'envoi du message');
        }

    } else {
        setPostResponse('Tous les champs sont obligatoires');
    }
}