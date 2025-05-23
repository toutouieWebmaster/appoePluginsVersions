function tracker_getVisites(dateStart = null, dateEnd = null) {

    let $icon = $('#refreshTracker').find('i');
    $icon.addClass('fa-spin');

    let url = '/app/plugin/tracker/tracker_dashboard.php';
    url += !empty(dateStart) || !empty(dateEnd) ? '?' : '';
    url += !empty(dateStart) ? 'dateStart=' + dateStart : '';
    url += !empty(dateStart) && !empty(dateEnd) ? '&' : '';
    url += !empty(dateEnd) ? 'dateEnd=' + dateEnd : '';

    jQuery('#visitorsTracker').load(url, function () {
        $icon.removeClass('fa-spin');
    });
}

jQuery(document).ready(function () {

    tracker_getVisites();

    $(document).on('click', '#refreshTracker, #refreshTrackerDate', function () {
        let $trackerData = $('#trackerData');
        let dateStart = $trackerData.find('input#dateStart').val();
        let dateEnd = $trackerData.find('input#dateEnd').val();
        if (dateStart > dateEnd) {
            $('#errorMessageTracker').html('<i>Veuillez entrer une date de début antérieure à la date de fin</i>');
        } else {
            tracker_getVisites(dateStart, dateEnd);
        }

    });

});