jQuery(document).ready(function ($) {

    var starClick = [];

    $('.total_votes').html('');

    $('.starClick').hover(
        // Handles the mouseover
        function () {
            var idStars = $(this).parent('div.rate_widget').data('idstars');
            if ($.inArray(idStars, starClick) == -1) {
                $(this).prevAll().andSelf().addClass('ratings_over');
                $(this).nextAll().removeClass('ratings_vote');
            }
        },
        // Handles the mouseout
        function () {
            var idStars = $(this).parent('div.rate_widget').data('idstars');
            if ($.inArray(idStars, starClick) == -1) {
                $(this).prevAll().andSelf().removeClass('ratings_over');
                set_votes($(this).parent());
            }
        }
    );

    $('.rate_widget').each(function (i) {
        var widget = this;
        var out_data = {
            widget_id: $(widget).attr('id').split('-')[2],
            widget_type: $(widget).data('type'),
            fetch: 1
        };
        $.post(
            '/app/plugin/rating/process/ajaxProcess.php',
            out_data,
            function (INFO) {
                if (INFO) {

                    $(widget).data('fsr', INFO);
                    set_votes(widget);
                }
            },
            'json'
        );
    });

    $('.starClick').bind('click', function () {
        var idStars = $(this).parent('div.rate_widget').data('idstars');
        if ($.inArray(idStars, starClick) == -1) {

            starClick.push(idStars);
            var star = this;
            var widget = $(this).parent();

            var clicked_data = {
                clicked_on: $(star).attr('class').split(' ')[0],
                widget_id: widget.attr('id').split('-')[2],
                widget_type: widget.data('type')
            };

            $.post(
                '/app/plugin/rating/process/ajaxProcess.php',
                clicked_data,
                function (INFO) {
                    if (INFO) {
                        $('#' + widget.data('type') + '_' + widget.attr('id').split('-')[2] + ' .total_votes').html(INFO);
                        /*widget.data('fsr', INFO);
                        set_votes(widget);*/
                    }
                },
                'json'
            );
        }
    });

    function set_votes(widget) {

        $('.total_votes', widget).html('');

        if ($(widget).data('fsr').whole_avg) {
            $('.total_votes', widget).html('<i class="fas fa-circle-notch fa-spin"></i>');

            var avg = $(widget).data('fsr').whole_avg;
            var votes = $(widget).data('fsr').number_votes;
            var exact = $(widget).data('fsr').dec_avg;

            $(widget).find('.star_' + avg).prevAll().addBack().addClass('ratings_vote');
            $(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote');
            $(widget).find('.total_votes').text(votes + ' évaluations ( note : ' + exact + ' )');
        }
    }
});