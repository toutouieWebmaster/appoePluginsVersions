<?php

use App\Plugin\Tracker\Tracker;

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');

\App\Hook::add_action('core_front_before_html', function () {
    if (!bot_detected() && !isIpAdministrator()) {
        new Tracker(true);
    }
});