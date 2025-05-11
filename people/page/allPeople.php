<?php require('header.php');

use App\Plugin\People\People;

$People = new People();
$allPersons = $People->showAll();
echo getTitle( getAppPageName(), getAppPageSlug() ); ?>
    <div class="row">
        <div class="col-12 mb-2" style="height: 50px;">
            <button class="btn btn-sm btn-outline-dark float-right" id="exportCSV">
                <i class="fas fa-download"></i> <?= trans('Exporter en CSV'); ?></button>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table id="pagesTable"
                       class="sortableTable table table-striped">
                    <thead>
                    <tr>
                        <th><?= trans('Type'); ?></th>
                        <th><?= trans('Nature'); ?></th>
                        <th><?= trans('Nom'); ?></th>
                        <th><?= trans('Age'); ?></th>
                        <th><?= trans('Email'); ?></th>
                        <th><?= trans('CP'); ?></th>
                        <th><?= trans('Ville'); ?></th>
                        <th><?= trans('Pays'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($allPersons):
                        foreach ($allPersons as $person): ?>
                            <tr data-idperson="<?= $person->id ?>">
                                <td><?= $person->type ?></td>
                                <td><?= !empty($person->nature) ? trans(getPeopleNatureNameById($person->nature)) : ''; ?></td>
                                <td><?= $person->entitled ?></td>
                                <td>
                                    <?php if (!empty($person->birthDate) && $person->birthDate != '0000-00-00'): ?>
                                        <strong><?= age($person->birthDate); ?></strong>
                                        <small>(<?= displayFrDate($person->birthDate); ?>)</small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= $person->email ?></td>
                                <td><?= $person->zip ?></td>
                                <td><?= $person->city ?></td>
                                <td><?= getPaysName($person->country); ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('people/page/update/', $person->id) ?>"
                                       class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                        <span class="btnEdit"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm deletePerson"
                                            title="<?= trans('Archiver'); ?>"
                                            data-idperson="<?= $person->id ?>">
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
    <script>
        $(document).ready(function () {
            $('.deletePerson').on('click', function () {
                let idPerson = $(this).data('idperson');
                if (confirm('<?= trans('Vous allez archiver cette personne'); ?>')) {
                    $.post(
                        '<?= PEOPLE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            archivePerson: 'OK',
                            idPersonArchive: idPerson
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idperson="' + idPerson + '"]').slideUp();
                            }
                        }
                    );
                }
            });

            $('#exportCSV').on('click', function () {
                window.open('<?= PEOPLE_URL . 'process/export.php?csv'; ?>');
            });
        });
    </script>
<?php require('footer.php'); ?>