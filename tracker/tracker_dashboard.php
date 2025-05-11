<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');

$MonthAgo = new DateTime();
$MonthAgo->sub(new DateInterval('P1M'));
$dateStart = !empty($_GET['dateStart']) ? $_GET['dateStart'] : $MonthAgo->format('Y-m-d');
$dateEnd = !empty($_GET['dateEnd']) ? $_GET['dateEnd'] : date('Y-m-d');

$Tracker = new App\Plugin\Tracker\Tracker();
$trackerData = $Tracker->getData($dateStart, $dateEnd);
if ($trackerData): ?>
    <div class="d-flex align-content-between flex-wrap w-100 h-100" id="trackerData">
        <div class="w-100">
            <strong class="d-block w-100">
                <span class="colorSecondary"><i class="fas fa-clock"></i></span> <?= trans('Depuis'); ?>
                <div class="d-inline-block mx-1"><input type="date" id="dateStart" class="noBorder" max="<?= date('Y-m-d'); ?>" value="<?= $dateStart; ?>"></div>
                <div class="d-inline-block mx-1"><?= trans('Jusqu\'à'); ?> <input type="date" id="dateEnd" class="noBorder" max="<?= date('Y-m-d'); ?>" value="<?= $dateEnd; ?>"></div>
            </strong>
            <div class="my-4">
                <div class="my-2 ml-0 ml-lg-4 position-relative">
                    <span class="mr-2"><?= trans('Visiteurs'); ?></span>
                    <span class="visitsStatsBadge bgColorSecondary"><?= $trackerData['visitors']->ips; ?></span>
                </div>
                <div class="my-2 ml-0 ml-lg-4 position-relative">
                    <span class="mr-2"> <?= trans('Pages consultées'); ?></span>
                    <span class="visitsStatsBadge bgColorSecondary"><?= $trackerData['visitors']->consultedPages; ?></span>
                </div>
            </div>
            <strong>
                <span class="colorSecondary"><i class="fas fa-eye"></i></span> <?= trans('Les plus consultés'); ?>
            </strong>
            <div class="my-3" id="statsDetails">
                <nav>
                    <div class="nav nav-tabs" role="tablist">
                        <?php foreach ($trackerData['pagesType'] as $type => $count): if($count == 0) continue; ?>
                            <a class="nav-item sidebarLink colorSecondary nav-link <?= $type === 'PAGE' ? 'active' : ''; ?>"
                               id="nav-<?= $type; ?>-tab"
                               data-toggle="tab" href="#nav-tracker-<?= $type; ?>" role="tab"
                               aria-controls="nav-<?= $type; ?>"
                               aria-selected="<?= $type === 'PAGE' ? 'true' : 'false'; ?>"><?= $type; ?> (<?= $count; ?>)</a>
                        <?php endforeach; ?>
                    </div>
                </nav>
                <div class="tab-content mt-3">
                    <?php foreach ($trackerData['pagesType'] as $type => $count): if($count == 0) continue; ?>
                        <div class="tab-pane fade <?= $type === 'PAGE' ? ' show active ' : ''; ?>"
                             id="nav-tracker-<?= $type; ?>"
                             role="tabpanel" aria-labelledby="nav-<?= $type; ?>-tab">
                            <?php
                            foreach (array_slice($trackerData['pagesName'][$type], 0, 5, true) as $page): ?>
                                <div class="my-2 ml-0 ml-lg-4" style="position: relative;">
                                    <span class="mr-2"><?= shortenText($page->pageName, 54); ?></span>
                                    <span class="visitsStatsBadge bgColorSecondary"><?= $page->count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>