<?php

use App\ShinouiKatan;

require_once('header.php');

//Connected User
mehoubarim_connectedUserStatus();
$mehoubarim = mehoubarim_connectedUsers();
if ($mehoubarim && is_array($mehoubarim)): ?>
    <li id="actifUsers" class="pt-3 pl-2 pb-0 pr-2" style="font-size: 0.8em;"
        data-user-status="<?= mehoubarim_getConnectedStatut(); ?>">
        <strong><?= trans('Utilisateurs actifs'); ?></strong>
    </li>
    <?php foreach ($mehoubarim as $connectedUserId => $connectedUserData):
        $connectedUserId = ShinouiKatan::Decrypter($connectedUserId);
        if (getUserIdSession() != $connectedUserId && getUserRoleId() >= getUserRoleId($connectedUserId)
            && $connectedUserData['status'] < 4 && isUserExist($connectedUserId)): ?>

            <li class="list-inline-item p-0 pr-2 mr-0" style="font-size: 0.7em;">
                <span class="activeUser pb-1 border-bottom border-<?= STATUS_CONNECTED_USER[$connectedUserData['status']]; ?>"
                      style="position: relative;cursor: pointer;"
                      data-page-consulting="<?= $connectedUserData['pageConsulting']; ?>"
                      data-last-connexion="<?= $connectedUserData['lastConnect']; ?>"
                      data-user-name="<?= getUserEntitled($connectedUserId); ?>"
                      data-user-status="<?= $connectedUserData['status']; ?>"
                      data-userid="<?= $connectedUserId; ?>"
                      data-txt-btn-logout="<?= trans("Déconnecter l'utilisateur"); ?>"
                      data-txt-btn-freeuser="<?= trans("Libérer la page de l'utilisateur"); ?>">
                   <?= getUserFirstName($connectedUserId) . ucfirst(substr(getUserName($connectedUserId), 0, 1)); ?>
                </span>
            </li>
        <?php endif;
    endforeach;
endif; ?>
<script>
    jQuery(document).ajaxSend(function (e, xhr, options) {

        var userStatus = $('#actifUsers').data('user-status');

        if (!userStatus || userStatus == "Déconnecté") {
            xhr.abort();
            return false;
        }
    });
</script>