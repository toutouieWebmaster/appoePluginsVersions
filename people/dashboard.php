<?php
require('main.php');
$Menu = new \App\Menu();
if ($Menu->checkUserPermission(getUserRoleId(), 'allPeople')) {

    $People = new \App\Plugin\People\People();
    $peopleCount = $People->showAll(true);

    $Menu = new \App\Menu();
    $menuData = $Menu->displayMenuBySlug('people');

    if (false !== $peopleCount) {
        echo json_encode(
            array(
                'name' => trans($menuData->name),
                'count' => $peopleCount,
                'url' => WEB_PLUGIN_URL . 'people/page/allPeople/'
            ), JSON_UNESCAPED_UNICODE
        );
    }
}