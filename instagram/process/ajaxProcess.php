<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
inc(WEB_PLUGIN_PATH . '/instagram/includeApp/instagram_functions.php');
if (checkAjaxRequest() && getUserIdSession()) {

    $_POST = cleanRequest($_POST);

    if (isset($_POST['updateTimeline'])) {

        if (defined('INSTAGRAM_TOKEN') && !empty(INSTAGRAM_TOKEN)) {

            $content = json_decode(instagram_getRecentMedia(), true);
            if (is_array($content)) {
                $content['lastUpdate'] = date('Y-m-d H:i:s');

                foreach ($content['data'] as &$item) {
                    $urlMedia = basename($item['media_url']);
                    $urlMedia = strpos($urlMedia, '?') ? strstr($urlMedia, '?', true) : $urlMedia;
                    if (file_exists(FILE_DIR_PATH . $urlMedia)) {
                        unlink(FILE_DIR_PATH . $urlMedia);
                    }
                    downloadFile(FILE_DIR_PATH . $urlMedia, $item['media_url']);
                    $item['media_url'] = WEB_DIR_INCLUDE . $urlMedia;
                    $item['thumbnail_url'] = getThumb($urlMedia, 200);
                }

                echo putJsonContent(WEB_PLUGIN_PATH . 'instagram/timeline.json', $content) ? json_encode(true) : json_encode(false);
                exit();
            }
        }

        echo json_encode(false);
        exit();
    }
}
echo json_encode(false);
exit();