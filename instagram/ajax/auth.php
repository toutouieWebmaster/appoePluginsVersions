<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
inc(WEB_PLUGIN_PATH . '/instagram/includeApp/instagram_functions.php');
if (getUserIdSession()) {
    $medias = instagram_getRecentMedia();
    debug($medias);
}