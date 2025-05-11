<?php

use App\Plugin\MessagIn\MessagIn;

require_once('main.php');
if (getUserIdSession() > 0) {

    $MessagIn = new MessagIn(getUserIdSession());
    $messagesCounter = 0;

    if ($MessagIn->getData()) {
        foreach ($MessagIn->getData() as $message) {
            $messagesCounter++;
        }
    }
    echo $messagesCounter;
    exit();
}