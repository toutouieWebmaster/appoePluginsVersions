<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/header_admin_template.php');
require_once(dirname(__DIR__) . '/ini.php');
echo getTitle(getAppPageName(), getAppPageSlug());
?>
    <div class="row">
        <div class="col-12">
            <div id="handlesContainer" class="position-relative"></div>
        </div>
    </div>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/footer_admin_template.php'); ?>