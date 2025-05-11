<?php
require('header.php');
$unconfirmedRating = getUnconfirmedRates();
$allRating = getAllRates();
echo getTitle( getAppPageName(), getAppPageSlug() ); ?>
    <div class="container">
        <div class="row">
            <div class="col-12" id="allRatingTable"><i class="fas fa-circle-notch fa-spin"></i></div>
        </div>
        <?php if ($unconfirmedRating): ?>
            <hr>
            <h2 class="subTitle"><?= trans('Évaluations à confirmer'); ?></h2>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="ratingTable"
                               class="sortableTable table table-striped">
                            <thead>
                            <tr>
                                <th><?= trans('Type'); ?></th>
                                <th><?= trans('Titre'); ?></th>
                                <th><?= trans('Note'); ?></th>
                                <th><?= trans('Utilisateur'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($unconfirmedRating as $rating): ?>
                                <?php
                                $Obj = getObj($rating->type);
                                $Obj->setId($rating->typeId);
                                $Obj->show();
                                ?>
                                <tr>
                                    <td><?= trans(TYPES_NAMES[$rating->type]); ?></td>
                                    <td><?= $Obj->getName(); ?></td>
                                    <td><strong><?= $rating->score; ?></strong>/5</td>
                                    <td><?= $rating->user ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm confirmRating"
                                                title="<?= trans('Confirmer l\'évaluation'); ?>"
                                                data-idrating="<?= $rating->id; ?>">
                                            <span class="btnCheck"><i class="fas fa-check"></i></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script type="text/javascript">

        $('#allRatingTable').load('<?= RATING_URL; ?>page/getAllRating.php');

        $(document).ready(function () {

            $('#allRatingTable .initRating').on('click', function () {

                let $btn = $(this);
                let type = $btn.data('type');
                let typeId = $btn.data('typeid');

                if (confirm('<?= trans('Vous allez supprimer cette évaluation'); ?>')) {

                    $btn.html('<i class="fas fa-circle-notch fa-spin"></i>').addClass('disabled').attr('disabled', 'disabled');
                    busyApp();

                    $.post(
                        '<?= RATING_URL; ?>process/ajaxProcess.php',
                        {
                            initRating: 1,
                            type: type,
                            typeId: typeId
                        }, function (data) {
                            if (data == 'true' || data === true) {
                                $btn.parent('td').parent('tr').fadeOut(function () {
                                    availableApp();
                                });
                            }
                        }
                    );
                }
            });

            $('.confirmRating').on('click', function () {

                busyApp();

                let $btn = $(this);
                let idRating = $btn.data('idrating');
                $btn.html('<i class="fas fa-circle-notch fa-spin"></i>').addClass('disabled').attr('disabled', 'disabled');

                $.post(
                    '<?= RATING_URL; ?>process/ajaxProcess.php',
                    {
                        confirmRating: 1,
                        idRating: idRating

                    }, function (data) {
                        if (data == 'true' || data === true) {
                            $btn.parent('td').parent('tr').fadeOut(function () {
                                $('#allRatingTable')
                                    .html('<i class="fas fa-circle-notch fa-spin"></i>')
                                    .load('<?= RATING_URL; ?>page/getAllRating.php', function () {
                                        availableApp();
                                    });
                            });
                        }
                    }
                );
            });
        });
    </script>
<?php require('footer.php'); ?>