<?php

use App\Category;

require('header.php');
require_once(SHOP_PATH . 'process/addProduct.php');
$Category = new Category();
$Category->setType('SHOP');
$listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');
echo getTitle( getAppPageName(), getAppPageSlug() );
showPostResponse(getDataPostResponse());
?>
    <div class="container">
        <form action="" method="post" id="addProductForm" enctype="multipart/form-data">
            <?= getTokenField(); ?>
            <div class="row d-flex align-items-start">
                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <?= App\Form::text('Nom du Produit', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= App\Form::text('Lien du produit' . ' (Slug)', 'slug', 'text', !empty($_POST['slug']) ? $_POST['slug'] : '', true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= App\Form::select('Type de produit', 'type', TYPE_PRODUCT, !empty($_POST['type']) ? $_POST['type'] : '', true); ?>
                        </div>
                        <div class="col-12 col-lg-4 mt-2">
                            <?= App\Form::text('Prix (€)', 'price', 'text', !empty($_POST['price']) ? $_POST['price'] : '', true, 9, '', '', '', 'Ex: 16.97', false); ?>
                        </div>

                        <div class="col-12 col-lg-4 mt-2">
                            <?= App\Form::text('Poids (en grammes)', 'poids', 'text', !empty($_POST['poids']) ? $_POST['poids'] : '', false, 9, '', '', '', 'Ex: 1500 pour 1.5 kg', false); ?>
                        </div>

                        <div class="col-12 col-lg-4 mt-3">
                            <?= App\Form::text('Épaisseur (en Millimètre)', 'dimension', 'text', !empty($_POST['dimension']) ? $_POST['dimension'] : '', false, 9, '', '', '', 'Ex: 1000 pour 1 m', false); ?>
                        </div>
                        <div class="col-12 mt-2">
                            <?= App\Form::checkbox('Catégories', 'categories', $listCatgories, '', 'checkCategories'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 bgColorPrimary">
                    <div class="row">
                        <div class="col-12 py-3 my-2 mb-auto">
                            <?= App\Form::radio('Statut du produit', 'status', array_map('trans', PRODUCT_STATUS), !empty($_POST['statut']) ? $_POST['statut'] : 1, true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= App\Form::target('ADDPRODUCT'); ?>
                            <?= App\Form::submit('Enregistrer', 'ADDPRODUCTSUBMIT', 'btn-light'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="my-4"></div>
    </div>
    <script>
        $(document).ready(function () {
            $('input#name').keyup(function () {
                $('input#slug').val(convertToSlug($(this).val()));
            });
        });
    </script>
<?php require('footer.php'); ?>