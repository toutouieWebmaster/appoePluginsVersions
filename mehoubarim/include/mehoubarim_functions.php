<?php

use App\ShinouiKatan;

const MEHOUBARIM_JSON = WEB_PLUGIN_PATH . 'mehoubarim/mehoubarim.json';

const STATUS_CONNECTED_USER = array(
    1 => 'success',
    2 => 'warning',
    3 => 'secondary',
    4 => 'danger'
);

/**
 * check and create necessarily files
 * @param null $file
 * @return bool
 */
function mehoubarim_checkExistingFiles($file = null)
{

    //Create files
    if (!is_null($file)) {
        if (!file_exists($file)) {
            if (false === fopen($file, 'w+')) {
                return mehoubarim_checkExistingFiles($file);
            }
        }

        return true;
    }

    //Connected Users File
    if (!file_exists(MEHOUBARIM_JSON)) {
        if (false === fopen(MEHOUBARIM_JSON, 'w+')) {
            return mehoubarim_checkExistingFiles(MEHOUBARIM_JSON);
        }

        //Edit
        $parsed_json_mehoubarim['users'] = [];

        //Write
        mehoubarim_jsonWrite($parsed_json_mehoubarim);
    }

    return true;
}

/**
 * write on json file
 *
 * @param $data
 * @param $file
 * @param $writingMode
 */
function mehoubarim_jsonWrite($data, $file = MEHOUBARIM_JSON, $writingMode = 'w')
{
    $json_file = fopen($file, $writingMode);
    if (flock($json_file, LOCK_EX)) {
        fwrite($json_file, json_encode($data));
        fflush($json_file);
        flock($json_file, LOCK_UN);
    }
    fclose($json_file);
}

/**
 * read a json file
 *
 * @param $file
 * @return array
 */
function mehoubarim_jsonRead($file = MEHOUBARIM_JSON)
{
    $parsed_json = [];

    if (mehoubarim_checkExistingFiles($file)) {
        $json = file_get_contents($file);
        $parsed_json = json_decode($json, true);
    }

    return $parsed_json;
}

/**
 * edit user statut to actif
 */
function mehoubarim_connecteUser()
{
    $userId = ShinouiKatan::Crypter(getUserIdSession());

    if (!empty($userId)) {

        //Get
        $parsed_json = mehoubarim_jsonRead();

        //Edit
        $parsed_json['users'][$userId]['lastConnect'] = time();
        $parsed_json['users'][$userId]['status'] = 1;
        $parsed_json['users'][$userId]['pageConsulting'] = $_SERVER['REQUEST_URI'];
        $parsed_json['users'][$userId]['order'] = null;

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }
}

/**
 * @return mixed
 */
function mehoubarim_connectedUsers()
{

    //Get
    $parsed_json = mehoubarim_jsonRead();

    return is_array($parsed_json) && array_key_exists('users', $parsed_json) ? $parsed_json['users'] : false;
}

/**
 * @return bool
 */
function mehoubarim_getConnectedStatut()
{

    if (getUserIdSession() > 0) {

        //Get
        $parsed_json = mehoubarim_jsonRead();

        $userId = ShinouiKatan::Crypter(getUserIdSession());
        if (!isArrayEmpty($parsed_json['users']) && array_key_exists($userId, $parsed_json['users'])) {
            return $parsed_json['users'][$userId]['status'];
        }
    }

    return false;
}

/**
 * @param $statut
 */
function mehoubarim_updateConnectedStatus($statut)
{

    //Get
    $parsed_json = mehoubarim_jsonRead();

    $user = ShinouiKatan::Crypter(getUserIdSession());
    if (getUserIdSession() > 0 && isset($parsed_json['users'][$user])) {

        //Edit
        $parsed_json['users'][$user]['status'] = $statut;

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }

}

/**
 * @param $user
 */
function mehoubarim_logoutUser($user)
{
    //Get
    $parsed_json = mehoubarim_jsonRead();

    $user = ShinouiKatan::Crypter($user);
    if (getUserIdSession() > 0 && isset($parsed_json['users'][$user])) {

        //Edit
        $parsed_json['users'][$user]['status'] = 4;
        $parsed_json['users'][$user]['pageConsulting'] = '';
        $parsed_json['users'][$user]['order'] = 'disconnect';

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }
}

/**
 * @param $user
 * @return mixed
 */
function mehoubarim_getUserOrder($user)
{
    $order = null;

    //Get
    $parsed_json = mehoubarim_jsonRead();

    $user = ShinouiKatan::Crypter($user);
    if (getUserIdSession() > 0 && isset($parsed_json['users'][$user])) {

        return $parsed_json['users'][$user]['order'];
    }

    return $order;
}

/**
 * @param $user
 */
function mehoubarim_freeUser($user)
{
    //Get
    $parsed_json = mehoubarim_jsonRead();

    $user = ShinouiKatan::Crypter($user);
    if (getUserIdSession() > 0 && isset($parsed_json['users'][$user])) {

        //Edit
        $parsed_json['users'][$user]['status'] = 4;
        $parsed_json['users'][$user]['pageConsulting'] = '';
        $parsed_json['users'][$user]['order'] = null;

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }
}

/**
 * @return bool
 */
function mehoubarim_pageFreeToChanges()
{
    $userSessionId = ShinouiKatan::Crypter(getUserIdSession());

    if ($userSessionId) {

        //Get
        $parsed_json = mehoubarim_jsonRead();

        //Check
        if ($parsed_json) {
            foreach ($parsed_json['users'] as $user => $param) {

                if ($user != $userSessionId) {
                    if (isset($param['pageConsulting']) && $param['pageConsulting'] == $_SERVER['REQUEST_URI']) {
                        if (preg_match('/update/i', basename($_SERVER['PHP_SELF']))) {
                            if ($param['status'] == 1) {

                                //Edit
                                $parsed_json['users'][$userSessionId]['pageConsulting'] = '';

                                //Write
                                mehoubarim_jsonWrite($parsed_json);

                                return $user;

                            } else {

                                //Edit
                                $parsed_json['users'][$user]['pageConsulting'] = '';

                                //Write
                                mehoubarim_jsonWrite($parsed_json);
                            }
                        }
                    }
                }

            }
        }
        return true;
    }
    return false;
}

/**
 * check & edit user status
 */
function mehoubarim_connectedUserStatus()
{
    $currentTime = time();
    $statutArray = array(
        $currentTime - (60 * 30) => 1,
        $currentTime - (60 * 60 * 4) => 2,
        $currentTime - (60 * 60 * 12) => 3
    );

    //Get
    $parsed_json = mehoubarim_jsonRead();

    //Check
    if ($parsed_json) {
        foreach ($parsed_json['users'] as $user => $connectedUser) {

            $lastConnect = $connectedUser['lastConnect'];
            $statut = $connectedUser['status'];

            if ($statut < 4) {

                foreach ($statutArray as $timeArr => $statusArr) {
                    if ($lastConnect >= $timeArr) {
                        $statut = $statusArr;
                        break;
                    }
                    $statut = 4;
                }

                //Edit
                $parsed_json['users'][$user]['status'] = $statut;
            }
        }
    }

    //Write
    mehoubarim_jsonWrite($parsed_json);
}