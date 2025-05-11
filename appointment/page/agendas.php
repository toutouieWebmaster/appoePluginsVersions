<?php

use App\Form;

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/header_admin_template.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12 mb-2"><h5 class="agendaTitle">Mes agendas</h5>
            <button class="btn btn-sm mx-3 btn-outline-info"
                    data-toggle="modal" data-target="#addAgendaModal"><i class="fas fa-plus"></i></button>
        </div>
        <div class="col-12" id="agendas"></div>
    </div>
    <div class="modal fade" id="addAgendaModal" tabindex="-1" aria-labelledby="addAgendaTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
            <div class="modal-content rounded-0">
                <div class="modal-header py-0 border-0">
                    <h5 class="modal-title agendaTitle" id="addAgendaTitle">Ajouter un agenda</h5>
                    <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addAgendaForm">
                        <div class="form-row">
                            <div class="col-12 mb-2">
                                <?= Form::input('agendaName', ['title' => 'Nom de l\'agenda', 'required' => true]); ?>
                            </div>
                            <div class="col-12">
                                <?= Form::btn('Enregistrer', 'ADDAGENDASUBMIT'); ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/footer_admin_template.php'); ?>