<?php require('header.php');

use App\Plugin\ItemGlue\Article;

$Article = new Article();
$Article->setStatut(0);
$Article->setLang(APP_LANG);
$allArticles = $Article->showAll();

echo getTitle(getAppPageName(), getAppPageSlug());
?>
    <div class="row">
        <div class="col-12">
            <?php if ($allArticles): ?>
                <div class="table-responsive">
                    <table id="pagesTable"
                           class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Nom'); ?></th>
                            <th><?= trans('Slug'); ?></th>
                            <th><?= trans('Catégories'); ?></th>
                            <th><?= trans('Modifié le'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($allArticles as $article): ?>
                            <tr data-idarticle="<?= $article->id ?>">
                                <td><?= $article->name ?></td>
                                <td><?= $article->slug ?></td>
                                <td><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByArticle($article->id), 'name')); ?></td>
                                <td><?= displayTimeStamp($article->updated_at) ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $article->id) ?>"
                                       class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                        <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm deleteArticle"
                                            title="<?= trans('Supprimer'); ?>"
                                            data-idarticle="<?= $article->id ?>"
                                            data-confirm-msg="<?= trans('Vous allez supprimer définitivement cet article'); ?>">
                                        <span class="btnArchive"><i class="fas fa-times"></i></span>
                                    </button>
                                    <button type="button" class="btn btn-sm unpackArticle"
                                            data-confirm-msg="<?= trans('Vous allez désarchiver cet article'); ?>"
                                            title="<?= trans('désarchiver'); ?>"
                                            data-idarticle="<?= $article->id ?>">
                                        <span class="btnCheck"> <i class="fas fa-check"></i></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p><?= trans('Aucun article n\'a été archivé'); ?></p>
            <?php endif; ?>
        </div>
    </div>
<?php require('footer.php'); ?>