<?php
require('main.php');
$InteractiveMap = new \App\Plugin\InteractiveMap\InteractiveMap();
$pagesCount = $InteractiveMap->showAll(true);

$Menu = new \App\Menu();
$menuData = $Menu->displayMenuBySlug('interactiveMap');

if (false !== $pagesCount) {
    echo json_encode(
        array(
            'name' => trans($menuData->name),
            'count' => $pagesCount,
            'url' => WEB_PLUGIN_URL . 'interactiveMap/page/allInterMaps/'
        ), JSON_UNESCAPED_UNICODE
    );
}