<?php

use App\Category;
use App\CategoryRelations;
use App\Form;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleRelation;

require('header.php');
if (!empty($_GET['id'])):

    require(ITEMGLUE_PATH . 'process/postProcess.php');
    $Article = new Article();
    $Article->setId($_GET['id']);
    $Article->setLang(APP_LANG);

    if ($Article->show()):

        $AllArticles = new Article();
        $AllArticles->setStatut($Article->getStatut() == 0 ? 0 : 1);
        $AllArticles->setLang(APP_LANG);
        $allArticles = $AllArticles->showAll();

        $Category = new Category();
        $Category->setType('ITEMGLUE');
        $listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

        $CategoryRelation = new CategoryRelations('ITEMGLUE', $Article->getId());
        $allCategoryRelations = extractFromObjToSimpleArr($CategoryRelation->getData(), 'categoryId', 'name');

        $ArticleMedia = new ArticleMedia($Article->getId());
        $ArticleMedia->setLang(APP_LANG);
        $allArticleMedias = $ArticleMedia->showFiles();

        $listUsers = extractFromObjToSimpleArr(getAllUsers(true), 'id', 'nom', 'prenom');
        $allRelations = '';

        $ArticleRelation = new ArticleRelation($Article->getId(), 'USERS');
        if ($ArticleRelation) {
            $allRelations = extractFromObjToSimpleArr($ArticleRelation->getData(), 'typeId', 'typeId');
        }

        echo getTitle($Article->getName(), getAppPageSlug());
        showPostResponse(); ?>
        <select class="custom-select custom-select-sm otherArticlesSelect otherProjetSelect notPrint float-sm-right"
                title="<?= trans('Parcourir les autres articles'); ?>...">
            <option selected="selected" disabled><?= trans('Parcourir les autres articles'); ?>
                ...
            </option>
            <?php if ($allArticles):
                foreach ($allArticles as $article):
                    if ($Article->getId() != $article->id): ?>
                        <option data-href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id); ?>"><?= $article->name; ?></option>
                    <?php endif;
                endforeach;
            endif; ?>
        </select>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link sidebarLink colorPrimary <?= !empty($contentTabActive) ? 'active' : ''; ?>"
                   id="nav-allLibraries-tab" data-toggle="tab"
                   href="#nav-allLibraries"
                   role="tab" aria-controls="nav-allLibraries"
                   aria-selected="true"><?= trans('Contenu'); ?></a>
                <a class="nav-item nav-link sidebarLink colorPrimary <?= !empty($mediaTabactive) ? 'active' : ''; ?>"
                   id="nav-newFiles-tab" data-toggle="tab" href="#nav-newFiles" role="tab"
                   aria-controls="nav-newFiles" aria-selected="false"><?= trans('Médias'); ?></a>
                <a class="nav-item nav-link sidebarLink colorPrimary"
                   id="nav-extra-tab" data-toggle="tab" href="#nav-extra" role="tab"
                   aria-controls="nav-extra" aria-selected="false"><?= trans('Détails'); ?></a>
                <a class="nav-item nav-link sidebarLink colorPrimary <?= !empty($relationActive) ? 'active' : ''; ?>"
                   id="nav-relation-tab" data-toggle="tab" href="#nav-relation" role="tab"
                   aria-controls="nav-relation" aria-selected="false"><?= trans('Association'); ?></a>
            </div>
        </nav>
        <div class="tab-content border border-top-0 bg-white py-3 mb-2" id="nav-mediaTabContent">
            <div class="tab-pane fade <?= !empty($contentTabActive) ? 'active show' : ''; ?>"
                 id="nav-allLibraries" role="tabpanel"
                 aria-labelledby="nav-allLibraries-tab">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <a id="updateArticleBtn" data-toggle="modal" data-target="#updateArticleHeadersModal"
                               href="<?= getPluginUrl('itemGlue/page/update/', $Article->getId()); ?>"
                               class="btn btn-warning btn-sm">
                                <span class="fas fa-wrench"></span> <?= trans('Modifier les en-têtes'); ?>
                            </a>
                            <?php if (defined('DEFAULT_ARTICLES_PAGE') && !empty(DEFAULT_ARTICLES_PAGE)): ?>
                                <a href="<?= webUrl(DEFAULT_ARTICLES_PAGE . '/', $Article->getSlug()); ?>"
                                   class="btn btn-primary btn-sm" target="_blank">
                                    <span class="fas fa-external-link-alt"></span> <?= trans('Visualiser l\'article'); ?>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                        data-page-lang="<?= APP_LANG; ?>" data-page-slug="<?= $Article->getSlug(); ?>"
                                        id="clearArticleCache"><i class="fas fa-eraser"></i> Vider le cache
                                </button>
                                <?php if (pluginExist('twitter')): ?>
                                    <button type="button" class="btn btn-primary btn-sm notPrint float-right ml-1"
                                            id="articleTwitterShareButton"
                                            data-toggle="modal" data-target="#modalTwitterManager"
                                            data-share-link="<?= $Article->getSlug(); ?>">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                <?php endif;
                                if (pluginExist('facebook')): ?>
                                    <button type="button"
                                            class="btn btn-primary btn-sm notPrint float-right ml-1 shareOnFb"
                                            data-fb-post-link="<?= articleUrl($Article->getSlug()); ?>">
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                <?php endif;
                            endif; ?>
                        </div>
                    </div>
                    <div class="my-2"></div>
                    <div class="row">
                        <div class="col-12">
                            <form action="" id="contentArticleManage" class="row" method="post">
                                <?= getTokenField(); ?>
                                <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                                <div class="col-12">
                                    <?= Form::textarea('articleContent', 'articleContent', htmlSpeCharDecode($Article->getContent()), 5, true, '', 'appoeditor', 'Contenu de l\'article'); ?>

                                </div>
                                <div class="my-4"></div>
                                <div class="col-12">
                                    <?= Form::checkbox('Catégories', 'categories', $listCatgories, $allCategoryRelations, 'checkCategories'); ?>
                                </div>
                                <div class="col-12">
                                    <?= Form::target('SAVEARTICLECONTENT'); ?>
                                    <?= Form::submit('Enregistrer', 'SAVEARTICLECONTENTSUBMIT'); ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= !empty($mediaTabactive) ? 'active show' : ''; ?>"
                 id="nav-newFiles" role="tabpanel" aria-labelledby="nav-newFiles-tab">
                <div class="container-fluid">
                    <form class="row" id="galleryArticleForm" action="" method="post" enctype="multipart/form-data">
                        <?= getTokenField(); ?>
                        <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                        <div class="col-12 col-lg-6 my-2">
                            <?= Form::file('Importer depuis votre appareil', 'inputFile[]', false, 'multiple', '', 'Choisissez...', false); ?>
                        </div>
                        <div class="col-12 col-lg-6 my-2">
                                <textarea name="textareaSelectedFile" id="textareaSelectedFile"
                                          class="d-none"></textarea>
                            <?= Form::text('Choisissez dans la bibliothèque', 'inputSelectFiles', 'text', '0 fichiers', false, 300, 'readonly data-toggle="modal" data-target="#allMediasModal"'); ?>
                        </div>
                        <div class="col-12">
                            <?= Form::target('ADDIMAGESTOARTICLE'); ?>
                            <?= Form::submit('Enregistrer', 'ADDIMAGESTOARTICLESUBMIT'); ?>
                        </div>
                    </form>
                    <?php if ($allArticleMedias): ?>
                        <hr class="my-4 mx-5">
                        <div class="card-columns mb-3">
                            <?php foreach ($allArticleMedias as $file): ?>
                                <div class="card view border-0 bg-none"
                                     data-file-id="<?= $file->id; ?>">
                                    <?php if (isImage(FILE_DIR_PATH . $file->name)):
                                        $fileSize = getimagesize(FILE_DIR_PATH . $file->name); ?>
                                        <img src="<?= getThumb($file->name, 370); ?>"
                                             class="img-fluid">
                                    <?php else:
                                        $fileSize = true; ?>
                                        <img src="<?= getImgAccordingExtension(getFileExtension($file->name)); ?>"
                                             class="img-fluid">
                                    <?php endif; ?>
                                    <a href="#" class="info getMediaDetails mask"
                                       data-file-id="<?= $file->id; ?>">
                                        <?php if ($fileSize || (is_array($fileSize) && $fileSize[1] > 150)): ?>
                                            <h2><?= $file->title; ?></h2>
                                            <p><?= nl2br($file->description ?? ''); ?></p>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-extra" role="tabpanel" aria-labelledby="nav-extra-tab">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div id="metaArticleContenair" data-article-id="<?= $Article->getId(); ?>"></div>
                        </div>
                        <div class="col-12 col-lg-6" style="box-shadow: -100px 0 70px -100px #ccc;">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               name="addMetaData" id="metaDataAvailable">
                                        <label class="custom-control-label" for="metaDataAvailable">
                                            <strong><?= trans('Supprimer la mise en forme'); ?></strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <form action="" method="post" id="addArticleMetaForm">
                                <input type="hidden" name="idArticle" value="<?= $Article->getId(); ?>">
                                <input type="hidden" name="UPDATEMETAARTICLE" value="">
                                <?= Form::target('ADDARTICLEMETA'); ?>
                                <?= getTokenField(); ?>
                                <div class="row">
                                    <div class="col-12 my-2">
                                        <?= Form::text('Titre', 'metaKey', 'text', '', true, 150); ?>
                                    </div>
                                    <div class="col-12 my-2">
                                        <?= Form::textarea('Contenu', 'metaValue', '', 5, true, '', 'appoeditor'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-3 my-2">
                                        <button type="reset" name="reset" id="resetmeta"
                                                class="btn btn-outline-danger btn-block btn-lg">
                                            <?= trans('Annuler'); ?>
                                        </button>
                                    </div>
                                    <div class="col-12 col-lg-9 my-2">
                                        <?= Form::submit('Enregistrer', 'ADDMETAPRODUCTSUBMIT'); ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= !empty($relationActive) ? 'active show' : ''; ?>" id="nav-relation"
                 role="tabpanel" aria-labelledby="nav-relation-tab">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <form method="post" id="relationUsersForm">
                                <?= getTokenField(); ?>
                                <input type="hidden" name="articleId" value="<?= $Article->getId(); ?>">
                                <?= Form::checkbox('Utilisateurs', 'userRelation', $listUsers, $allRelations); ?>
                                <div class="my-4"></div>
                                <?= Form::target('RELATIONUSERS'); ?>
                                <?= Form::submit('Associer', 'RELATIONUSERSSUBMIT'); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="allMediasModal" tabindex="-1" role="dialog" aria-labelledby="allMediasModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allMediasModalLabel"><?= trans('Tous les médias'); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="allMediaModalContainer"></div>
                    <div class="modal-footer">
                        <button type="button" id="closeAllMediaModalBtn" class="btn btn-secondary" data-dismiss="modal">
                            <?= trans('Fermer et annuler la sélection'); ?></button>
                        <button type="button" id="saveMediaModalBtn" class="btn btn-info" data-dismiss="modal">
                            0 <?= trans('médias'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="updateArticleHeadersModal" tabindex="-1" role="dialog"
             aria-labelledby="updateArticleHeadersModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="updateArticleHeadersForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="updateArticleHeadersModalLabel"><?= trans('Modifier les en têtes'); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-control custom-checkbox my-3">
                                <input type="checkbox" class="custom-control-input" id="updateSlugAuto">
                                <label class="custom-control-label"
                                       for="updateSlugAuto"><?= trans('Mettre à jour le lien de l\'article automatiquement'); ?></label>
                            </div>

                            <?= getTokenField(); ?>
                            <input type="hidden" name="id" value="<?= $Article->getId(); ?>">
                            <div class="row my-2">
                                <div class="col-12 my-2">
                                    <?= Form::text('Nom', 'name', 'text', $Article->getName(), true, 70, 'data-seo="title"'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::textarea('Description', 'description', $Article->getDescription(), 2, true, 'maxlength="158" data-seo="description"'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', $Article->getSlug(), true, 100, 'data-seo="slug"'); ?>
                                </div>
                                <hr class="hrStyle">
                                <div class="col-12 my-2">
                                    <?= Form::text('Date de création', 'createdAt', 'date', $Article->getCreatedAt(), true, 10); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::radio('Statut de l\'article', 'statut', array_map('trans', ITEMGLUE_ARTICLES_STATUS), $Article->getStatut(), true); ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?= Form::target('UPDATEARTICLEHEADERS'); ?>
                            <button type="submit" name="UPDATEARTICLEHEADERSSUBMIT"
                                    class="btn btn-outline-info"><?= trans('Enregistrer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="/app/lib/template/js/media.js"></script>
    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>