<?php require('header.php');
echo getTitle( getAppPageName(), getAppPageSlug() );
$People = new \App\Plugin\People\People();
$People->setStatus(0);
$allPersons = $People->showAll(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Type'); ?></th>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Prénom'); ?></th>
                            <th><?= trans('Age'); ?></th>
                            <th><?= trans('Email'); ?></th>
                            <th><?= trans('Ville'); ?></th>
                            <th><?= trans('Pays'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($allPersons): ?>
                            <?php foreach ($allPersons as $person): ?>
                                <tr data-idperson="<?= $person->id ?>">
                                    <td><?= $person->type ?></td>
                                    <td><?= $person->name ?></td>
                                    <td><?= $person->firstName; ?></td>
                                    <td>
                                        <?php if (!empty($person->birthDate) && $person->birthDate != '0000-00-00'): ?>
                                            <strong><?= age($person->birthDate); ?></strong>
                                            <small>(<?= displayFrDate($person->birthDate); ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $person->email ?></td>
                                    <td><?= $person->city ?></td>
                                    <td><?= $person->country ?></td>
                                    <td>
                                        <a href="<?= getPluginUrl('people/page/update/', $person->id) ?>"
                                           class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                            <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                        </a>
                                        <button type="button" class="btn btn-sm unpackPerson"
                                                title="<?= trans('désarchiver'); ?>"
                                                data-idperson="<?= $person->id ?>">
                                            <span class="btnCheck"> <i class="fas fa-check"></i></span>
                                        </button>
                                        <button type="button" class="btn btn-sm deletePerson"
                                                title="<?= trans('Supprimer'); ?>"
                                                data-idperson="<?= $person->id ?>">
                                            <span class="btnArchive"> <i class="fas fa-times"></i></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('.unpackPerson').on('click', function () {
                let idPerson = $(this).data('idperson');
                if (confirm('<?= trans('Vous allez désarchiver cette personne'); ?>')) {
                    $.post(
                        '<?= PEOPLE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            unpackPerson: 'OK',
                            idUnpackPerson: idPerson
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idperson="' + idPerson + '"]').slideUp();
                            }
                        }
                    );
                }
            });

            $('.deletePerson').on('click', function () {
                let idPerson = $(this).data('idperson');
                if (confirm('<?= trans('Vous allez supprimer définitivement cette personne'); ?>')) {
                    $.post(
                        '<?= PEOPLE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            deletePerson: 'OK',
                            idDeletePerson: idPerson
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idperson="' + idPerson + '"]').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>