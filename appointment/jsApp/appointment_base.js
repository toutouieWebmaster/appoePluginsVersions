const WEB_APPOINTMENT_URL = WEB_APP_URL + 'plugin/appointment/';
const WEB_APPOINTMENT_AJAX_URL = WEB_APPOINTMENT_URL + 'ajax/';

function getAdminAgendas() {
    appointment_getLoader();
    appointment_ajax({getAdminAgendas: 'OK'}).done(function (data) {
        if (data) {
            $('#addAgendaModal').modal('hide');
            setTimeout(function () {
                $('div#agendas').html(data);
                popoverUrlFile();
            }, 500);
        }
        appointment_removeLoader();
    });
}

function getAdminRdvAvailabilities(date, idRdvType) {
    if (date && idRdvType) {
        appointment_getLoader();
        appointment_ajax({
            getRdvAvailabilities: 'OK',
            date: date,
            idRdvType: idRdvType,
        }).done(function (data) {
            if (data) {
                $('#rdvAvailabilities').html(data);
            }
            appointment_removeLoader();
        });
    }
}

function getAdminListManage(idAgenda, list) {
    return appointment_ajax({getManageList: list, idAgenda: idAgenda});
}

jQuery(window).on('load', function () {

    if ($('div#agendas').length) {
        getAdminAgendas();

        $(document.body).on('submit', 'form#addAgendaForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            let agendaName = $form.find('input#agendaName').val();
            if (agendaName && agendaName !== '') {

                busyApp();
                appointment_ajax({setAdminAgendas: 'OK', agendaName: agendaName}).done(function (data) {
                    if (data === 'true') {
						notification('Agenda ajouté');
                        $form.trigger('reset');
                        getAdminAgendas();
                    }
                    availableApp();
                });
            }

            getAdminAgendas();
        });

        $(document.body).on('click', 'button.deleteAgenda', function () {
            let $btn = $(this);
            let $parent = $btn.closest('div.agendaInfos');
            let idAgenda = $parent.attr('data-id-agenda');

            if (confirm('Voulez-vous vraiment supprimer cet agenda ?')) {
                busyApp();
                appointment_ajax({deleteAdminAgendas: 'OK', idAgenda: idAgenda}).done(function (data) {
                    if (data === 'true') {
                        $parent.fadeOut(500, function () {
                            $parent.remove();
							notification('Agenda supprimé');
                        });
                        //getAdminAgendas();
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('input', 'input[id^=agendaName-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idAgenda = $parent.attr('data-id-agenda');
            let agendaName = $input.val();
            busyApp();
            delay(function () {
                appointment_ajax({changeNameAdminAgendas: 'OK', idAgenda: idAgenda, agendaName: agendaName})
                    .done(function (data) {
                        if (data === 'true') {
                            notification('Le nom de l\'agenda a été mis à jour');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
            }, 2000);
        });

        $(document.body).on('change', 'input[id^=agendaStatus-]', function () {
            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idAgenda = $parent.attr('data-id-agenda');

            appointment_ajax({changeStatusAdminAgendas: 'OK', idAgenda: idAgenda}).done(function (data) {
                if (data === 'true') {
                    notification('Statut mis à jour');
                } else {
                    notification('Erreur', 'danger');
                }
            });
        });

        $(document.body).on('input', 'input.agendaSettingInput, select.agendaSettingInput', function (e) {
            e.preventDefault();
            let $input = $(this);
            busyApp();
            delay(function () {
                appointment_ajax({
                    adminAgendaSetting: 'OK',
                    key: $input.attr('name'),
                    val: $input.val()
                }).done(function (data) {
                    if (data === 'true') {
                        notification('Paramètre enregistré');
                        if ($input.hasClass('urlFile')) {
                            popoverUrlFile();
                        }
                    } else {
                        notification('Erreur', 'danger');
                    }
                    availableApp();
                });
            }, 1000);
        });
    }

    if ($('div#manageList').length) {

        $(document.body).on('click', 'button.btnAgendaManager', function () {
            let $btn = $(this);
            let manageList = $btn.attr('data-manage');
            let $parent = $btn.closest('div#manageList');
            let idAgenda = $parent.attr('data-id-agenda');

            if ($('#manageType').attr('data-current-type') !== manageList) {

                $('button.btnAgendaManager').removeClass('active');
                $btn.addClass('active');

                appointment_getLoader()
                getAdminListManage(idAgenda, manageList).done(function (data) {
                    if (data) {
                        $('#manageType').attr('data-current-type', manageList).html(data);
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('change', 'input#holidayWorking', function () {
            let $input = $(this);
            let idAgenda = $input.data('id-agenda');

            appointment_ajax({adminAgendaWorkingHoliday: 'OK', idAgenda: idAgenda}).done(function (data) {
                if (data === 'true') {
                    notification('Préférence enregistrée');
                } else {
                    notification('Erreur', 'danger');
                }
            });
        });
        $(document.body).on('submit', 'form#addAvailability', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.attr('data-id-agenda');
            let day = $form.find('select#day').val();
            let start = $form.find('select#start').val();
            let end = $form.find('select#end').val();

            if (idAgenda && $.isNumeric(day) && $.isNumeric(start) && $.isNumeric(end)) {

                busyApp();
                appointment_ajax({
                    setAdminAvailability: 'OK',
                    idAgenda: idAgenda,
                    day: day,
                    start: start,
                    end: end
                }).done(function (data) {
                    if (data === 'true') {
                        getAdminListManage(idAgenda, 'availabilities').done(function (data) {
                            if (data) {
                                $('#addAvailabilityModal').modal('hide');
                                setTimeout(function () {
                                    $('div#manageType').html(data);
                                }, 500);
                            }
                        });
                    } else if (data === 'false') {
                        notification('Certaines données sont manquantes au créneau', 'danger');
                    } else {
                        notification(data, 'danger');
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('click', 'button.deleteAvailability', function () {
            let $btn = $(this);
            let $parent = $btn.closest('div.agendaInfos');
            let idAvailability = $btn.attr('data-id-availability');

            if (confirm('Voulez-vous vraiment supprimer cette disponibilité ?')) {
                busyApp();
                appointment_ajax({deleteAdminAvailability: 'OK', idAvailability: idAvailability}).done(function (data) {
                    if (data === 'true') {
                        $btn.parent('li').fadeOut(500, function () {
                            $btn.parent('li').remove();
							notification('Disponibilité supprimée');
                        });
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('click', 'button.deleteRdvTypeForm', function () {
            let $btn = $(this);
            let $parent = $btn.closest('form.rdvTypForm');
            let idRdvTypeForm = $parent.attr('data-id-rdv-type-form');

            if (confirm('Voulez-vous vraiment supprimer ce champ ?')) {
                appointment_ajax({deleteAdminARdvTypeForm: 'OK', idRdvTypeForm: idRdvTypeForm}).done(function (data) {
                    if (data === 'true') {
                        $parent.fadeOut(500, function () {
                            $parent.remove();
							notification('Formulaire de type de rendez-vous supprimé');
                        });
                    }
                });
            }
        });


        $(document.body).on('submit', 'form#addTypeRdv', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.attr('data-id-agenda');
            let rdvTypeName = $form.find('input#name').val();
            let rdvTypeDuration = $form.find('select#duration').val();
            let rdvTypeInfo = $form.find('textarea#information').val();
            if (idAgenda && rdvTypeName !== '' && rdvTypeDuration !== '') {

                busyApp(false);
                appointment_ajax({
                    adminAddRdvType: 'OK',
                    idAgenda: idAgenda,
                    name: rdvTypeName,
                    duration: rdvTypeDuration,
                    information: rdvTypeInfo
                }).done(function (data) {
                    if (data === 'true') {
                        getAdminListManage(idAgenda, 'typeRdv').done(function (data) {
                            if (data) {
                                $('#addRdvTypeModal').modal('hide');
                                setTimeout(function () {
                                    $('#manageType').html(data);
									notification('Type de rendez-vous ajouté');
                                }, 500);

                            }
                        });
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('submit', 'form#addInfoForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.data('id-agenda');
            let metaKey = $form.find('input#metaKey').val();
            let metaVal = $form.find('input#metaVal').val();
            let position = $form.find('select#position').val();

            if (idAgenda && metaKey !== '' && metaVal !== '' && position !== '') {

                busyApp(false);
                appointment_getLoader();
                appointment_ajax({
                    adminAddAgendaMeta: 'OK',
                    idAgenda: idAgenda,
                    metaKey: metaKey,
                    metaVal: metaVal,
                    position: position
                }).done(function (data) {
                    if (data === 'true') {
                        appointment_ajax({getManageList: 'preferences', idAgenda: idAgenda}).done(function (data) {
                            if (data) {
                                $('#addInfoModal').modal('hide');
                                setTimeout(function () {
                                    $('div#manageType').html(data);
                                }, 500);
                            }
                        });
						notification('Information complémentaire ajoutée');
                    }
                    appointment_removeLoader();
                    availableApp();
                });
            }
        });

        $(document.body).on('submit', 'form#addTypeRdvForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.data('id-agenda');
            let idRdvType = $form.data('id-rdv-type');
            let name = $form.find('input#name').val();
            let type = $form.find('select#type').val();
            let placeholder = $form.find('input#placeholder').val();
            let required = $form.find('input#required').is(':checked');
            let position = $form.find('select#position').val();

            if (idAgenda && idRdvType && name !== '' && type !== '' && required !== '' && position !== '') {

                busyApp(false);
                appointment_ajax({
                    adminAddRdvTypeForm: 'OK',
                    idAgenda: idAgenda,
                    idRdvType: idRdvType,
                    name: name,
                    type: type,
                    placeholder: placeholder,
                    required: required,
                    position: position,
                }).done(function (data) {
                    if (data === 'true') {
                        appointment_ajax({getManageList: 'typeRdvForm', idAgenda: idAgenda, idRdvType: idRdvType})
                            .done(function (data) {
                                if (data) {
                                    $('#rdvTypeFormContent').html(data);
									notification('Formulaire de type de rendez-vous ajouté');
                                }
                            });
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('input', 'input[id^=rdvTypeForm], select[id^=rdvTypeForm]', function (e) {
            e.preventDefault();

            let $form = $(this).closest('form.rdvTypForm');
            let idRdvTypeForm = $form.attr('data-id-rdv-type-form');
            let name = $form.find('input#rdvTypeFormName-' + idRdvTypeForm).val();
            let type = $form.find('select#rdvTypeFormType-' + idRdvTypeForm).val();
            let placeholder = $form.find('input#rdvTypeFormPlaceholder-' + idRdvTypeForm).val();
            let required = $form.find('input#rdvTypeFormRequired-' + idRdvTypeForm).is(':checked');
            let position = $form.find('select#rdvTypeFormPosition-' + idRdvTypeForm).val();

            if (idRdvTypeForm && name !== '' && type !== '' && required !== '' && position !== '') {
                busyApp();
                delay(function () {
                    appointment_ajax({
                        adminUpdateRdvTypeForm: 'OK',
                        idRdvTypeForm: idRdvTypeForm,
                        name: name,
                        type: type,
                        placeholder: placeholder,
                        required: required,
                        position: position
                    }).done(function (data) {
                        if (data === 'true') {
                            notification('Champ personnalisé enregistré');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
                }, 2000);
            }
        });

        $(document.body).on('click', 'button.deleteRdvType', function () {
            let $btn = $(this);
            let $parent = $btn.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');

            if (confirm('Voulez-vous vraiment supprimer ce type de rendez-vous ?')) {
                appointment_ajax({deleteAdminRdvType: 'OK', idRdvType: idRdvType}).done(function (data) {
                    if (data == 'true') {
                        $parent.fadeOut(500, function () {
                            $parent.remove();
							notification('Type de rendez-vous supprimé', 'danger');
                        });
                    }
                });
            }
        });

        $(document.body).on('click', 'button.deleteMeta', function () {
            let $btn = $(this);
            let idMeta = $btn.attr('data-id-meta');
            let $parent = $btn.closest('div.agendaInfos');

            if (idMeta && confirm('Voulez-vous vraiment supprimer cette information ?')) {
                appointment_ajax({deleteAdminAgendaMeta: 'OK', idMeta: idMeta}).done(function (data) {
                    if (data == 'true') {
                        $parent.fadeOut(500, function () {
                            $parent.remove();
                            notification('Information supprimée', 'danger');
                        });
                    }
                });
            }
        });

        $(document.body).on('click', 'button.deleteRdv', function () {
            let $btn = $(this);
            let idRdv = $btn.attr('data-id-rdv');
            let $parent = $btn.closest('li');
            let $parentContainer = $btn.closest('div#rdvList');
            let idRdvType = $parentContainer.attr('data-id-rdv-type');
            let date = $parentContainer.attr('data-date');

            if (confirm('Voulez-vous vraiment annuler ce rendez-vous ?')) {

                busyApp();
                appointment_ajax({deleteAdminRdv: 'OK', idRdv: idRdv}).done(function (data) {
                    if (data === 'true') {
                        $parent.fadeOut(500, function () {
                            notification('Rendez-vous annulé');
                            getAdminRdvAvailabilities(date, idRdvType);
                        });
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('click', 'button.confirmRdv', function () {
            let $btn = $(this);
            let idRdv = $btn.attr('data-id-rdv');
            let $parent = $btn.closest('li');
            let $parentContainer = $btn.closest('div#rdvList');
            let idRdvType = $parentContainer.attr('data-id-rdv-type');
            let date = $parentContainer.attr('data-date');

            appointment_ajax({confirmAdminRdv: 'OK', idRdv: idRdv}).done(function (data) {
                if (data === 'true') {
                    $parent.fadeOut(500, function () {
                        notification('Rendez-vous confirmé');
                        getAdminRdvAvailabilities(date, idRdvType);
                    });
                }
            });
        });

        $(document.body).on('click', 'button.confirmClient', function () {
            let $btn = $(this);
            let idClient = $btn.attr('data-id-client');

            appointment_ajax({confirmAdminClient: 'OK', idClient: idClient}).done(function (data) {
                if (data === 'true') {
                    $btn.fadeOut(500, function () {
                        notification('Client confirmé');
                    });
                }
            });
        });

        $(document.body).on('click', 'button.addNewRdv', function () {
            let $btn = $(this);
            let start = $btn.attr('data-start');
            let end = $btn.attr('data-end');
            let modal = $('div#addNewRdvForm');
            let dateRemind = modal.find('div#addNewRdvFormDate').data('date-reminder');
            let dateSlice = dateRemind.split(' ');
            modal.find('div#addNewRdvFormDate').html('<div class="dateTitleForm">' + dateSlice[0] + '</div><div>' + dateSlice[1] + ' ' + dateSlice[2] + ' ' + dateSlice[3] + '</div>');
            modal.find('select#rdvBegin').val(start);
            modal.find('select#rdvEnd').val(end);
        });

        $(document.body).on('change', 'div#addNewRdvForm select#selectClient', function () {
            let select = $(this);
            let selectedIdClient = select.val();
            let $form = $('form#addNewRdvClientForm');
            let idAgenda = $form.find('input[name="idAgenda"]').val();
            let idRdvType = $form.find('input[name="idRdvType"]').val();
            let rdvDate = $form.find('input[name="rdvDate"]').val();
            let rdvBegin = $form.find('select#rdvBegin').val();
            let rdvEnd = $form.find('select#rdvEnd').val();

            $form.trigger('reset');
            $form.find('input[name="idAgenda"]').val(idAgenda);
            $form.find('input[name="idRdvType"]').val(idRdvType);
            $form.find('input[name="rdvDate"]').val(rdvDate);
            $form.find('select#rdvBegin').val(rdvBegin);
            $form.find('select#rdvEnd').val(rdvEnd);
            $form.find('select#selectClient').val(selectedIdClient);

            if (selectedIdClient > 0) {
                appointment_getLoader();
                appointment_ajax({
                    getClientData: 'OK',
                    idClient: selectedIdClient,
                }).done(function (data) {
                    if (data) {
                        data = JSON.parse(data);
                        $.each(data, function (i, val) {
                            if ($('#' + i, $form).length) {
                                $('#' + i, $form).val(val);
                            }
                        });
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('submit', 'form#addNewRdvClientForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            $form.prepend('<input name="appointmentFromAdmin" type="hidden" value="OK">');

            postFormRequest($form, function () {
                $('#addNewRdvForm').modal('hide');
                setTimeout(function () {
                    let $box = $('table#calendar td.selectedDay');
                    if (!$box.find('span.shapeRdv').length) {
                        $box.append('<span class="shapeRdv"></span>');
                    }
                    notification('Le rendez-vous a été enregistré');
                    getAdminRdvAvailabilities($form.find('input[name="rdvDate"]').val(), $form.find('input[name="idRdvType"]').val());
                }, 300);
            });
        });

        $(document.body).on('click', 'button.MakeTheTimeSlotUnavailable', function () {
            let $btn = $(this);
            let start = $btn.attr('data-start');
            let end = $btn.attr('data-end');
            let $parentContainer = $btn.closest('div#rdvList');
            let idAgenda = $parentContainer.attr('data-id-agenda');
            let idRdvType = $parentContainer.attr('data-id-rdv-type');
            let date = $parentContainer.attr('data-date');

            appointment_ajax({
                makeTheTimeSlotUnavailable: 'OK',
                idAgenda: idAgenda,
                date: date,
                start: start,
                end: end
            }).done(function (data) {
                if (data === 'true') {
                    $btn.fadeOut(500, function () {
                        notification('Le créneau est enregistré comme indisponible');
                        getAdminRdvAvailabilities(date, idRdvType);
                    });
                }
            });
        });

        $(document.body).on('click', 'button.makeTheDayUnavailable', function () {
            let $btn = $(this);
            let start = 0;
            let end = 1440;
            let $parentContainer = $btn.closest('div#rdvList');
            let idAgenda = $parentContainer.attr('data-id-agenda');
            let idRdvType = $parentContainer.attr('data-id-rdv-type');
            let date = $parentContainer.attr('data-date');

            appointment_ajax({
                makeTheTimeSlotUnavailable: 'OK',
                idAgenda: idAgenda,
                date: date,
                start: start,
                end: end
            }).done(function (data) {
                if (data === 'true') {
                    $btn.fadeOut(500, function () {
                        $('table#calendar td.day[data-date="' + date + '"]').addClass('disabledDay');
                        notification('La journée est enregistrée comme indisponible');
                        getAdminRdvAvailabilities(date, idRdvType);
                    });
                }
            });
        });

        $(document.body).on('click', 'button.makeTheDayAvailable', function () {
            let $btn = $(this);
            let $parentContainer = $btn.closest('div#rdvList');
            let idAgenda = $parentContainer.attr('data-id-agenda');
            let idRdvType = $parentContainer.attr('data-id-rdv-type');
            let date = $parentContainer.attr('data-date');

            appointment_ajax({
                makeTheDayAvailable: 'OK',
                idAgenda: idAgenda,
                date: date,
            }).done(function (data) {
                if (data === 'true') {
                    $btn.fadeOut(500, function () {
                        $('table#calendar td.day[data-date="' + date + '"]').removeClass('disabledDay');
                        notification('La journée est enregistrée comme disponible');
                        getAdminRdvAvailabilities(date, idRdvType);
                    });
                }
            });
        });

        $(document.body).on('click', 'button.MakeTheTimeSlotAvailable', function () {
            let $btn = $(this);
            let idsException = $btn.attr('data-ids-exception');
            let $parentContainer = $btn.closest('div#rdvList');
            let idRdvType = $parentContainer.attr('data-id-rdv-type');
            let date = $parentContainer.attr('data-date');

            appointment_ajax({
                makeTheTimeSlotAvailable: 'OK',
                idsException: idsException
            }).done(function (data) {
                if (data === 'true') {
                    $btn.fadeOut(500, function () {
                        notification('Le créneau est enregistré comme disponible');
                        getAdminRdvAvailabilities(date, idRdvType);
                    });
                }
            });
        });

        $(document.body).on('input', 'input[id^=rdvTypeName-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');
            let rdvTypeName = $input.val();
            busyApp();
            delay(function () {
                appointment_ajax({changeNameAdminRdvType: 'OK', idRdvType: idRdvType, rdvTypeName: rdvTypeName})
                    .done(function (data) {
                        if (data === 'true') {
                            notification('Nom mis à jour');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
            }, 1000);
        });

        $(document.body).on('input', 'select[id^=rdvTypeDuration-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');
            let rdvTypeDuration = $input.val();

            appointment_ajax({changeDurationAdminRdvType: 'OK', idRdvType: idRdvType, duration: rdvTypeDuration})
                .done(function (data) {
                    if (data === 'true') {
                        notification('Durée mise à jour');
                    } else {
                        notification('Erreur', 'danger');
                    }
                });
        });

        $(document.body).on('input', 'textarea[id^=information-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');
            let information = $input.val();
            busyApp();
            delay(function () {
                appointment_ajax({changeInformationAdminRdvType: 'OK', idRdvType: idRdvType, information: information})
                    .done(function (data) {
                        if (data === 'true') {
                            notification('Information mise à jour');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
            }, 4000);
        });

        $(document.body).on('change', 'input[id^=rdvTypeStatus-]', function () {
            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');

            appointment_ajax({changeStatusAdminRdvType: 'OK', idRdvType: idRdvType}).done(function (data) {
                if (data === 'true') {
                    notification('Statut mis à jour');
                } else {
                    notification('Erreur', 'danger');
                }
            });
        });

        $(document.body).on('change', 'select#rdvTypes, select#rdvYear, select#rdvMonth', function () {
            let $select = $(this);
            let $parent = $select.closest('div#getRdvGrid');
            let idRdvType = $parent.find('select#rdvTypes').val();
            let year = $parent.find('select#rdvYear').val();
            let month = $parent.find('select#rdvMonth').val();

            if (idRdvType && year && month) {
                appointment_getLoader();
                appointment_ajax({
                    getRdvGrid: 'OK',
                    idRdvType: idRdvType,
                    year: year,
                    month: month
                }).done(function (data) {
                    if (data) {
                        $('#rdvCalendar').html(data);
                    }
                    appointment_removeLoader();
                    $('td.day.currentDay').trigger('click');
                });
            }
        });


        $(document.body).on('click', 'table#calendar td.day:not(".other-month")', function () {
            let $el = $(this);

            $('table#calendar td.day').removeClass('selectedDay');
            $el.addClass('selectedDay');

            let date = $el.data('date');
            let idRdvType = $el.closest('table#calendar').data('id-rdv-type');

            if (!$('div#rdvList').length || date !== $('div#rdvList').data('date')) {
                getAdminRdvAvailabilities(date, idRdvType);
            }
        });

        $(document.body).on('show.bs.modal', '#dedicatedForm', function (event) {
            let button = $(event.relatedTarget);
            let idRdvType = button.closest('div.agendaInfos').data('id-rdv-type');
            let idAgenda = button.closest('div.agendaInfos').data('id-agenda')
            let modal = $(this);

            appointment_ajax({
                getManageList: 'typeRdvForm',
                idAgenda: idAgenda,
                idRdvType: idRdvType
            }).done(function (data) {
                if (data) {
                    modal.find('.modal-body #rdvTypeFormContent').html(data);
                }
            });
        });

    }
});