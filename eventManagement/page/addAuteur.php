<?php require('header.php');
require_once(EVENTMANAGEMENT_PATH . 'process/addAuteur.php');
echo getTitle( getAppPageName(), getAppPageSlug() );
showPostResponse( getDataPostResponse() ); ?>
    <div class="container">
        <form action="" method="post" id="addAuteurForm">
            <?= getTokenField(); ?>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text('Provenance (ville)', 'city', 'text', !empty($_POST['city']) ? $_POST['city'] : ''); ?>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::target('ADDAUTHOR'); ?>
                    <?= App\Form::submit('Enregistrer', 'ADDAUTHORSUBMIT'); ?>
                </div>
            </div>
        </form>

        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>