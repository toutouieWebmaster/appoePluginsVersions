//get messages
function liveMessages() {
    return jQuery.get('/app/plugin/messagIn/syncMessages.php');
}

function getLiveMessage() {
    liveMessages().done(function (data) {
        if (data != '' && ($.isNumeric(data) || data === 0)) {
            badgeMessageCheck(data);
            return true;
        } else {
            return false;
        }
    });
}

function badgeMessageCheck(data) {
    if (!$('a#navbarDropdownMessageMenu > span.countMsg').length) {
        $('a#navbarDropdownMessageMenu').append(' <span class="countMsg">' + data + '</span>');
    } else {
        $('a#navbarDropdownMessageMenu span.countMsg').html(data);
    }
}

jQuery(document).ready(function () {
    checkUserSessionExit().done(function (data) {
        if (data == 'true') {
            getLiveMessage();

            //Start Cron
            setInterval(function () {
                getLiveMessage();
            }, 60000);
        }
    });
});