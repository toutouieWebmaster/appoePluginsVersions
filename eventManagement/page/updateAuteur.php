<?php use App\Plugin\EventManagement\Auteur;

if (!empty($_GET['id'])): ?>
    <?php require('header.php'); ?>
    <?php $Auteur = new Auteur();
    $Auteur->setId($_GET['id']);
    if ($Auteur->show()) :
        require_once(EVENTMANAGEMENT_PATH . 'process/updateAuteur.php');
        echo getTitle( getAppPageName(), getAppPageSlug() );
        showPostResponse(); ?>
        <div class="container">
            <form action="" method="post" id="updateAuteurForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $Auteur->getId() ?>">
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::text('Nom', 'name', 'text', $Auteur->getName(), true); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::text('Provenance', 'city', 'text', $Auteur->getCity()); ?>
                    </div>
                </div>
                <div class="my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::target('UPDATEAUTHOR'); ?>
                        <?= App\Form::submit('Enregistrer', 'UPDATEAUTHORSUBMIT'); ?>
                    </div>
                </div>
                <div class="my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-warning"
                                id="displayForm"><?= trans('Modifier'); ?></button>
                    </div>
                </div>
                <div class="my-4"></div>
            </form>
        </div>
        <script>
            $(document).ready(function () {
                $('input, textarea, select, button[type="submit"], .custom-control-indicator').addClass('displayNoForm').attr('disabled', 'disabled');
                $('input[name="nature"]:not(:checked)').parent('label').css('display', 'none');
                $('#displayForm').click(function () {
                    $(this).remove();
                    $('input, textarea, select, button[type="submit"], .custom-control-indicator').removeClass('displayNoForm').attr('disabled', false);
                    $('input[name="nature"]:not(:checked)').parent('label').css('display', 'inline');
                });
            });
        </script>
        <?php require('footer.php'); ?>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet auteur n\'existe pas')); ?>
    <?php endif; ?>
<?php else: ?>
    <?= trans('Cet auteur n\'existe pas'); ?>
<?php endif; ?>
