<?php require('header.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <?php
        $Product = new \App\Plugin\Shop\Product();
        $produits = $Product->showAll();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="productTable" class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Lien</th>
                            <th>Résumé</th>
                            <th>Prix</th>
                            <th><?= trans('Catégories'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($produits as $produit): ?>
                            <?php
                            $featuredClass = ($produit->status == 2) ? 'success disabled' : 'warning spotlight';
                            $featuredText = ($produit->status == 2) ? 'En vedette' : 'Mettre en vedette';
                            $ProductContent = new \App\Plugin\Shop\ProductContent($produit->id);
                            ?>
                            <tr>
                                <td><?= TYPE_PRODUCT[$produit->type] ?></td>
                                <td><?= $produit->name ?></td>
                                <td><?= $produit->slug ?></td>
                                <td><?= shortenText($ProductContent->getResume(), 70); ?></td>
                                <td><?= $produit->price ?></td>
                                <td><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByProduct($produit->id), 'name')); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm featuredProduit"
                                            title="<?= $produit->status == 2 ? trans('Produit standard') : trans('Produit en vedette'); ?>"
                                            data-idproduct="<?= $produit->id ?>"
                                            data-statutproduit="<?= $produit->status; ?>">
                                            <span class="text-warning">
                                            <?= $produit->status == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                                </span>
                                    </button>
                                    <a href="<?= getPluginUrl('shop/page/updateProductData/', $produit->id); ?>"
                                       class="btn btn-sm"
                                       title="<?= trans('Détails du produit'); ?>">
                                        <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <a href="<?= getPluginUrl('shop/page/updateProduct/', $produit->id); ?>"
                                       class="btn btn-sm"
                                       title="<?= trans('Configurer'); ?>">
                                        <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm archiveProduct"
                                            data-idproduct="<?= $produit->id ?>" title="<?= trans('Archiver'); ?>">
                                        <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('.archiveProduct').click(function () {

                if (confirm('Vous allez archiver ce produit !')) {
                    var $btn = $(this);
                    var idProduct = $btn.data('idproduct');

                    $.post(
                        '/app/plugin/shop/process/ajaxProcess.php',
                        {
                            idArchiveProduct: idProduct
                        },
                        function (data) {
                            if (true === data || data == 'true') {
                                $btn.parent('td').parent('tr').slideUp();
                            }
                        }
                    );
                }
            });

            $('.featuredProduit').click(function () {
                busyApp();
                var $btn = $(this);
                var idProduct = $btn.data('idproduct');

                $.post(
                    '/app/plugin/shop/process/ajaxProcess.php',
                    {
                        idSpotlightProduct: idProduct
                    },
                    function (data) {
                        availableApp();
                        location.reload();
                    }
                );
            });
        });
    </script>
<?php require('footer.php'); ?>