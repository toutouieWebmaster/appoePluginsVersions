<?php require('header.php');
echo getTitle( getAppPageName(), getAppPageSlug() ); ?>
    <div class="container-fluid">
        <?php
        $Auteur = new \App\Plugin\EventManagement\Auteur();
        $auteurs = $Auteur->showByType();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="clientTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Provenance'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($auteurs as $auteur): ?>
                            <tr>
                                <td><?= $auteur->name ?></td>
                                <td><?= $auteur->city ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('eventManagement/page/auteur/', $auteur->id); ?>"
                                       class="btn btn-sm"
                                       title="<?= trans('Modifier'); ?>">
                                        <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm deleteAuteur"
                                            title="<?= trans('Archiver'); ?>"
                                            data-idauteur="<?= $auteur->id ?>">
                                        <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
    </div>
    <script>
        $(document).ready(function () {
            $('.deleteAuteur').click(function () {
                if (confirm('<?= trans('Vous allez archiver cet auteur'); ?>')) {
                    let $btn = $(this);
                    let idAuteur = $btn.data('idauteur');
                    $.post(
                        '<?= EVENTMANAGEMENT_URL . 'ajax/auteurs.php'; ?>',
                        {
                            idDeleteAuteur: idAuteur
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