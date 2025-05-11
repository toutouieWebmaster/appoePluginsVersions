<?php
require('main.php');
if (isUserAuthorized('cms')) {
    $Cms = new \App\Plugin\Cms\Cms();
    $pagesCount = $Cms->showAllPages(true);

    $Menu = new \App\Menu();
    $menuData = $Menu->displayMenuBySlug('cms');

    if (false !== $pagesCount) {
        echo json_encode(
            array(
                'name' => trans($menuData->name),
                'count' => $pagesCount,
                'url' => WEB_PLUGIN_URL . 'cms/page/allPages/'
            ), JSON_UNESCAPED_UNICODE
        );
    }
}