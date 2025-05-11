<?php

use App\Plugin\GlueCard\Content;
use App\Plugin\GlueCard\Item;
use App\Plugin\GlueCard\Plan;

require_once(dirname(__DIR__) . '/main.php');
if (!empty($_GET['idHandle']) && is_numeric($_GET['idHandle'])):

    $idHandle = $_GET['idHandle'];
    $Item = new Item();
    $Item->setIdHandle($idHandle);
    $items = $Item->showByHandle();

    $Plan = new Plan();
    $Plan->setIdHandle($idHandle);
    if ($plans = $Plan->showByHandle()): ?>
        <div class="row">

            <?php if ($items):
                foreach ($items as $c => $item): ?>
                    <div class="col-12 col-xl-3 col-lg-6 mb-2 px-2">
                        <div class="card <?= $item->status > 0 ? 'borderColorSecondary' : 'border-secondary'; ?>"
                             data-handle-id="<?= $idHandle; ?>"
                             data-item-id="<?= $item->id; ?>">
                            <div class="card-header <?= $item->status > 0 ? 'bgColorSecondary' : 'bg-secondary'; ?> d-flex justify-content-between align-items-center">
                                <?php if ($item->status > 0): ?>
                                    <span>
                                    Position :
                                    <span class="ml-3 itemPositionContainer">
                                        <i class="fas fa-minus lowerOrder"></i>
                                        <strong class="px-2 text-dark itemOrder"
                                                data-count-items="<?= count($items); ?>"><?= $item->order; ?></strong>
                                        <i class="fas fa-plus increaseOrder"></i>
                                    </span>
                                </span>
                                    <span class="btn btn-sm archiveItem" title="Archiver la carte">
                                <i class="fas fa-archive"></i>
                                </span>
                                <?php else: ?>
                                    <span class="btn btn-sm unpackItem" title="Désarchiver la carte">
                                <i class="fas fa-check"></i></span>
                                    <span class="btn btn-sm deleteItem" title="Supprimer la carte">
                                <i class="fas fa-times"></i></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <form method="post" class="contentForm">
                                    <?php $Content = new Content();
                                    $Content->setIdItem($item->id);
                                    $content = extractFromObjArr($Content->showByItem(), 'id_plan');
                                    foreach ($plans as $plan):

                                        $value = !empty($content[$plan->id]) ? $content[$plan->id]->text : '';
                                        $attr = 'data-handle-id="' . $idHandle . '" data-plan-id="' . $plan->id . '" data-item-id="' . $item->id . '" ' . ($item->status == 0 ? 'readonly disabled' : '');
                                        $name = 'input-' . $item->id . '-' . $plan->id;

                                        if ($plan->type == 'textarea') {
                                            echo \App\Form::textarea($plan->name, $name, htmlSpeCharDecode($value), 4, false, $attr, 'appoeditor ajaxContentInput mb-3');
                                        } elseif ($plan->type == 'urlFile') {
                                            echo \App\Form::text($plan->name, $name, 'url', $value, false, 250, $attr, '', 'urlFile ajaxContentInput mb-3');
                                        } elseif ($plan->type == 'textBig') {
                                            echo \App\Form::textarea($plan->name, $name, htmlSpeCharDecode($value), 4, false, $attr, 'ajaxContentInput mb-3');
                                        } else {
                                            echo \App\Form::text($plan->name, $name, $plan->type, $value, false, 250, $attr, '', 'ajaxContentInput mb-3');
                                        }

                                    endforeach; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>

            <div class="col-12 col-xl-3 col-lg-6 mb-2 px-2">
                <div class="card border-0">
                    <div class="card-body" id="newItemBtnSubmit" data-id-handle="<?= $idHandle; ?>">
                        <i class="fas fa-plus"></i> Nouvelle carte</div>
                </div>
            </div>

        </div>
    <?php endif;
endif; ?>