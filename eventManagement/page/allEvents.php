<?php require('header.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <?php
        $Event = new \App\Plugin\EventManagement\Event();
        $evenements = $Event->showAll();
        $Auteur = new \App\Plugin\EventManagement\Auteur();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="projetsTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Auteur'); ?></th>
                            <th><?= trans('Titre'); ?></th>
                            <th><?= trans('Durée'); ?></th>
                            <th><?= trans('Type de spectacle'); ?></th>
                            <th><?= trans('IN / OFF'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($evenements):
                            foreach ($evenements as $evenement):
                                $Auteur->setId($evenement->auteurId);
                                $Auteur->show(); ?>
                                <tr>
                                    <td><?= $Auteur->getName(); ?></td>
                                    <td><?= $evenement->titre; ?></td>
                                    <td><?= displayDuree($evenement->duree) ?></td>
                                    <td><?= trans(SPECTACLES_TYPES[$evenement->spectacleType]); ?></td>
                                    <td><?= trans(INDOOR_OFF[$evenement->indoor]); ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('eventManagement/page/event/', $evenement->id) ?>"
                                           class="btn btn-sm"
                                           title="<?= trans('Consulter'); ?>">
                                            <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                        </a>
                                        <a href="<?= getPluginUrl('eventManagement/page/event/update/', $evenement->id) ?>"
                                           class="btn btn-sm"
                                           title="<?= trans('Modifier'); ?>">
                                            <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                        </a>
                                        <button type="button" class="btn btn-sm deleteEvent"
                                                title="<?= trans('Archiver'); ?>"
                                                data-idevent="<?= $evenement->id ?>">
                                            <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
    </div>
    <script>
        $(document).ready(function () {

            $('.deleteEvent').click(function () {

                if (confirm('<?= trans('Vous allez archiver cet évènement'); ?>')) {
                    let $btn = $(this);
                    let idevent = $btn.data('idevent');

                    $.post(
                        '<?= EVENTMANAGEMENT_URL . 'ajax/event.php'; ?>',
                        {
                            idDeleteEvent: idevent
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $btn.parent('td').parent('tr').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>