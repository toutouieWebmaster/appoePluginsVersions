<?php require('header.php');
$allArticles = getRecentArticles(false, APP_LANG);
echo getTitle(getAppPageName(), getAppPageSlug(), '', '<div class="position-absolute" style="top:0;right:0;">
<button type="button" id="displayArticleAsGrid" class="btn btn-sm bgColorPrimary noHandle my-2" title="' . trans('Mode grille') . '"><i class="fas fa-th"></i></button>
    <button type="button" id="displayArticleAsTable" class="btn btn-sm bgColorPrimary noHandle my-2" disabled="disabled" title="' . trans('Mode tableau') . '"><i class="fas fa-table"></i></button></div>'); ?>
    <div class="row">
        <div class="col-12">
            <?php if ($allArticles): ?>
                <div class="table-responsive" id="articlesTable">
                    <table class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Slug'); ?></th>
                            <th><?= trans('Catégories'); ?></th>
                            <th><?= trans('Créé le'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($allArticles as $article): ?>
                            <tr data-idarticle="<?= $article->id ?>" data-description="<?= $article->description ?>"
                                data-img="<?= getArtFeaturedImg($article, ['tempPos' => 1, 'thumbSize' => 370, 'onlyUrl' => true, 'webp' => true]); ?>">
                                <td data-col="name"><?= $article->name ?></td>
                                <td data-col="slug"><?= $article->slug ?></td>
                                <td data-col="categories"><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name')); ?></td>
                                <td data-col="date"><?= displayCompleteDate($article->created_at) ?></td>
                                <td data-col="buttons">
                                    <button type="button" class="btn btn-sm featuredArticle"
                                            title="<?= $article->statut == 2 ? trans('Article standard') : trans('Article vedette'); ?>"
                                            data-idarticle="<?= $article->id ?>"
                                            data-title-standard="<?= trans('Article vedette'); ?>"
                                            data-title-vedette="<?= trans('Article standard'); ?>"
                                            data-confirm-standard="<?= trans('Cet article ne sera plus vedette'); ?>"
                                            data-confirm-vedette="<?= trans('Vous allez mettre cet article en vedette'); ?>"
                                            data-statutarticle="<?= $article->statut; ?>">
                                            <span class="text-warning">
                                            <?= $article->statut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                                    </button>
                                    <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                       class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                        <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm archiveArticle"
                                            title="<?= trans('Archiver'); ?>"
                                            data-confirm-msg="<?= trans('Vous allez archiver cet article'); ?>"
                                            data-idarticle="<?= $article->id ?>">
                                        <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                    </button>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p><?= trans('Aucun article n\'a été enregistré'); ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php require('footer.php'); ?>