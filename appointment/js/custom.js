(function () {
    if ('undefined' == typeof window.jQuery) {
        let jqueryScript = document.createElement("script");
        jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        jqueryScript.type = 'text/javascript';
        jqueryScript.onload = function () {
            let $ = window.jQuery;
        };
        document.getElementsByTagName("head")[0].appendChild(jqueryScript);
    }
})();

function appointment_ajax(params) {
    return jQuery.post(WEB_PLUGIN_URL + 'appointment/ajax/request.php', params);
}

function appointment_getLoader() {
    jQuery(document.body).append('<div class="appointmentLoader"><div></div><div></div><div></div><div></div></div>');
}

function appointment_removeLoader() {
    jQuery('div.appointmentLoader').fadeOut(300, function () {
        jQuery(this).remove();
    });
}

function minutesToHours(time, format = ':') {
    time = parseInt(time);

    if (time < 0 || time >= 1440) {
        return 0;
    }
    let hours, minutes;
    hours = Math.floor(time / 60);
    minutes = time % 60;

    if (hours < 10) {
        hours = '0' + hours;
    }
    if (minutes < 10) {
        minutes = '0' + minutes;
    }

    return hours + format + minutes;
}

function scrollSmooth(top) {
    jQuery('html, body').animate({scrollTop: top}, 500);
}

function owlCarouselInit() {

    let owl = jQuery('#appointmentSwipeCalendar');

    jQuery('div', owl).each(function (i, el) {
        jQuery(el).attr('data-position', i);
    });

    owl.owlCarousel({
        loop: false,
        dots: false,
        nav: false,
        center: false,
        slideBy: 7,
        margin: 10,
        autoWidth: true,
        rewind: false,
        rewindNav: false,
        responsiveClass: true,
        responsive: {
            0: {
                items: 3
            },
            600: {
                items: 4
            },
            900: {
                items: 5
            },
            1100: {
                items: 7
            }
        }
    });

    jQuery('#appointmentPrevWeek').click(function () {
        owl.trigger('prev.owl.carousel');
    });

    jQuery('#appointmentCurrentWeek').click(function () {
        owl.trigger('to.owl.carousel', [0, 400, true]);
    });

    jQuery('#appointmentNextWeek').click(function () {
        owl.trigger('next.owl.carousel');
    });

    owl.on('changed.owl.carousel', function (property) {
        let current = property.item.index;
        let currentDate = jQuery(property.target).find(".owl-item").eq(current).find('div.dayBox');
        let lastDate = jQuery(property.target).find(".owl-item").last().find('div.dayBox');
        //let prevDate = jQuery(property.target).find(".owl-item").eq(current-7).find('div.dayBox');
        let nextDate = jQuery(property.target).find(".owl-item").eq(current + 7).find('div.dayBox');
        //jQuery('#appointmentPrevWeekInfos').html((currentDate.data('position') != 0) ? prevDate.data('date-reminder') : '');

        if (currentDate.data('position') == 0) {
            jQuery('#appointmentPrevWeekInfos').fadeOut();
        } else {
            jQuery('#appointmentPrevWeekInfos').fadeIn();
        }

        let year = currentDate.data('date-choice').split('-')[0];
        //currentDate.data('date-reminder') + ' - ' + nextDate.data('date-reminder')
        let currentDay = currentDate.find('span.date').text();
        let nextWeek = nextDate.find('span.date').text() + ' ' + nextDate.find('span.month').text() + ' ' + year;

        jQuery('#appointmentNextWeekInfos').html(((lastDate.data('position') - 7) > currentDate.data('position')) ? currentDay + ' - ' + nextWeek : '');
    });
}

