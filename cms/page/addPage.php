<?php

use App\Form;

require('header.php');
require(CMS_PATH . 'process/postProcess.php');

$files = getFilesFromDir(WEB_PUBLIC_PATH . 'html/', ['onlyFiles' => true, 'onlyExtension' => 'php', 'noExtensionDisplaying' => true]);

echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(getDataPostResponse()); ?>
    <form action="" method="post" id="addPageForm">
        <?= getTokenField(); ?>
        <div class="row my-2">
            <div class="col-12 col-lg-6 my-2">
                <?= Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70, 'autofocus data-seo="title"'); ?>
            </div>
            <div class="col-12 col-lg-6 mt-2">
                <?= Form::text('Nom du lien URL (slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true, 70, 'data-seo="slug"'); ?>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <?= Form::textarea('Description', 'description', !empty($_POST['description']) ? $_POST['description'] : '', 4, true, 'maxlength="158" data-seo="description"'); ?>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <?= Form::text('Nom du menu', 'menuName', 'text', !empty($_POST['menuName']) ? $_POST['menuName'] : '', true, 40); ?>
                <div class="mt-3">
                    <?= Form::select('Fichier', 'filename', array_combine($files, $files), !empty($_POST['filename']) ? $_POST['filename'] : '', true); ?>
                </div>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <?= Form::radio('Statut de la page', 'statut', array_map('trans', CMS_PAGE_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <?= Form::select('Type de page', 'type', array_combine(CMS_TYPES, CMS_TYPES), !empty($_POST['type']) ? $_POST['type'] : 'PAGE', true); ?>
            </div>
            <div class="col-12 mb-3 mt-3">
                <?= Form::target('ADDPAGE'); ?>
                <?= Form::submit('Enregistrer', 'addPageSubmit', 'btn-outline-info'); ?>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('input#name').focus();
            }, 100);
            $('input#name').on('keyup', function () {
                $('input#slug').val(convertToSlug($(this).val()));
                $('textarea#description').val($(this).val());
                $('input#menuName').val($(this).val());

                countChars($('input#slug'), 'slug');
                countChars($('textarea#description'), 'description');
            });

            //Stop adding automaticly slug and description from the name of article
            $('input#slug, textarea#description, input#menuName').on('focus', function () {
                $('input#name').unbind('keyup');
            });
        });
    </script>
<?php require('footer.php'); ?>