<?php require('header.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <?php
        //get products
        $Product = new \App\Plugin\Shop\Product();
        $Product->setStatus(0);
        $allProduct = $Product->showAll();

        //get commandes
        $Commande = new \App\Plugin\Shop\Commande();
        $allCommandes = $Commande->showAll(0);
        ?>
        <div class="row">
            <div class="col-12 my-2">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <?php if ($allProduct): ?>
                            <a class="nav-item nav-link active" id="nav-Produits-tab" data-toggle="tab"
                               href="#nav-Produits"
                               role="tab" aria-controls="nav-Produits"
                               aria-selected="true">
                                <?= trans('Produits'); ?>
                            </a>

                        <?php endif;
                        if ($allCommandes): ?>
                            <a class="nav-item nav-link" id="nav-Commandes-tab" data-toggle="tab"
                               href="#nav-Commandes"
                               role="tab" aria-controls="nav-Commandes"
                               aria-selected="true"><?= trans('Commandes'); ?></a>
                        <?php endif; ?>
                    </div>
                </nav>
                <div class="tab-content border-top-0 bg-white py-3" id="nav-mediaTabContent">
                    <?php if ($allProduct): ?>
                        <div class="tab-pane fade active show" id="nav-Produits" role="tabpanel"
                             aria-labelledby="nav-Produits-tab">
                            <div class="table-responsive">
                                <table id="pagesTable"
                                       class="sortableTable table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= trans('Nom du produit'); ?></th>
                                        <th><?= trans('Lien du produit'); ?></th>
                                        <th><?= trans('Description'); ?></th>
                                        <th><?= trans('Catégories'); ?></th>
                                        <th><?= trans('Modifié le'); ?></th>
                                        <th><?= trans('Options'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($allProduct as $product): ?>
                                        <tr data-idproduct="<?= $product->id ?>">
                                            <td><?= $product->name ?></td>
                                            <td><?= $product->slug; ?></td>
                                            <td><?= shortenText((new \App\Plugin\Shop\ProductContent($product->id))->getResume(), 70); ?></td>
                                            <td><?= implode(', ', extractFromObjToSimpleArr(getCategoriesByProduct($product->id), 'name')); ?></td>
                                            <td><?= displayTimeStamp($product->updated_at) ?></td>
                                            <td>
                                                <a href="<?= getPluginUrl('shop/page/updateProductData/', $product->id); ?>"
                                                   class="btn btn-sm"
                                                   title="<?= trans('Détails du produit'); ?>">
                                                    <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                                </a>
                                                <a href="<?= getPluginUrl('shop/page/updateProduct/', $product->id); ?>"
                                                   class="btn btn-sm"
                                                   title="<?= trans('Configurer'); ?>">
                                                    <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                                </a>
                                                <?php if (isTechnicien(getUserRoleId())): ?>
                                                    <button type="button" class="btn btn-sm deleteProduct"
                                                            title="<?= trans('Supprimer définitivement'); ?>"
                                                            data-idproduct="<?= $product->id ?>">
                                                        <span class="btnArchive"><i class="fas fa-times"></i></span>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm unpackProduct"
                                                        title="<?= trans('désarchiver'); ?>"
                                                        data-idproduct="<?= $product->id ?>">
                                                    <span class="btnCheck"> <i class="fas fa-check"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($allCommandes): ?>
                        <div class="tab-pane fade" id="nav-Commandes" role="tabpanel"
                             aria-labelledby="nav-Commandes-tab">
                            <div class="table-responsive">
                                <table id="pagesTable"
                                       class="sortableTable table table-striped">
                                    <thead>
                                    <tr>
                                        <th><?= trans('Date'); ?></th>
                                        <th><?= trans('Client'); ?></th>
                                        <th><?= trans('Adresse Email'); ?></th>
                                        <th><?= trans('Transport'); ?></th>
                                        <th><?= trans('Total'); ?></th>
                                        <th><?= trans('État du paiement'); ?></th>
                                        <th><?= trans('N° facture'); ?></th>
                                        <th><?= trans('Options'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach ($allCommandes as $commande): ?>
                                        <tr class="seeCommandeDetails" data-idcommande="<?= $commande->id ?>">
                                            <td><?= displayTimeStamp($commande->created_at) ?></td>

                                            <?php $Client = new \App\Plugin\People\People($commande->client_id); ?>
                                            <td class="client"><?= $Client->getEntitled(); ?></td>
                                            <td class="client"><?= $Client->getEmail(); ?></td>
                                            <td class="transportPrice"><?= $commande->total_transport ?>€</td>
                                            <td class="table-info"><strong><?= $commande->total ?>€</strong></td>
                                            <td><?= ORDER_STATUS[$commande->orderState] ?></td>
                                            <td><?= $commande->preBilling . '-' . formatBillingNB($commande->billing) ?></td>
                                            <td>
                                                <?php if ($commande->orderState == 3): ?>
                                                    <button type="button" class="btn btn-sm unpackCommande"
                                                            title="<?= trans('désarchiver'); ?>"
                                                            data-idcommande="<?= $commande->id ?>">
                                                        <span class="btnCheck"> <i class="fas fa-check"></i></span>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('.unpackProduct').on('click', function () {
                var idProduct = $(this).data('idproduct');
                if (confirm('<?= trans('Vous allez désarchiver ce produit'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= SHOP_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            UNPACKPRODUCT: 'OK',
                            idUnpackProduct: idProduct
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idproduct="' + idProduct + '"]').slideUp();
                                availableApp();
                            }
                        }
                    );
                }
            });

            $('.unpackCommande').on('click', function () {
                var idCommande = $(this).data('idcommande');
                if (confirm('<?= trans('Vous allez désarchiver cette commande'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= SHOP_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            UNPACKCOMMANDE: 'OK',
                            idUnpackCommande: idCommande
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idcommande="' + idCommande + '"]').slideUp();
                                availableApp();
                            }
                        }
                    );
                }
            });

            <?php if (isTechnicien(getUserRoleId())): ?>
            $('.deleteProduct').on('click', function () {

                var idProduct = $(this).data('idproduct');
                if (confirm('<?= trans('Vous allez supprimer définitivement ce produit'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= SHOP_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            deletePRODUCT: 'OK',
                            idProductDelete: idProduct
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $('tr[data-idproduct="' + idProduct + '"]').slideUp();
                                availableApp();
                            }
                        }
                    );
                }
            });
            <?php endif; ?>
        });
    </script>
<?php require('footer.php'); ?>