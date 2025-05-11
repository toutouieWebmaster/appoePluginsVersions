<?php
require_once('../main.php');

use App\Plugin\Twitter\Manager;

if (twitter_is_active()):

    $Twitter = new Manager();
    $groupList = $Twitter->twitter_get_lists();
    if (is_array($groupList)):
        if (!isArrayEmpty($groupList)):
            foreach ($groupList as $list): ?>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" data-list-name="<?= $list->name; ?>"
                           class="custom-control-input twitterGroupCheckbox"
                           id="twitterGroupCheck-<?= $list->id_str; ?>">
                    <label class="custom-control-label"
                           for="twitterGroupCheck-<?= $list->id_str; ?>">
                        <h6 class="mb-1"><?= $list->name; ?>
                            <small>(<?= trans('membres'); ?>
                                : <?= $list->member_count; ?>)
                            </small>
                        </h6>
                        <p><?= $list->description; ?></p>
                    </label>
                </div>
            <?php endforeach; ?>
            <?= \App\Form::textarea('Message', 'message', '', 3, true); ?>
            <button type="button" class="btn btn-sm bgColorPrimary mt-2"
                    id="submitTwitterGroup">
                <i class="fas fa-share"></i> <?= trans('Partager'); ?>
            </button>
            <div id="twitterInfo" class="my-3"></div>
        <?php else: ?>
            <p>Vous n'avez pas de group Twitter</p>
        <?php endif;
    else: ?>
        <p>Une erreur est survenue lors de la connexion à Twitter</p>
    <?php endif;
else: ?>
    <p>Les accès de l'API Twitter ne sont pas renseignés !</p>
<?php endif; ?>
