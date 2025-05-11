<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once('../ini.php');
includePluginsFiles();
$allRating = getAllRates();
?>
<table id="ratingTable" class="sortableTable table table-striped">
    <thead>
    <tr>
        <th><?= trans('Type'); ?></th>
        <th><?= trans('Titre'); ?></th>
        <th><?= trans('Note'); ?></th>
        <th><?= trans('Nombre d\'évaluations'); ?></th>
        <th><?= trans('Score'); ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($allRating):
        foreach ($allRating as $key => $type):
            foreach ($type as $typeId => $rating) :
                $Obj = getObj($key);
                $Obj->setId($typeId);
                $Obj->show();
                ?>
                <tr>
                    <td><?= trans(TYPES_NAMES[$key]); ?></td>
                    <td><?= $Obj->getName(); ?></td>
                    <td>
                <span style="margin-right: 10px;">
                    <strong><?= $rating['average'] ?></strong>/5
                </span> <?= showRatings($key, $typeId, false, 'littleStars', true); ?>
                    </td>
                    <td><?= $rating['nbVotes'] ?></td>
                    <td><?= $rating['score'] ?></td>
                    <td>
                        <button type="button" class="btn btn-sm initRating"
                                title="<?= trans('Réinitialiser l\'évaluation'); ?>"
                                data-type="<?= $key; ?>" data-typeid="<?= $typeId ?>">
                            <span class="btnArchive"><i class="fas fa-times"></i></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach;
        endforeach;
    endif; ?>
    </tbody>
</table>
<script type="text/javascript" src="/app/plugin/rating/js/rating_base.js"></script>
