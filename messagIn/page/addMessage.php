<?php use App\Form;

require('header.php');
require_once('../process/postProcess.php');
getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(); ?>
    <div class="container">
        <form action="" method="post" id="addMessageForm">
            <?= getTokenField(); ?>
            <div class="my-4"></div>
            <div class="row">

                <div class="col-12 col-lg-6">
                    <p class="p-3"><?= trans('De la part de'); ?> <?= getUserEntitled(); ?></p>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="form-group">
                        <label for="toUser"><?= trans('destiné à'); ?></label>
                        <select class="form-control custom-select" id="toUser" name="toUser" required>
                            <?php foreach (getAllUsers() as $userId => $user): ?>
                                <?php if (getUserIdSession() != $user->id): ?>
                                    <option value="<?= $user->id; ?>"><?= $user->nom . ' ' . $user->prenom; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= Form::textarea('Texte', 'text', '', 5, true, '', 'ckeditor'); ?>
                </div>
            </div>

            <div class="my-4"></div>
            <div class="row">
                <div class="col-12">
                    <?= Form::target('ADDMESSAGE'); ?>
                    <?= Form::submit('Envoyer', 'ADDMESSAGESUBMIT'); ?>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
<?php require('footer.php'); ?>