<?php
require('header.php');

use App\Form;
use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsMenu;
use App\Plugin\ItemGlue\Article;

require(CMS_PATH . 'process/ajaxProcess.php');
require(CMS_PATH . 'process/postProcess.php');

$Cms = new Cms();
$Cms->setLang(APP_LANG);

$CmsMenu = new CmsMenu();
$Articles = new Article();

$allCmsPages = $Cms->showAllPages();
$allCmsMenu = $CmsMenu->showAll(false, APP_LANG);

$MENUS = constructMenu($allCmsMenu);

$allPages = extractFromObjToSimpleArr($allCmsPages, 'id', 'menuName');
$allArticles = extractFromObjToSimpleArr($Articles->showAll(), 'slug', 'name');
$allArticlesPages = extractFromObjToSimpleArr($allCmsPages, 'slug', 'menuName');
echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button id="addMenuPage" type="button" class="btn btn-primary mb-4" data-toggle="modal"
                        data-target="#modalAddMenuPage">
                    <?= trans('Nouvelle page au menu'); ?>
                </button>
            </div>
        </div>
        <div class="row my-3">
            <?php foreach (CMS_LOCATIONS as $key => $value): ?>
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="subTitle"><?= $value; ?> <span class="badge badge-info"><?= $key; ?></span></h2>
                        </div>
                    </div>
                    <div class="row mb-4" id="menuAdminUpdate">
                        <div class="col-12">
                            <?php if (isset($MENUS[$key][10])):
                                foreach ($MENUS[$key][10] as $menu):
                                    if ($menu->location == $key): ?>
                                        <div data-menuid="<?= $menu->id; ?>"
                                             class="m-0 mt-3 py-0 px-3 jumbotron bg-warning text-white fileContent">
                                            <input type="tel" class="updateMenuData positionMenuSpan"
                                                   data-menuid="<?= $menu->id; ?>" data-column="position"
                                                   value="<?= $menu->position; ?>">
                                            <input type="text" data-menuid="<?= $menu->id; ?>" class="updateMenuData"
                                                   data-column="name" value="<?= $menu->name; ?>"
                                                <?= is_numeric($menu->idCms) ? ' readonly ' : ''; ?>>
                                            <small class="inputInfo"></small>
                                            <?php if (empty($MENUS[$key][$menu->id])): ?>
                                                <button type="button" class="close deleteMenu">
                                                    <span class="fas fa-times"></span>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="close updateIdCmsMenuBtn mr-3">
                                                <span class="fas fa-link"></span>
                                            </button>
                                            <input type="text"
                                                   data-menuid="<?= $menu->id; ?>"
                                                   class="updateMenuData updateIdCmsMenu idCmsMenuInput"
                                                   data-column="idCms"
                                                   value="<?= $menu->idCms; ?>">
                                        </div>
                                        <?php if (!empty($MENUS[$key][$menu->id])):
                                            foreach ($MENUS[$key][$menu->id] as $subMenu): ?>
                                                <div class="px-3 py-0 m-0 ml-4 mt-1 jumbotron fileContent"
                                                     data-menuid="<?= $subMenu->id; ?>">
                                                    <input type="tel" class="updateMenuData positionMenuSpan"
                                                           data-menuid="<?= $subMenu->id; ?>" data-column="position"
                                                           value="<?= $subMenu->position; ?>">
                                                    <input type="text" data-menuid="<?= $subMenu->id; ?>"
                                                           class="updateMenuData" data-column="name"
                                                           value="<?= $subMenu->name; ?>"
                                                        <?= is_numeric($subMenu->idCms) ? ' readonly ' : ''; ?>>
                                                    <small class="inputInfo"></small>
                                                    <?php if (empty($MENUS[$key][$subMenu->id])): ?>
                                                        <button type="button" class="close deleteMenu">
                                                            <span class="fas fa-times"></span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="close updateIdCmsMenuBtn mr-3">
                                                        <span class="fas fa-link"></span>
                                                    </button>
                                                    <input type="text"
                                                           data-menuid="<?= $subMenu->id; ?>"
                                                           class="updateMenuData updateIdCmsMenu idCmsMenuInput"
                                                           data-column="idCms"
                                                           value="<?= $subMenu->idCms; ?>">
                                                </div>
                                                <?php if (!empty($MENUS[$key][$subMenu->id])):
                                                    foreach ($MENUS[$key][$subMenu->id] as $subSubMenu): ?>
                                                        <div class="px-3 py-0 m-0 ml-5 mt-1 jumbotron fileContent"
                                                             data-menuid="<?= $subSubMenu->id; ?>">
                                                            <input type="tel" class="updateMenuData positionMenuSpan"
                                                                   data-menuid="<?= $subSubMenu->id; ?>"
                                                                   data-column="position"
                                                                   value="<?= $subSubMenu->position; ?>">
                                                            <input type="text" data-menuid="<?= $subSubMenu->id; ?>"
                                                                   class="updateMenuData" data-column="name"
                                                                   value="<?= $subSubMenu->name; ?>"
                                                                <?= is_numeric($subSubMenu->idCms) ? ' readonly ' : ''; ?>>
                                                            <small class="inputInfo"></small>
                                                            <?php if (empty($MENUS[$key][$subSubMenu->id])): ?>
                                                                <button type="button" class="close deleteMenu">
                                                                    <span class="fas fa-times"></span>
                                                                </button>
                                                            <?php endif; ?>
                                                            <button type="button" class="close updateIdCmsMenuBtn mr-3">
                                                                <span class="fas fa-link"></span>
                                                            </button>
                                                            <input type="text"
                                                                   data-menuid="<?= $subSubMenu->id; ?>"
                                                                   class="updateMenuData updateIdCmsMenu idCmsMenuInput"
                                                                   data-column="idCms"
                                                                   value="<?= $subSubMenu->idCms; ?>">
                                                        </div>
                                                    <?php endforeach;
                                                endif;
                                            endforeach;
                                        endif;
                                    endif;
                                endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="modalAddMenuPage" tabindex="-1" role="dialog" aria-labelledby="modalAddMenuPageTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addMenuPageForm">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddMenuPageTitle"><?= trans('Ajouter une page au menu'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalAddMenuPageBody">
                        <?= getTokenField(); ?>
                        <div class="row">
                            <div class="col-12 mt-2">
                                <?= Form::radio('Type de menu', 'radioBtnIdCMS', array('Page' => 'Page', 'URL' => 'URL', 'Article' => 'Article'), !empty($_POST['radioBtnIdCMS']) ? $_POST['radioBtnIdCMS'] : 'Page', true, 'custom-control-inline'); ?>
                            </div>
                            <div class="col-12 my-2 idCmsChoise" data-cmstype="URL" style="display: none;">
                                <?= Form::text('Lien URL', 'idCms', 'text', !empty($_POST['idCms']) ? $_POST['idCms'] : '', true, 255, 'disabled'); ?>
                            </div>
                            <div class="col-12 my-2 idCmsChoise" data-cmstype="Page">
                                <?= Form::select('Page', 'idCms', $allPages, !empty($_POST['idCms']) ? $_POST['idCms'] : '', true); ?>
                            </div>
                            <div class="col-12 my-2 idArticleChoise" data-cmstype="Article" style="display: none;">
                                <?= Form::select('Article', 'idArticle', $allArticles, !empty($_POST['idArticle']) ? $_POST['idArticle'] : '', true, 'disabled'); ?>
                                <input type="hidden" name="name" value="">
                            </div>
                            <div class="col-12 my-2 idArticleChoise" data-cmstype="Article" style="display: none;">
                                <?= Form::select('Page de l\'article', 'slugArticlePage', $allArticlesPages, !empty($_POST['slugArticlePage']) ? $_POST['slugArticlePage'] : '', true, 'disabled'); ?>
                            </div>
                            <div class="col-12 my-2 idCmsChoise" data-cmstype="URL" style="display: none;">
                                <?= Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70, 'disabled'); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= Form::text('Position / Ordre', 'position', 'tel', !empty($_POST['position']) ? $_POST['position'] : ''); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= Form::select('Emplacement', 'location', CMS_LOCATIONS, !empty($_POST['location']) ? $_POST['location'] : '', true); ?>
                            </div>
                            <div class="col-12 my-2" id="parentPageForm"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalAddMenuPageFooter">
                        <?= Form::target('ADDMENUPAGE'); ?>
                        <button type="submit" name="ADDMENUPAGESUBMIT"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('input[name="radioBtnIdCMS"]').on('change', function () {

                var dataType = $(this).val();
                $('[data-cmstype]').slideUp().find('input, select').prop('disabled', true);
                $('[data-cmstype="' + dataType + '"]').slideDown().find('input, select').prop('disabled', false);
            });

            $('select#idArticle').on('input', function () {
                $('div[data-cmstype="Article"] input[name="name"]').val($('option:selected', this).text());
            });

            $('select#location').on('change', function () {
                var location = $(this).val();
                var $parentPageInput = $('#parentPageForm');
                $parentPageInput.html('<i class="fas fa-circle-notch fa-spin"></i> <?= trans('Chargement'); ?>');
                $.post(
                    '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                    {
                        getParentPageByLocation: location
                    },
                    function (data) {
                        if (data) {
                            $parentPageInput.html(data);
                        }
                    }
                )
            });

            $('.updateMenuData').on('input', function (event) {
                event.preventDefault();

                busyApp();
                var $input = $(this);
                var column = $input.data('column');
                var idMenu = $input.data('menuid');
                var value = $input.val();
                var $inputInfo = $input.parent('div').children('small.inputInfo');
                $('small.inputInfo').html('');

                delay(function () {
                    $.post(
                        '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            updateMenu: 'OK',
                            column: column,
                            idMenu: idMenu,
                            value: value
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $inputInfo.html('<?= trans('Enregistré'); ?>');
                                availableApp();
                            }
                        }
                    );
                }, 500);
            });

            $('.updateIdCmsMenuBtn').on('click', function (event) {
                event.preventDefault();

                var inputIdCms = $(this).next('input.updateIdCmsMenu');
                if (inputIdCms.css('display') == 'none') {
                    inputIdCms.fadeIn();
                } else {
                    inputIdCms.fadeOut();
                }

            });

            $('.deleteMenu').on('click', function () {
                var div = $(this).parent('div');
                var menuId = div.data('menuid');
                if (confirm('<?= trans('Vous allez supprimer ce menu'); ?>')) {
                    $.post(
                        '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idCmsMenuDelete: menuId
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                div.slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>