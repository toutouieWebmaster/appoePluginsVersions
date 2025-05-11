<?php if (!empty($_GET['id'])): ?>
    <?php require('header.php');
    $Event = new \App\Plugin\EventManagement\Event();
    $Event->setId($_GET['id']);
    if ($Event->show()) : ?>
        <?php
        $AllGeneralesEvents = $Event->showAll();
        $Auteur = new \App\Plugin\EventManagement\Auteur($Event->getAuteurId());
        $EventsDates = new \App\Plugin\EventManagement\EventsDates();
        $EventsDates->setEventId($Event->getId());
        $allEvents = $EventsDates->showAllEvent();
        echo getTitle($Event->getTitre(), getAppPageSlug()); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a id="updateEventBtn"
                       href="<?= getPluginUrl('eventManagement/page/event/update/', $Event->getId()); ?>"
                       class="btn btn-warning btn-sm">
                        <span class="fa fa-cog"></span> <?= trans('Modifier l\'évènement'); ?>
                    </a>
                    <button id="newDateEventBtn" class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#modalNewEventDate">
                        <span class="fa fa-plus"></span> <?= trans('Nouvelle date'); ?>
                    </button>
                    <select class="custom-select otherEventsSelect otherProjetSelect notPrint float-right"
                            title="<?= trans('Parcourir les évènements'); ?>...">
                        <option selected="selected" disabled><?= trans('Parcourir les évènements'); ?>...</option>
                        <?php foreach ($AllGeneralesEvents as $eventSelect): ?>
                            <?php if ($Event->getId() != $eventSelect->id): ?>
                                <option data-href="<?= getPluginUrl('eventManagement/page/event/', $eventSelect->id); ?>"><?= $eventSelect->titre; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped bg-white">
                            <tr class="table-info-light">
                                <th><?= trans('Auteur'); ?></th>
                                <th><?= trans('Durée'); ?></th>
                                <th><?= trans('Type'); ?></th>
                            </tr>
                            <tr>
                                <td><?= $Auteur->getName(); ?> <em>(<?= $Auteur->getCity(); ?>)</em></td>
                                <td><?= displayDuree($Event->getDuree()); ?></td>
                                <td><?= trans(INDOOR_OFF[$Event->getIndoor()]); ?>
                                    - <?= trans(SPECTACLES_TYPES[$Event->getSpectacleType()]); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-6">
                    <h5 class="strong py-3 border border-right-0 border-top-0 border-left-0 text-uppercase text-vert">
                        <?= trans('Les dates de l\'évènement'); ?>
                    </h5>
                    <div id="eventsDates"></div>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
        <!-- MODAL EVENTS DATES -->
        <div class="modal fade" id="modalNewEventDate" tabindex="-1" role="dialog"
             aria-labelledby="modalTitleNewEventDate"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleNewEventDate"><?= trans('Nouvelle date'); ?></h5>
                    </div>
                    <div class="modal-body">
                        <p class="text-info">
                            <strong>
                                <?= trans('RAPPEL'); ?> :
                            </strong>
                            <?= $Event->getTitre(); ?> <?= trans('a une durée de'); ?> <?= displayDuree($Event->getDuree()); ?>
                        </p>
                        <form method="post" action="" id="NewEventDateForm">
                            <input type="hidden" name="eventId" id="eventId" value="<?= $Event->getId() ?>">
                            <?= App\Form::target('addDateEvent'); ?>
                            <?= getTokenField(); ?>
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <?= App\Form::text('Date début - au format Mois/Jour/Année, par exemple 01/15/2024 pour le 15 Janvier 24', 'dateDebut', 'text', '', true, 10, '', '', 'datepicker'); ?>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <?= App\Form::selectTime('Heure début', 'heureDebut', true, 0, 24, 55, 5); ?>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <?= App\Form::select('Localisation', 'localisation', [
                                        1 => '1a', '2', '10a', '10b', '10c']); ?>
                                </div>
                            </div>
                            <small id="addDateErrorMsg" class="text-danger"></small>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                        <button type="button" id="submitFormAddNewDateEvent"
                                class="btn btn-primary"><?= trans('Ajouter'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {

                var $error = $('form#NewEventDateForm #addDateErrorMsg');

                function getEventData() {
                    $('#eventsDates').html('<i class="fas fa-circle-notch fa-spin"></i> <?= trans('Chargement'); ?>...<span class="sr-only"><?= trans('Chargement'); ?>...</span>');
                    $error.html('');
                    setTimeout(function () {
                        $('#eventsDates').load('<?= EVENTMANAGEMENT_URL . 'script/eventsDates.php'; ?>', {id: <?= $Event->getId() ?>});
                    }, 1000);
                }

                getEventData();

                $('#submitFormAddNewDateEvent').click(function () {
                    $('form#NewEventDateForm').submit();
                });

                $('form#NewEventDateForm').on('submit', function (event) {
                    event.preventDefault();

                    if ($('#dateDebut').val() != '' && $('#heureDebut').val() != '') {
                        $.post(
                            '<?= EVENTMANAGEMENT_URL . 'ajax/eventDates.php'; ?>',
                            $('form#NewEventDateForm input, form#NewEventDateForm select').serialize(),
                            function (data) {
                                if (data === true || data === 'true') {
                                    $error.html('');
                                    $('#eventsDates').load('<?= EVENTMANAGEMENT_URL . 'script/eventsDates.php'; ?>', {id: <?= $Event->getId() ?>});
                                    $('#submitFormAddNewDateEvent').html('<?= trans('Enregistré'); ?>').delay(2000).html('<?= trans('Ajouter'); ?>');
                                } else {
                                    $error.html(data);
                                }
                            }
                        );
                    } else {
                        $error.html('<?= trans('Tous les champs sont obligatoires'); ?>');
                    }
                });

                $('#eventsDates').on('click', '.deleteDateEvent', function () {
                    if (confirm("<?= trans('Vous allez supprimer une date d\'évènement'); ?>")) {
                        var $parentLi = $(this).parent('li');
                        var idDateEvent = $(this).data('iddateevent');
                        $.post(
                            '<?= EVENTMANAGEMENT_URL . 'ajax/eventDates.php'; ?>',
                            {
                                deleteDateEvent: idDateEvent
                            },
                            function (data) {
                                if (data === true || data === 'true') {
                                    $parentLi.slideUp('fast');
                                }
                            }
                        );
                    }
                });

                $('.otherEventsSelect').change(function () {
                    var otherEventslink = $('option:selected', this).data('href');
                    location.assign(otherEventslink);
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet évènement n\'existe pas')); ?>
    <?php endif; ?>
    <?php require('footer.php'); ?>
<?php else: ?>
    <?= trans('Cet évènement n\'existe pas'); ?>
<?php endif; ?>