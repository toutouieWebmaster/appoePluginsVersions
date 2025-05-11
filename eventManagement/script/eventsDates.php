<?php require_once('../main.php');
if (checkAjaxRequest() && !empty($_REQUEST['id'])): ?>

    <?php
    $Event = new \App\Plugin\EventManagement\Event($_REQUEST['id']);
    $EventsDates = new \App\Plugin\EventManagement\EventsDates();
    $EventsDates->setEventId($Event->getId());
    $allEvents = $EventsDates->showAllEvent();

    $eventsArray = [];
    if ($allEvents) {
        foreach ($allEvents as $event) {
            $Date = new DateTime($event->dateDebut);
            if (!in_array($Date->format('Y-m-d'), $eventsArray)) {
                $eventsArray[] = $Date->format('Y-m-d');
            }
        }
    }
    sort($eventsArray);
    ?>
    <div class="my-3">
        <ul class="noListStyle retroPlanningList">
            <?php if ($allEvents): ?>
                <?php foreach ($eventsArray as $key => $event): ?>
                    <h6><?= displayCompleteDate($event); ?></h6>
                    <?php foreach ($allEvents as $eventDate): ?>
                        <?php $Date = new DateTime($eventDate->dateDebut); ?>
                        <?php if ($Date->format('Y-m-d') == $event): ?>
                            <li>
                        <span class="deleteDateEvent float-right" title="<?= trans('Supprimer'); ?>"
                              data-iddateevent="<?= $eventDate->id ?>">
                              <i class="fas fa-times"></i>
                            </span>
                                <i class="far fa-clock"></i>
                                <?= getHoursFromDate($eventDate->dateDebut, $eventDate->dateFin); ?>
                                <?= !empty($eventDate->localisation) ? ' <i class="fas fa-map-marker-alt"></i> ' . $eventDate->localisation : ''; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <hr class="mt-3">
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>