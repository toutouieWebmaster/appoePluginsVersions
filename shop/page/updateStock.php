<?php

use App\Form;
use App\Plugin\Shop\Product;
use App\Plugin\Shop\Stock;

if (!empty($_GET['id'])):
    require('header.php');
    $Stock = new Stock($_GET['id']);
    require_once('../process/updateStock.php');
    $Product = new Product($Stock->getProductId());
    echo getTitle('Limite de stock pour ' . $Product->getName(), getAppPageSlug());
    showPostResponse(); ?>
    <div class="container">
        <form action="" method="post">
            <input type="hidden" name="stock_id" value="<?= $Stock->getId(); ?>">
            <input type="hidden" name="product_id" value="<?= $Stock->getProductId(); ?>">

            <div class="row">

                <div class="col-12 col-lg-6 my-2">
                    <?= Form::text('Quantité limitée', 'limit_quantity', 'number', $Stock->getLimitQuantity(), false, 10, 'aria-describedby="limit_quantity_help"'); ?>
                    <small id="limit_quantity_help" class="form-text text-muted">
                        <?= trans('Quantité restante'); ?> <?= $Product->getRemainingQuantity(); ?>
                    </small>
                </div>

                <div class="col-12 col-lg-6 my-2">
                    <?= Form::text('Date limitée (au format année-mois-jour, ex. 2025-01-31)', 'date_limit', 'text', $Stock->getDateLimit(), false, 10, '', '', 'datepicker'); ?>
                </div>

            </div>
            <div class="row">
                <div class="col-12 my-2">
                    <?= Form::target('UPDATESTOCK'); ?>
                    <?= Form::submit('Enregistrer', 'UPDATESTOCKSUBMIT'); ?>
                </div>
            </div>
        </form>
    </div>
    <?php require('footer.php');
endif; ?>