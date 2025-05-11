const WEB_GLUECARD_URL = WEB_APP_URL + 'plugin/glueCard/';
const WEB_GLUECARD_PROCESS_URL = WEB_GLUECARD_URL + 'process/ajax.php';

function loadHandles(idHandle = 0, loadCardsToo = false) {
    $('#handlesContainer').html(loaderHtml()).load(WEB_GLUECARD_URL + 'page/handles.php?idHandle=' + idHandle, function () {
        if (loadCardsToo) {
            loadCards((idHandle > 0 ? idHandle : getActiveHandle()));
        }
    });
}

function loadCards(idHandle) {
    $('.cardsContainer[data-handle-id="'+idHandle+'"]').html(loaderHtml()).load(WEB_GLUECARD_URL + 'page/cards.php?idHandle=' + idHandle, function () {
        appoEditor();
        getMediaLibrairy();
    }).fadeIn();
}

function closeCards(idHandle){
    $('.cardsContainer[data-handle-id="'+idHandle+'"]').fadeOut();
}
function getActiveHandle() {
    return $('.collapse.show').data('handle-id');
}

function removeHandleAction(planToo = false) {
    $('button.handleBtn').attr('data-toggle', false);
    $('button#newHandle').remove();
    if (planToo) {
        $('span.updatePlan, span.removePlan, li.newPlan, span.archiveHandle, span.updateHandle, span.unpackHandle').remove();
    }
}

function getMediaLibrairy() {
    if ($('input.urlFile').length) {
        $libraryContainer.load(WEB_APP_URL + 'lib/assets/mediaLibrary.php');
    }
}

