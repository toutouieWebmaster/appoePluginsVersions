<?php use App\Plugin\EventManagement\Auteur;

require('header.php'); ?>
<?php require_once(EVENTMANAGEMENT_PATH . 'process/addEvent.php');
echo getTitle( getAppPageName(), getAppPageSlug() );
showPostResponse( getDataPostResponse() ); ?>
    <div class="container">
        <form action="" method="post" id="addEventForm" enctype="multipart/form-data">
            <?= getTokenField(); ?>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <?php
                    $Auteur = new Auteur();
                    $auteurs = extractFromObjToSimpleArr($Auteur->showByType(), 'id', 'name');

                    echo App\Form::select('Auteur', 'auteurId', $auteurs, !empty($_POST['auteurId']) ? $_POST['auteurId'] : '', true);
                    ?>
                </div>
                <div class="col-12 col-lg-4">
                    <?= App\Form::text('Titre', 'titre', 'text', !empty($_POST['titre']) ? $_POST['titre'] : '', true, 250); ?>
                </div>
                <div class="col-12 col-lg-4">
                    <?= App\Form::selectDuree('Durée', 'duree', true, 0, 2, 55, 5); ?>
                    <small id="passwordHelpBlock" class="form-text text-muted">
                        <?= trans('La durée de l\'évènement n\'est pas modifiable'); ?>.
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text('Pitch', 'pitch', 'text', !empty($_POST['pitch']) ? $_POST['pitch'] : '', false, 255); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::textarea('Participation', 'participation', !empty($_POST['participation']) ? $_POST['participation'] : ''); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::textarea('Description', 'description', !empty($_POST['description']) ? $_POST['description'] : '', 5, true, '', 'ckeditor'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <?= App\Form::radio('Type de Spectacle', 'spectacleType', array_map('trans', SPECTACLES_TYPES)); ?>
                </div>
                <div class="col-12 col-lg-4">
                    <?= App\Form::radio('IN / OFF', 'indoor', array_map('trans', INDOOR_OFF)); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::text('Image', 'image', 'file'); ?>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= App\Form::target('ADDEVENT'); ?>
                    <?= App\Form::submit('Enregistrer', 'ADDEVENTSUBMIT'); ?>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
    <script>
        $(document).ready(function () {
            $('form').submit(function () {
                $('#loader').fadeIn('fast');
            });
        });
    </script>
<?php require('footer.php'); ?>