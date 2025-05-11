<?php
require_once('../main.php');

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['shareLink'])) {

            if (!empty($_POST['url'])) {

                $url = WEB_DIR_URL . DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR . $_POST['url'];

                $message = !empty($_POST['message']) ? $_POST['message'] . ' ' . $url : $url;

                if (twitter_share_article($message)) {

                    echo trans('L\'article a été partagé');
                } else {
                    echo trans('Un problème est survenu lors du partage de l\'article');
                }
                exit();
            }
        }

        if (!empty($_POST['sendMessageToLists'])) {

            if (!empty($_POST['lists']) && !empty($_POST['message']) && !empty($_POST['url'])) {

                $url = WEB_DIR_URL . DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR . $_POST['url'];
                $message = $_POST['message'] . ' ' . $url;

                if (twitter_send_message_to_lists($_POST['lists'], $message)) {

                    echo trans('L\'article a été partagé');
                } else {
                    echo 'false';
                }
                exit();
            }
        }
    }
}