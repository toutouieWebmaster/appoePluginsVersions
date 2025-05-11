<?php
require('main.php');
if (isUserAuthorized('itemGlue')) {
    $Article = new \App\Plugin\ItemGlue\Article();
    $articlesCount = $Article->showAll(true);

    $Menu = new \App\Menu();
    $menuData = $Menu->displayMenuBySlug('itemGlue');

    if (false !== $articlesCount) {
        echo json_encode(
            array(
                'name' => trans($menuData->name),
                'count' => $articlesCount,
                'url' => WEB_PLUGIN_URL . 'itemGlue/page/allArticles/'
            ), JSON_UNESCAPED_UNICODE
        );
    }
}