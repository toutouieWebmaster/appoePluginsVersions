<?php

use App\Plugin\InteractiveMap\InteractiveMap;

require('header.php');
$InteractiveMap = new InteractiveMap();
$allCartes = extractFromObjArr($InteractiveMap->showAll(), 'id');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="pagesTable"
                       class="sortableTable table table-striped">
                    <thead>
                    <tr>
                        <th><?= trans('Titre'); ?></th>
                        <th><?= trans('Largeur'); ?></th>
                        <th><?= trans('Hauteur'); ?></th>
                        <th><?= trans('Modifié le'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($allCartes): ?>
                        <?php foreach ($allCartes as $carte): ?>
                            <tr data-idcarte="<?= $carte->id ?>">
                                <td><?= $carte->title ?></td>
                                <td><?= $carte->width; ?></td>
                                <td><?= $carte->height ?></td>
                                <td><?= displayTimeStamp($carte->updated_at) ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMapContent/', $carte->id) ?>"
                                       class="btn btn-sm"
                                       title="<?= trans('Consulter'); ?>">
                                        <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMap/', $carte->id) ?>"
                                       class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                        <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                    </a>
                                    <?php if (isTechnicien(getUserRoleId())): ?>
                                        <button type="button" class="btn btn-sm deleteMap"
                                                title="<?= trans('Archiver'); ?>" data-idcarte="<?= $carte->id ?>">
                                            <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                        </button>
                                    <?php endif; ?>
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
<?php if (isTechnicien(getUserRoleId())): ?>
    <script>
        $(document).ready(function () {
            $('.deleteMap').on('click', function () {

                let idCarte = $(this).data('idcarte');
                if (confirm('<?= trans('Vous allez archiver cette carte'); ?>')) {
                    $.post(
                        '<?= INTERACTIVE_MAP_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idMapDelete: idCarte
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idcarte="' + idCarte + '"]').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php endif;
require('footer.php'); ?>
