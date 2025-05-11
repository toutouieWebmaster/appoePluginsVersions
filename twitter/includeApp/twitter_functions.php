<?php
require_once(WEB_PLUGIN_PATH . 'twitter/ini.php');

use App\Plugin\Twitter\Manager;

function twitter_is_active()
{

    if (
        defined('TWITTER_CONSUMER_KEY') && !empty(TWITTER_CONSUMER_KEY)
        && defined('TWITTER_CONSUMER_SECRET') && !empty(TWITTER_CONSUMER_SECRET)
        && defined('TWITTER_ACCESS_TOKEN') && !empty(TWITTER_ACCESS_TOKEN)
        && defined('TWITTER_ACCESS_TOKEN_SECRET') && !empty(TWITTER_ACCESS_TOKEN_SECRET)
        && defined('TWITTER_USERNAME') && !empty(TWITTER_USERNAME)) {

        return true;
    }
    return false;
}

function twitter_share_article($message)
{

    $Manager = new Manager();
    return (bool)$Manager->twitter_share_article($message);
}

function twitter_send_message_to_lists(?array $lists, $message)
{

    if (is_array($lists) && twitter_is_active()) {

        $Manager = new Manager();
        $userIds = [];

        foreach ($lists as $key => $listName) {
            $userIds[] = $Manager->twitter_get_ids_list_members($listName);
        }

        foreach (flatten($userIds) as $userId) {
            $Manager->twitter_send_directMessage($userId, $message);
        }

        return true;
    }

    return false;
}