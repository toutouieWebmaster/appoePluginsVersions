<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once(WEB_PLUGIN_PATH . 'mehoubarim/include/mehoubarim_functions.php');

use App\ShinouiKatan;

$order = mehoubarim_getUserOrder(getUserIdSession());
if ($order === 'disconnect') {
    disconnectUser();
}

$mehoubarim_url_parts = explode('/', $_SERVER['PHP_SELF']);

if (in_array('app', $mehoubarim_url_parts) && in_array('page', $mehoubarim_url_parts)) {

    mehoubarim_connecteUser();
    $mehoubarim = mehoubarim_pageFreeToChanges();

    if (true !== $mehoubarim && false !== $mehoubarim) {
        $message = trans('Cette page est en ce moment manipulée par') . ' <strong>' . getUserEntitled(ShinouiKatan::Decrypter($mehoubarim)) . '</strong>';

        if (getOption('PREFERENCE', 'sharingWork') === 'true') {
            define('MEHOUBARIM_MSG', $message);

        } else {
            $mehoubarim_html = '<!doctype html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport"content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><title>APPOE</title><style type="text/css">body,html{margin:0;padding:0;height:100%}body{font-family:Helvetica,Arial,Sans-Serif;background-color:#3eb293;color:#fff;-moz-font-smoothing:antialiased;-webkit-font-smoothing:antialiased}.error-container{text-align:center;height:100%}@media (max-width:480px){.error-container{position:relative;top:50%;height:initial;-webkit-transform:translateY(-50%);-ms-transform:translateY(-50%);transform:translateY(-50%)}}.error-container h1{margin:0;font-size:100px;font-weight:300}@media (min-width:768px){.error-container h1{font-size:220px}}.return{color:rgba(255,255,255,.6);font-weight:400;letter-spacing:-.04em;margin:0}@media (min-width:480px){.error-container h1{position:relative;top:50%;-webkit-transform:translateY(-50%);-ms-transform:translateY(-50%);transform:translateY(-50%)}.return{position:absolute;width:100%;bottom:30px}}.return a{padding-bottom:1px;color:#fff;text-decoration:none;border-bottom:1px solid rgba(255,255,255,.6);-webkit-transition:border-color .1s ease-in;transition:border-color .1s ease-in}.return a:hover{border-bottom-color:#fff}</style></head><body cz-shortcut-listen="true"><div class="error-container"><h1>' . trans('Occupé') . '</h1><p>{message}</p><p class="return"><a href="javascript:history.back()">Revenir en arrière</a></p></div></body></html>';
            $mehoubarim_html = str_replace('{message}', $message, $mehoubarim_html);
            echo $mehoubarim_html;
            exit();
        }
    }
}