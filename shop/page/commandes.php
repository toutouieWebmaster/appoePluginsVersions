<?php require('header.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <?php
        $Commande = new \App\Plugin\Shop\Commande();
        $commandes = $Commande->showAll();
        ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="commandeTable" class="sortableTable table table-striped">
                        <thead>
                        <tr>
                            <th><?= trans('Date'); ?></th>
                            <th><?= trans('Client'); ?></th>
                            <th><?= trans('Transport'); ?></th>
                            <th><?= trans('Total'); ?></th>
                            <th><?= trans('État du paiement'); ?></th>
                            <th><?= trans('État de la livraison'); ?></th>
                            <th><?= trans('N° facture'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($commandes):
                            foreach ($commandes as $commande): ?>
                                <?php
                                $bgOrderState = $commande->orderState == 3 ? 'success' : 'danger';
                                $bgDeliveryState = match ($commande->deliveryState) {
                                    2 => 'success',
                                    default => 'warning',
                                };
                                ?>
                                <tr class="seeCommandeDetails" data-commandeid="<?= $commande->id ?>">
                                    <td><?= displayTimeStamp($commande->created_at) ?></td>

                                    <?php $Client = new \App\Plugin\People\People($commande->client_id); ?>
                                    <td class="client"><?= $Client->getEntitled(); ?></td>
                                    <td class="transportPrice"><?= $commande->total_transport ?>€</td>
                                    <td class="table-info"><strong><?= $commande->total ?>€</strong></td>
                                    <td class="table-<?= $bgOrderState ?>">
                                        <?= ORDER_STATUS[$commande->orderState] ?></td>
                                    <td class="table-<?= $bgDeliveryState ?> commandeEtat"
                                        data-commandeid="<?= $commande->id ?>"><?= DELIVERY_STATE[$commande->deliveryState] ?></td>
                                    <td><?= $commande->preBilling . '-' . formatBillingNB($commande->billing) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm cancelCommande"
                                                data-commandeid="<?= $commande->id; ?>"
                                                title="<?= trans('Annuler / archiver la commande'); ?>">
                                            <span class="btnArchive"><i class="fas fa-times"></i></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('.cancelCommande').click(function (event) {
                event.stopPropagation();
                event.preventDefault();

                let id_commande = $(this).data('commandeid');

                if (confirm('<?= trans('Vous allez annuler / archiver cette commande'); ?>')) {
                    cancelCommande(id_commande).done(function (data) {
                        if (data == 'true') {
                            $('tr.seeCommandeDetails[data-commandeid="' + id_commande + '"]').fadeOut();
                        }
                    });
                }
            });

            $('body').on('click', 'button.changeCommandeEtat', function () {
                let id_commande = $(this).data('commandeid');
                let deliveryState = $(this).data('deliverystate');
                if ($(this).hasClass('changeCommandeEtat')) {
                    $('#modalInfo').modal('hide');
                    changeCommandeDeliveryState(id_commande, deliveryState);
                }
            });

            $('tr.seeCommandeDetails').on('click', function (event) {
                event.preventDefault();

                let transport = parseFloat($(this).find('.transportPrice').text());
                let id_commande = $(this).data('commandeid');

                getCommandeDetails(id_commande, transport);

            }).children('td.commandeEtat').click(function(event) {
                event.stopPropagation();
                event.preventDefault();

                let id_commande = $(this).data('commandeid');

                getEtatChoise(id_commande);
                return false;
            });
        });
    </script>
<?php require('footer.php'); ?>