jQuery(document).ready(function ($) {

    loadHandles(0, true);

    $(document).on('show.bs.collapse', '#handlesCollapses', function (e) {
        let newHandleId = $(e.target).data('handle-id');
        if ($('#handlesCollapses').attr('data-current-id-handler') != newHandleId) {
            $('#handlesCollapses').attr('data-current-id-handler', newHandleId);
            loadCards(newHandleId);
        }
    });

    /** HANDLE */

    //Show Add handle form
    $(document.body).on('click', '#newHandle', function (e) {
        e.preventDefault();
        let $btn = $(this);
        $btn.fadeOut(300, function () {
            $('#newHandleForm').fadeIn(300, function () {
                $('input[name="newHandleInputName"]').focus();
            });
        });
    });

    //Update handle name button click
    $(document.body).on('click', '.updateHandle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let $span = $(this);
        let $btn = $span.closest('button');
        $btn.find('span').fadeOut().remove();
        let $input = $btn.find('input[name="handleNameInput"]');
        $input.addClass('border-bottom border-white').removeAttr('readonly').css('cursor', 'text').focus();
        $btn.append('<span class="btn btn-sm float-right saveNameHandle" title="Enregistrer la catégorie"><i class="fas fa-check"></i></span>');
        removeHandleAction();
    });

    //Update handle name
    $(document.body).on('click', 'span.saveNameHandle', function (e) {
        e.preventDefault();

        let $span = $(this);
        let $btn = $span.closest('button');
        let handleId = $btn.data('handle-id');
        let handleName = $btn.find('input[name="handleNameInput"]').val();
        if (handleName) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {
                updateHandleId: handleId,
                updateHandleName: handleName
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadHandles(handleId, true);
                } else {
                    notification('Un problème est survenu lors de la mise à jour de la catégorie', 'danger');
                }
                availableApp();
            });
        }
    });

    //Archive handle name
    $(document.body).on('click', 'span.archiveHandle', function (e) {
        e.preventDefault();

        if (confirm('Vous êtes sur le point d\'archiver la catégorie')) {

            let $span = $(this);
            let $btn = $span.closest('button');
            let handleId = $btn.data('handle-id');
            if (handleId) {

                busyApp();
                $.post(WEB_GLUECARD_PROCESS_URL, {archiveHandleId: handleId}).done(function (data) {
                    if (data === 'true' || data === true) {
                        loadHandles(0, true);

                    } else {
                        notification('Un problème est survenu lors de l\'archivage de la catégorie', 'danger');
                    }
                    availableApp();
                });
            }
        }
    });

    //Delete handle
    $(document.body).on('click', 'span.deleteHandle', function (e) {
        e.preventDefault();

        if (confirm('Vous êtes sur le point de supprimer définitivement la catégorie')) {

            let $span = $(this);
            let $btn = $span.closest('button');
            let handleId = $btn.data('handle-id');
            if (handleId) {

                busyApp();
                $.post(WEB_GLUECARD_PROCESS_URL, {deleteHandleId: handleId}).done(function (data) {
                    if (data === 'true' || data === true) {
                        loadHandles(0, true);

                    } else {
                        notification('Un problème est survenu lors de la suppression de la catégorie', 'danger');
                    }
                    availableApp();
                });
            }
        }
    });

    //Unpack handle name
    $(document.body).on('click', 'span.unpackHandle', function (e) {
        e.preventDefault();

        if (confirm('Vous êtes sur le point de désarchiver la catégorie')) {

            let $span = $(this);
            let $btn = $span.closest('button');
            let handleId = $btn.data('handle-id');
            if (handleId) {

                busyApp();
                $.post(WEB_GLUECARD_PROCESS_URL, {unpackHandleId: handleId}).done(function (data) {
                    if (data === 'true' || data === true) {
                        loadHandles(handleId, true);
                    } else {
                        notification('Un problème est survenu lors du désarchivage de la catégorie', 'danger');
                    }
                    availableApp();
                });
            }
        }
    });

    //Add new handle
    $(document.body).on('submit', 'form#newHandleForm', function (e) {
        e.preventDefault();

        let newHandleName = $('input[name="newHandleInputName"]', $(this)).val();
        if (newHandleName) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {newHandleName: newHandleName}).done(function (newIdHandle) {
                if ($.isNumeric(newIdHandle)) {
                    loadHandles(newIdHandle, true);
                } else {
                    notification('Un problème est survenu lors de l\'enregistrement de la nouvelle catégorie', 'danger');
                }
                availableApp();
            });
        }
    });

    /** PLAN */

    //Show Add plan form
    $(document.body).on('click', '.newPlan', function (e) {
        e.preventDefault();
        let $btn = $(this);
        let idHandle = $btn.data('handle-id');
        $btn.fadeOut().remove();

        let $form = $('form.newPlanForm[data-handle-id="'+idHandle+'"]');
        $form.fadeIn();
        $form.find('input[name="newPlanInputName"]').focus();
    });

    $(document.body).on('change', '.selectPlan', function (e){
        let $select = $(this);
        let smallInfo = $select.next('small');
        let option = $select.find(':selected');
        if(option.is('option')){
            smallInfo.html(option.data('description'));
        }
    });

    //New Plan
    $(document.body).on('submit', 'form.newPlanForm', function (e) {
        e.preventDefault();
        let $form = $(this);
        let newPlanHandleId = $('input[name="newPlanHandleId"]', $form).val();
        let newPlanName = $('input[name="newPlanInputName"]', $form).val();
        let newPlanType = $('select[name="newPlanInputType"]', $form).val();

        if (newPlanHandleId && newPlanName && newPlanType) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL,
                {
                    newPlanHandleId: newPlanHandleId,
                    newPlanName: newPlanName,
                    newPlanType: newPlanType,
                }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadHandles(newPlanHandleId, true);
                } else {
                    notification('Un problème est survenu lors de l\'enregistrement du nouveau plan', 'danger');
                }
                availableApp();
            });
        }
    });

    //Update plan button click
    $(document.body).on('click', '.updatePlan', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let $span = $(this);
        let $btn = $span.closest('li');

        $span.removeClass('updatePlan').addClass('saveUpdatedPlan')
            .attr('title', 'Enregistrer les changements').find('i')
            .removeClass('fa-pencil-alt').addClass('fa-check');
        $btn.addClass('list-group-item-info');

        let $input = $btn.find('input[name="planNameInput"]');
        $input.addClass('border-bottom border-dark').removeAttr('readonly').focus();

        let $parent = $btn.closest('ul.list-group');
        $parent.find('li.list-group-item').not($btn).addClass('list-group-item-light');

        removeHandleAction(true)
    });

    //Update plan name
    $(document.body).on('click', 'span.saveUpdatedPlan', function (e) {
        e.preventDefault();

        let $span = $(this);
        let $parent = $span.closest('li');
        let $input = $parent.find('input[name="planNameInput"]');
        let newPlanName = $input.val();
        let handleId = $parent.data('handle-id');
        let planId = $parent.data('plan-id');
        if (newPlanName && planId) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {
                updatePlanId: planId,
                updatePlanName: newPlanName
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadHandles(handleId, true);
                } else {
                    notification('Un problème est survenu lors de la mise à jour du plan', 'danger');
                }
                availableApp();
            });
        }
    });

    //Remove plan
    $(document.body).on('click', 'span.removePlan', function (e) {
        e.preventDefault();

        let $span = $(this);
        let $parent = $span.closest('li');
        let handleId = $parent.data('handle-id');
        let planId = $parent.data('plan-id');
        if (planId && handleId && confirm('Vous êtes sur le point de supprimer un plan ainsi que toutes les données associées')) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {
                removePlanId: planId
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadHandles(handleId, true);
                } else {
                    notification('Un problème est survenu lors de la mise à jour du plan', 'danger');
                }
                availableApp();
            });
        }
    });

    /** ITEM */

    //Add item
    $(document.body).on('click', 'div#newItemBtnSubmit', function (e) {
        e.preventDefault();
        let $div = $(this);
        let newItemHandleId = $div.attr('data-id-handle');

        if (newItemHandleId) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL,
                {newItemHandleId: newItemHandleId}).done(function (data) {
                if (data === 'true' || data === true) {
                    loadCards(newItemHandleId);
                } else {
                    notification('Un problème est survenu lors de l\'enregistrement de la nouvelle carte', 'danger');
                }
                availableApp();
            });
        }
    });

    //Archive item
    $(document.body).on('click', 'span.archiveItem', function (e) {
        e.preventDefault();

        let $span = $(this);
        let $parent = $span.closest('.card');
        let itemId = $parent.data('item-id');
        let handleId = $parent.data('handle-id');
        if (itemId && confirm('Vous êtes sur le point d\'archiver cette carte')) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {
                archiveItemId: itemId
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadCards(handleId);
                } else {
                    notification('Un problème est survenu lors de l\'archivage de la carte', 'danger');
                }
                availableApp();
            });
        }
    });

    //Delete item
    $(document.body).on('click', 'span.deleteItem', function (e) {
        e.preventDefault();

        let $span = $(this);
        let $parent = $span.closest('.card');
        let itemId = $parent.data('item-id');
        let handleId = $parent.data('handle-id');
        if (itemId && confirm('Vous êtes sur le point de supprimer définitivement cette carte')) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {
                deleteItemId: itemId
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadCards(handleId);
                } else {
                    notification('Un problème est survenu lors de la suppression de la carte', 'danger');
                }
                availableApp();
            });
        }
    });

    //Unpack item
    $(document.body).on('click', 'span.unpackItem', function (e) {
        e.preventDefault();

        let $span = $(this);
        let $parent = $span.closest('.card');
        let itemId = $parent.data('item-id');
        let handleId = $parent.data('handle-id');
        if (itemId && confirm('Vous êtes sur le point de désarchiver cette carte')) {

            busyApp();
            $.post(WEB_GLUECARD_PROCESS_URL, {
                unpackItemId: itemId
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    loadCards(handleId);
                } else {
                    notification('Un problème est survenu lors du désarchivage de la carte', 'danger');
                }
                availableApp();
            });
        }
    });

    //Unpack item
    $(document.body).on('click', '.increaseOrder, .lowerOrder', function (e) {
        e.preventDefault();

        let itemOrder = 0;
        let $btn = $(this);
        let $parent = $btn.closest('.card');
        let $container = $btn.closest('.itemPositionContainer');
        let itemOrderContainer = $container.find('.itemOrder');
        itemOrderContainer.removeClass('text-dark');

        let itemId = $parent.data('item-id');
        let handleId = $parent.data('handle-id');
        let countItems = parseInt(itemOrderContainer.data('count-items'));
        itemOrder = parseInt(itemOrderContainer.text());

        if (itemOrder > 0 && countItems && itemId && handleId) {
            busyApp();

            if ($btn.hasClass('increaseOrder')) {
                if ((itemOrder + 1) <= countItems) {
                    itemOrder += 1;
                    itemOrderContainer.text(itemOrder);
                }
            } else {
                if ((itemOrder - 1) >= 1) {
                    itemOrder -= 1;
                    itemOrderContainer.text(itemOrder);
                }
            }

            delay(function () {
                $.post(WEB_GLUECARD_PROCESS_URL,
                    {
                        newItemOrder: itemOrder,
                        itemId: itemId
                    }).done(function (data) {
                    if (data === 'true' || data === true) {
                        loadCards(handleId);
                    } else {
                        notification('Un problème est survenu lors de l\'enregistrement', 'danger');
                    }
                    availableApp();
                });
            }, 1200);
        }
    });

    /** CONTENT */

    $(document.body).on('input change', 'div.inlineAppoeditor', function () {
        let textarea = $('textarea[data-editor-id="' + $(this).data('editor-id') + '"]');

        if (getViewMode($(this)) === 'viewMode') {
            textarea.val($(this).html());
            textarea.trigger('input');
        }
    });

    $(document.body).on('input', 'input.ajaxContentInput, textarea.ajaxContentInput', function (e) {
        e.preventDefault();
        busyApp();

        let $input = $(this);
        let contentHandleId = $input.data('handle-id');
        let contentPlanId = $input.data('plan-id');
        let contentItemId = $input.data('item-id');
        let contentText = $input.val();

        $('.ajaxContentInput.successInput').removeClass('successInput');

        if (contentHandleId && contentPlanId && contentItemId) {

            delay(function () {
                $.post(WEB_GLUECARD_PROCESS_URL,
                    {
                        contentHandleId: contentHandleId,
                        contentPlanId: contentPlanId,
                        contentItemId: contentItemId,
                        contentText: contentText
                    }).done(function (data) {
                    if (data === 'true' || data === true) {
                        $input.addClass('successInput');
                    } else {
                        notification('Un problème est survenu lors de l\'enregistrement', 'danger');
                    }
                    availableApp();
                });
            }, 1000);
        }
    });

    /** AJAX COMPLETE */

    $(document).ajaxComplete(function () {

        //image popover on input
        $('input.urlFile').filter(function () {
            return this.value.length !== 0;
        }).popover({
            container: 'body',
            html: true,
            trigger: 'hover',
            delay: 200,
            placement: 'top',
            content: function () {
                return '<img src="' + $(this).val() + '" />';
            }
        });
    });
});

jQuery(window).on('load', function () {
    getMediaLibrairy();
});