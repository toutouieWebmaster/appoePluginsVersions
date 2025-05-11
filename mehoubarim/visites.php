<?php
require_once('header.php');
if (!empty($_GET['resetStats']) && $_GET['resetStats'] === 'OK') mehoubarim_cleanVisitor();

$visitors = mehoubarim_getVisitor();
$globalData = mehoubarim_getGlobal();
if ($visitors && is_array($visitors['visitors'])):
    foreach (MEHOUBARIM_TYPES as &$name) {
        if (array_key_exists($name, $visitors) && !isArrayEmpty($visitors[$name])) {
            arsort($visitors[$name]);
        }
    } ?>
    <div class="d-flex align-content-between flex-wrap w-100 h-100">
        <div class="w-100">
            <strong>
        <span class="colorSecondary">
            <i class="fas fa-clock"></i>
        </span> <?= trans('Depuis'); ?> <?= !empty($globalData['dateBegin']) ? displayCompleteDate($globalData['dateBegin'], true) : ""; ?>
            </strong>
            <div class="my-4">
                <div class="my-2 ml-0 ml-lg-4" style="position: relative;">
                    <span class="mr-2"><?= trans('Visiteurs'); ?></span>
                    <span class="visitsStatsBadge bgColorSecondary"><?= count($visitors['visitors']); ?></span>
                </div>
                <div class="my-2 ml-0 ml-lg-4" style="position: relative;">
                    <span class="mr-2"> <?= trans('Pages consultées'); ?></span>
                    <span class="visitsStatsBadge bgColorSecondary"><?= array_sum($visitors['visitors']); ?></span>
                </div>
            </div>
            <strong>
                <span class="colorSecondary"><i class="fas fa-eye"></i></span> <?= trans('Les plus consultés'); ?>
            </strong>
            <div class="my-3" id="statsDetails">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <?php foreach (MEHOUBARIM_TYPES as $type => $key):
                            if (array_key_exists($type, getMehoubarimType()) && isset($visitors[$key]) && !isArrayEmpty($visitors[$key])): ?>
                                <a class="nav-item sidebarLink colorSecondary nav-link <?= $type === 'PAGE' ? 'active' : ''; ?>"
                                   id="nav-<?= $type; ?>-tab"
                                   data-toggle="tab" href="#nav-<?= $type; ?>" role="tab"
                                   aria-controls="nav-<?= $type; ?>"
                                   aria-selected="<?= $type === 'PAGE' ? 'true' : 'false'; ?>"><?= getMehoubarimType()[$type]; ?></a>
                            <?php endif;
                        endforeach; ?>
                    </div>
                </nav>
                <div class="tab-content mt-3" id="nav-tabContent">
                    <?php foreach (MEHOUBARIM_TYPES as $type => $key):
                        if (array_key_exists($type, getMehoubarimType()) && array_key_exists($key, $visitors) && !isArrayEmpty($visitors[$key])): ?>
                            <div class="tab-pane fade <?= $type === 'PAGE' ? ' show active ' : ''; ?>"
                                 id="nav-<?= $type; ?>"
                                 role="tabpanel" aria-labelledby="nav-<?= $type; ?>-tab">
                                <?php foreach (array_slice($visitors[$key], 0, 5, true) as $name => $nb): ?>
                                    <div class="my-2 ml-0 ml-lg-4" style="position: relative;">
                                        <span class="mr-2"><?= shortenText(ucfirst(mb_strtolower($name)), 54); ?></span>
                                        <span class="visitsStatsBadge bgColorSecondary"><?= $nb; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>
        </div>
        <div class="w-100">
            <div class="text-right">
                <button class="btn btn-outline-info btn-sm border-radius-0 borderColorSecondary colorSecondary"
                        id="resetStats"
                        type="button">
                    <?= trans('Réinitialiser les statistiques'); ?>
                </button>
            </div>
            <div class="progress my-2" style="height: 1px;">
                <div class="progress-bar bgColorSecondary" role="progressbar" id="visitsLoader" style="width: 0;"
                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
<?php endif; ?>