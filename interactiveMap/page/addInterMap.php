<?php

use App\Form;

require('header.php');
require_once(INTERACTIVE_MAP_PATH . 'process/postProcess.php');
echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(); ?>
    <div class="container">
        <form action="" method="post" id="addInterMapForm">
            <?= getTokenField(); ?>
            <div class="row">
                <div class="col-12 my-2">
                    <?= Form::text('Titre', 'title', 'text', !empty($_POST['title']) ? $_POST['title'] : '', true); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 my-2">
                    <?= Form::text('Largeur', 'width', 'text', !empty($_POST['width']) ? $_POST['width'] : '', true); ?>
                </div>
                <div class="col-12 col-md-6 my-2">
                    <?= Form::text('Hauteur', 'height', 'text', !empty($_POST['height']) ? $_POST['height'] : '', true); ?>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= Form::target('ADDINTERMAP'); ?>
                    <?= Form::submit('Enregistrer', 'ADDINTERMAPSUBMIT'); ?>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>