<?php

use App\Plugin\GlueCard\Handle;
use App\Plugin\GlueCard\Plan;

require_once(dirname(__DIR__) . '/main.php');
$idHandle = !empty($_GET['idHandle']) ? $_GET['idHandle'] : 0;
?>
<div class="accordion mb-3" id="handlesCollapses" data-current-id-handler="">
    <?php $Handle = new Handle();
    if ($handles = $Handle->showAll()):
        foreach ($handles as $c => $handle): ?>
            <div class="card handleCard <?= $handle->status > 0 ? 'borderColorPrimary' : 'border-secondary'; ?>">
                <div class="card-header <?= $handle->status > 0 ? 'bgColorPrimary' : 'bg-secondary'; ?>"
                     id="heading<?= slugify($handle->name); ?>">
                    <h2 class="mb-0 position-relative">
                        <button class="btn btn-link handleBtn btn-block text-left" type="button"
                                data-toggle="collapse" data-handle-id="<?= $handle->id; ?>"
                            <?php if ($handle->status > 0): ?>
                                aria-controls="collapse<?= slugify($handle->name); ?>"
                                data-target="#collapse<?= slugify($handle->name); ?>"
                            <?php endif; ?>
                                aria-expanded="<?= $idHandle == 0 ? ($idHandle == $c ? 'true' : 'false') : ($idHandle == $handle->id ? 'true' : 'false'); ?>">
                            <input name="handleNameInput" readonly type="text" value="<?= $handle->name; ?>">
                            <?php if ($handle->status > 0): ?>
                                <span class="btn btn-sm float-right archiveHandle" title="Archiver la catégorie">
                                <i class="fas fa-archive"></i></span>
                                <span class="btn btn-sm float-right updateHandle" title="Modifier la catégorie">
                                <i class="fas fa-pencil-alt"></i></span>
                            <?php else:
                                if (isTechnicien(getUserRoleId())): ?>
                                    <span class="btn btn-sm float-right deleteHandle" title="Supprimer la catégorie">
                                <i class="fas fa-times"></i></span>
                                <?php endif; ?>
                                <span class="btn btn-sm float-right unpackHandle" title="désarchiver la catégorie">
                                <i class="fas fa-check"></i></span>
                            <?php endif; ?>
                        </button>
                        <?php if (isTechnicien(getUserRoleId())): ?>
                            <span class="idHandle"><?= $handle->id; ?></span>
                        <?php endif; ?>
                    </h2>
                </div>
                <div id="collapse<?= slugify($handle->name); ?>" data-parent="#handlesCollapses"
                     aria-labelledby="heading<?= slugify($handle->name); ?>" data-handle-id="<?= $handle->id; ?>"
                     class="collapse <?= $idHandle == 0 ? ($idHandle == $c ? 'show' : '') : ($idHandle == $handle->id ? 'show' : ''); ?>">
                    <div class="card-body">
                        <?php
                        if (isTechnicien(getUserRoleId())): ?>
                            <ul class="list-group list-group-horizontal flex-wrap">
                                <?php $Plan = new Plan();
                                $Plan->setIdHandle($handle->id);
                                if ($plans = $Plan->showByHandle()):
                                    foreach ($plans as $plan): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center"
                                            data-handle-id="<?= $handle->id; ?>" data-plan-id="<?= $plan->id; ?>">
                                            <input name="planNameInput" readonly type="text"
                                                   value="<?= $plan->name; ?>">
                                            <span class="d-flex flex-row">
                                            <span class="btn btn-sm updatePlan py-0" title="Modifier le plan">
                                                <i class="fas fa-pencil-alt"></i></span>
                                            <span class="btn btn-sm removePlan py-0" title="Supprimer le plan">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
                                        </span>
                                        </li>
                                    <?php endforeach;
                                endif; ?>
                                <li class="list-group-item list-group-item-info d-flex justify-content-between align-items-center newPlan"
                                    data-handle-id="<?= $handle->id; ?>">
                                    <span style="cursor: pointer;"><i class="fas fa-plus"></i> Nouveau plan</span>
                                </li>
                            </ul>
                        <?php endif; ?>

                        <form method="post" data-handle-id="<?= $handle->id; ?>" class="newPlanForm container my-5">
                            <input type="hidden" name="newPlanHandleId" value="<?= $handle->id; ?>">
                            <small class="text-muted d-block mb-1">Ajoutez un plan à la structure de vos cartes en
                                choisissant le type de données qui y sera stocké</small>
                            <div class="form-row">
                                <div class="col-12 col-md-3 mb-2">
                                    <input class="form-control" type="text" name="newPlanInputName"
                                           placeholder="Nom du nouveau plan" required>
                                </div>
                                <div class="col-12 col-md-3 mb-2">
                                    <select class="custom-select selectPlan" name="newPlanInputType" required>
                                        <option value="" disabled selected>Type de plan</option>
                                        <option value="text" data-description="Un champ texte simple">Texte</option>
                                        <option value="tel" data-description="Un champ de numéros">Téléphone</option>
                                        <option value="email" data-description="Un champ email">Email</option>
                                        <option value="url" data-description="Un champ de lien hypertexte">Lien</option>
                                        <option value="urlFile"
                                                data-description="Un champ de lien hypertexte avec choix dans la bibliothèque">
                                            Lien media
                                        </option>
                                        <option value="textBig" data-description="Grande zone de texte simple">Zone de
                                            texte
                                        </option>
                                        <option value="textarea" data-description="Grande zone de texte enrichie">Zone
                                            de texte enrichie
                                        </option>
                                    </select>
                                    <small class="text-muted d-block">Déterminez le type de données</small>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button class="btn btn-outline-info" type="submit">Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="position-relative cardsContainer mt-3 p-0 p-lg-3"
                             data-handle-id="<?= $handle->id; ?>"></div>
                    </div>
                </div>
            </div>
        <?php endforeach;
    endif;
    if (isTechnicien(getUserRoleId())): ?>
        <div class="py-3">
            <button type="button" id="newHandle" class="btn btn-light">
                <i class="fas fa-plus"></i> Nouvelle catégorie
            </button>
            <form method="post" id="newHandleForm" style="display: none;">
                <div class="input-group">
                    <input class="form-control" type="text" name="newHandleInputName" placeholder="Nom de la catégorie">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit" id="newHandleBtnSubmit" title="enregistrer">
                            <i class="fas fa-check"></i></button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

