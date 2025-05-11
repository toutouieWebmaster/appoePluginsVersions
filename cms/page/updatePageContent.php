<?php
require('header.php');

use App\Form;
use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsTemplate;

require(CMS_PATH . 'process/postProcess.php');

if (!empty($_GET['id'])):

    $Cms = new Cms();
    $Cms->setId($_GET['id']);
    $Cms->setLang(APP_LANG);

    if ($Cms->show()):

        // get page content
        $CmsContent = new CmsContent($Cms->getId(), APP_LANG);

        //get all pages for navigations
        $allCmsPages = $Cms->showAll();

        //get all html files
        $files = getFilesFromDir(WEB_PUBLIC_PATH . 'html/', ['onlyFiles' => true, 'onlyExtension' => 'php', 'noExtensionDisplaying' => true]);
        echo getTitle(trans('Contenu de la page') . '<strong> ' . $Cms->getName() . '</strong>', getAppPageSlug());
        showPostResponse(); ?>
        <div class="row my-2">
            <div class="col-12 table-responsive">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= trans('Type'); ?></th>
                        <th><?= trans('Fichier'); ?></th>
                        <th><?= trans('Slug'); ?></th>
                        <th><?= trans('Nom du menu'); ?></th>
                        <th><?= trans('Nom de la page'); ?></th>
                        <th class="text-left"><?= trans('Description'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td data-cms="id"><?= $Cms->getId(); ?></td>
                        <td><?= $Cms->getType(); ?></td>
                        <td><?= $Cms->getFilename(); ?></td>
                        <td data-slug-page="<?= $Cms->getSlug(); ?>"><?= $Cms->getSlug(); ?></td>
                        <td><?= $Cms->getMenuName(); ?></td>
                        <td><?= $Cms->getName(); ?></td>
                        <td class="text-left"><?= $Cms->getDescription(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <div class="row my-2">
                    <div class="col-12 col-lg-8 my-2">
                        <button id="updatePageBtn" data-toggle="modal" data-target="#updatePageModal"
                                class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-wrench"></i> <?= trans('Modifier les en têtes'); ?>
                        </button>
                        <?php if ($Cms->getType() === 'PAGE'): ?>
                            <a href="<?= webUrl($Cms->getSlug() . '/'); ?>"
                               class="btn btn-outline-info btn-sm" id="takeLookToPage" target="_blank">
                                <i class="fas fa-external-link-alt"></i> <?= trans('Visualiser la page'); ?>
                            </a>
                            <button class="btn btn-sm btn-outline-danger"
                                    data-page-lang="<?= APP_LANG; ?>" data-page-slug="<?= $Cms->getSlug(); ?>"
                                    id="clearPageCache"><i class="fas fa-eraser"></i> Vider le cache
                            </button>
                            <?php if (isTechnicien(getUserRoleId())): ?>
                                <button class="btn btn-sm btn-outline-dark"
                                        data-page-lang="<?= APP_LANG; ?>" data-page-id="<?= $Cms->getId(); ?>"
                                        id="fillLorem"><i class="fas fa-paint-roller"></i> Préremplir la page
                                </button>
                            <?php endif;
                        endif; ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2 text-right">
                        <select class="custom-select custom-select-sm otherPagesSelect"
                                title="<?= trans('Parcourir les pages'); ?>...">
                            <option selected="selected" disabled><?= trans('Parcourir les pages'); ?>...</option>
                            <?php foreach ($allCmsPages as $pageSelect):
                                if ($Cms->getId() != $pageSelect->id): ?>
                                    <option data-href="<?= getPluginUrl('cms/page/pageContent/', $pageSelect->id); ?>"><?= $pageSelect->menuName; ?></option>
                                <?php endif;
                            endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php if (file_exists(WEB_PATH . $Cms->getFilename() . '.php')): ?>
        <div class="row mb-2">
            <div class="col-12 col-lg-10">
                <form action="" method="post" id="pageContentManageForm" style="display:none;">
                    <?php
                    $Template = new CmsTemplate(WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData());
                    $Template->show(); ?>
                </form>
            </div>
            <div class="d-none d-lg-block col-2 positionRelative">
                <nav id="headerLinks" class="list-group list-group-flush"></nav>
            </div>
        </div>
    <?php else: ?>
        <p><?= trans('Model manquant'); ?></p>
    <?php endif; ?>

        <div class="modal fade" id="updatePageModal" tabindex="-1" role="dialog"
             aria-labelledby="updatePageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="updatePageForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updatePageModalLabel">Modifier les en têtes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-control custom-checkbox my-3">
                                <input type="checkbox" class="custom-control-input" id="updateSlugAuto">
                                <label class="custom-control-label"
                                       for="updateSlugAuto"><?= trans('Mettre à jour le lien de la page automatiquement'); ?></label>
                            </div>

                            <?= getTokenField(); ?>
                            <input type="hidden" name="id" value="<?= $Cms->getId(); ?>">
                            <div class="row my-2">
                                <div class="col-12 my-2">
                                    <?= Form::text('Nom', 'name', 'text', $Cms->getName(), true, 70, 'data-seo="title"'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::textarea('Description', 'description', $Cms->getDescription(), 2, true, 'maxlength="158" data-seo="description"'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::text('Nom du menu', 'menuName', 'text', $Cms->getMenuName(), true, 70); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', $Cms->getSlug(), true, 70, 'data-seo="slug"'); ?>
                                </div>
                                <?php if (isTechnicien(getUserRoleId())): ?>
                                    <hr class="hrStyle">
                                    <div class="col-12 my-2">
                                        <?= Form::select('Fichier', 'filename', array_combine($files, $files), $Cms->getFilename(), true); ?>
                                    </div>
                                    <div class="col-12 my-2">
                                        <?= Form::select('Type de page', 'type', array_combine(CMS_TYPES, CMS_TYPES), $Cms->getType(), true); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?= Form::target('UPDATEPAGE'); ?>
                            <button type="submit" name="UPDATEPAGESUBMIT"
                                    class="btn btn-outline-info"><?= trans('Enregistrer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>