jQuery(window).on('load', function () {

    if ($('section#agendas.appointmentAppoe').length
        || $('section#agendaRdvType.appointmentAppoe').length
        || $('section#agendaDatesRdv.appointmentAppoe').length) {

        appointment_getLoader();
        let loadedTimer = setInterval(function () {
            if (jQuery.isFunction(jQuery.fn.owlCarousel)) {
                owlCarouselInit();
                clearInterval(loadedTimer);
                appointment_removeLoader();
            }

            if($('script[src*="owl.carousel.min.js"]').length === 0) {
                let owlScript = document.createElement("script");
                owlScript.src = '/app/plugin/appointment/assets/owl.carousel.min.js';
                owlScript.type = 'text/javascript';
                document.getElementsByTagName("head")[0].appendChild(owlScript);
            }

        }, 300);

        let idAgenda, idClient, idRdvType, rdvDateReminder, rdvDate, rdvDuration, rdvBegin, rdvEnd;

        $(document.body).on('click', 'button.agendaChoice', function () {

            let $btn = $(this);
            idAgenda = $btn.attr('data-id-agenda');

            if (!$btn.hasClass('activeAgendaBtn')) {
                appointment_getLoader();
                $('button.agendaChoice').removeClass('activeAgendaBtn');
                $('p#emptyAgenda').remove();
                $('section#agendaRdvType').remove();
                $('section#agendaDatesRdv').remove();
                $('section.agendaAvailabilities').remove();
                $('section#agendaForm').remove();

                let $parent = $btn.closest('section');
                $btn.addClass('activeAgendaBtn');

                if ($('div#appointment-appoe').length === 0) {
                    $($parent).wrap('<div id="appointment-appoe"></div>');
                }

                appointment_ajax({
                    getRdvTypeByAgenda: 'OK',
                    idAgenda: idAgenda
                }).done(function (data) {
                    if (data) {
                        $('div#appointment-appoe').append(data);
                        owlCarouselInit();
                        scrollSmooth($btn.closest('section').next().offset().top - 100);
                    } else {
                        $('div#appointment-appoe').append('<p id="emptyAgenda">Pas de rendez-vous disponibles</p>');
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('click', 'button.rdvTypeChoice', function () {

            let $btn = $(this);
            idAgenda = $btn.attr('data-id-agenda');
            idRdvType = $btn.attr('data-id-rdv-type');
            rdvDuration = $btn.attr('data-rdv-duration');

            if (!$btn.hasClass('activeAgendaBtn')) {
                appointment_getLoader();
                $('button.rdvTypeChoice').removeClass('activeAgendaBtn');
                $('p#emptyAgenda').remove();
                $('section.agendaAvailabilities').remove();
                $('section#agendaForm').remove();

                $btn.addClass('activeAgendaBtn');
                let $parent = $btn.closest('section');

                if ($('div#appointment-appoe').length === 0) {
                    $($parent).wrap('<div id="appointment-appoe"></div>');
                }

                //$('<section id="agendaDatesRdv"></section>').insertAfter($parent).html(getHtmlLoader() + ' Chargement');
                if ($('section#agendaDatesRdv').length > 0) {
                    let $daySection = $('section#agendaDatesRdv');
                    let $dayBoxes = $daySection.find('div.dayBox');
                    let $dayActive = $daySection.find('div.dayBox.activeDate')

                    $dayBoxes.not('.disabledDay').attr('data-id-rdv-type', idRdvType).attr('data-rdv-duration', rdvDuration);

                    if ($dayActive.length) {
                        $dayActive.removeClass('activeDate').trigger('click');
                        scrollSmooth($btn.closest('section').next().offset().top - 100);
                    }
                    appointment_removeLoader();
                    return;
                }

                appointment_ajax({
                    getDateByRdvType: 'OK',
                    idAgenda: idAgenda,
                    idRdvType: idRdvType
                }).done(function (data) {
                    if (data) {
                        $('div#appointment-appoe').append(data);
                        owlCarouselInit();
                        scrollSmooth($btn.closest('section').next().offset().top - 100);
                    } else {
                        $('div#appointment-appoe').append('<p id="emptyAgenda">Pas de dates disponibles</p>');
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('click', 'div.dayBox', function () {
            let $btn = $(this);
            let $parent = $btn.closest('section');
            rdvDateReminder = $btn.attr('data-date-reminder');
            rdvDate = $btn.attr('data-date-choice');
            idRdvType = $btn.attr('data-id-rdv-type');
            idAgenda = $btn.attr('data-id-agenda');

            if ($('div#appointment-appoe').length === 0) {
                $($parent).wrap('<div id="appointment-appoe"></div>');
            }

            if (!$btn.hasClass('activeDate')) {
                appointment_getLoader();
                $('div.dayBox').removeClass('activeDate');
                $('p#emptyAgenda').remove();
                $('.appointmentAppoeReminder').remove();
                $btn.addClass('activeDate');

                $('section.agendaAvailabilities').remove();
                $('section#agendaForm').remove();

                if ($btn.hasClass('disabledDay')) {
                    $('div#appointment-appoe').append('<p id="emptyAgenda">Pas de disponibilités</p>');
                    appointment_removeLoader();
                    return;
                }

                appointment_ajax({
                    getAvailabilitiesByDate: 'OK',
                    idAgenda: $btn.attr('data-id-agenda'),
                    dateChoice: $btn.attr('data-date-choice'),
                    rdvTypeDuration: $btn.attr('data-rdv-duration')
                }).done(function (data) {
                    if (data) {
                        $('div#appointment-appoe').append(data);
                        scrollSmooth($btn.closest('section#agendaDatesRdv').offset().top - 100);
                    } else {
                        $('div#appointment-appoe').append('<p id="emptyAgenda">Aucun créneau horaire n\'est disponible</p>');
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('click', 'button.availabilityChoice', function () {

            let $btn = $(this);
            rdvBegin = $btn.attr('data-start');
            rdvEnd = $btn.attr('data-end');

            if (!$btn.hasClass('activeAgendaBtn')) {
                appointment_getLoader();
                $('p#emptyAgenda').remove();
                $('button.availabilityChoice').removeClass('activeAgendaBtn');
                $btn.addClass('activeAgendaBtn');

                appointment_ajax({
                    getFormByRdvType: 'OK',
                    idRdvType: idRdvType
                }).done(function (data) {
                    if (data) {
                        $('.appointmentAppoeReminder.hoursRemind').fadeIn(200).find('strong').html(rdvDateReminder + ' à ' + minutesToHours(rdvBegin));
                        if ($('section#agendaForm').length === 0) {
                            $('div#appointment-appoe').append(data);
                        }
                        scrollSmooth($btn.closest('section').offset().top - 100);
                    } else {
                        $('div#appointment-appoe').append('<p id="emptyAgenda">Pas d\'identification possible</p>');
                    }
                    appointment_removeLoader();
                });

            }
        });

        $(document.body).on('blur', 'form#appointmentFormulaire input#appointment_email', function (e) {
            e.preventDefault();

            let input = $(this);
            let email = input.val();
            let form = input.closest('form#appointmentFormulaire');
            $('input[name="idClientKnown"], .unkownClient').remove();

            if (email !== '') {
                appointment_ajax({
                    checkClientKnown: 'OK',
                    email: email
                }).done(function (idClient) {
                    if (idClient && $.isNumeric(idClient)) {
                        form.prepend('<input name="idClientKnown" type="hidden" value="' + idClient + '">');
                    } else {
                        input.closest('div#defaultFields').append('<div class="unkownClient">ATTENTION !<br>Pour votre premier rendez-vous, vous devrez confirmer votre adresse email après validation</div>');
                    }
                });
            }
        });

        $(document.body).on('submit', 'form#appointmentFormulaire', function (e) {
            e.preventDefault();

            let $form = $(this);
            $form.prepend('<input name="idAgenda" type="hidden" value="' + idAgenda + '">');
            $form.prepend('<input name="idRdvType" type="hidden" value="' + idRdvType + '">');
            $form.prepend('<input name="rdvDate" type="hidden" value="' + rdvDate + '">');
            $form.prepend('<input name="rdvBegin" type="hidden" value="' + rdvBegin + '">');
            $form.prepend('<input name="rdvEnd" type="hidden" value="' + rdvEnd + '">');

            if ($form.find('input[name="idRdvToRemove"]').length > 0) {
                $form.attr('data-success', '<div class="appointmentAppoeReminder"><img src="/app/plugin/appointment/img/check.svg" width="30px">' +
                    'Votre rendez-vous du <strong>' + rdvDateReminder + '</strong>' +
                    ' a bien été modifié.<br>Vous recevrez bientôt un email récapitulatif de votre rendez vous.</div>');
            } else {

                if ($form.find('input[name="idClientKnown"]').length > 0) {
                    $form.attr('data-success', '<div class="appointmentAppoeReminder"><img src="/app/plugin/appointment/img/check.svg" width="30px">' +
                        'Votre rendez-vous du <strong>' + rdvDateReminder + '</strong>' +
                        ' a bien été enregistré.<br>Vous recevrez bientôt un email récapitulatif de votre rendez vous.</div>');
                } else {
                    $form.attr('data-success', '<div class="appointmentAppoeReminder"><img src="/app/plugin/appointment/img/check.svg" width="30px">' +
                        '<strong>Un email vous a été envoyé</strong>' +
                        '<em style="font-size: 16px;line-height: 24px;display: block;margin-top: 10px;">' +
                        'Afin de s\'assurer de la validité de vos coordonnées de contact et ainsi finaliser notre premier rendez-vous, ' +
                        'veuillez consulter l\'email et laissez-vous guider.</em></div>');
                }

            }

            postFormRequest($form, function () {
                let msg = $('#freturnMsg').html();
                let $parent = $('#appointment-appoe').parent();

                $('#editRdvMsg').remove();
                $('#appointment-appoe').slideUp(300, function () {
                    $(this).remove();
                });
                $parent.html(msg)
                $(window).scrollTop($parent.offset().top - 100);
            });
        });
    }
});