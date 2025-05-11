<?php use App\Plugin\Shop\Product;
use App\Plugin\Shop\Stock;

require('header.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <?php
        $Stock = new Stock();
        $listStock = $Stock->showAll();
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="stockTable" class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Quantité restante / disponible</th>
                            <th>Date limité à</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($listStock as $stock): ?>
                            <?php $Product = new Product($stock->product_id); ?>

                            <?php
                            $limitQuantity = !is_null($stock->limit_quantity) ? $Product->getRemainingQuantity() . ' / ' . $stock->limit_quantity : '-';
                            $limitDate = !is_null($stock->date_limit) ? displayCompleteDate($stock->date_limit) : '-';
                            ?>
                            <tr>
                                <td><?= displayTimeStamp($stock->updated_at); ?></td>
                                <td><?= $Product->getName(); ?></td>
                                <td><?= $limitQuantity ?></td>
                                <td><?= $limitDate ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('shop/page/updateStock/', $stock->id); ?>"
                                       class="btn btn-sm" title="<?= trans('Modifier'); ?>">
                                        <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                    </a>
                                    <button type="button" class="btn btn-sm deleteStock"
                                            data-stock="<?= $stock->id ?>" title="<?= trans('Supprimer'); ?>">
                                        <span class="btnArchive"><i class="fas fa-times"></i></span>
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

            $('.deleteStock').click(function () {

                if (confirm('Vous allez supprimer ce stock !')) {
                    busyApp();
                    var $btn = $(this);
                    var idStock = $btn.data('stock');

                    $.post(
                        '<?= SHOP_URL; ?>process/ajaxProcess.php',
                        {
                            DELETESTOCK: 'OK',
                            idDeleteStock: idStock
                        },
                        function (data) {
                            if (true === data || data == 'true') {
                                availableApp();
                                $btn.parent('td').parent('tr').slideUp();
                            }
                        }
                    );
                }
            });
        });
    </script>
<?php require('footer.php'); ?>