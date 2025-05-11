jQuery(document).ready(function ($) {

    if ($('.shareOnFb').length) {

        let appId;

        $.post('/app/ajax/plugin.php', {getDefinedConst: 'FACEBOOK_APP_ID'}).done(function (data) {
            if (data) {
                appId = data.toString();

                $.getScript('https://connect.facebook.net/en_US/sdk.js', function () {
                    FB.init({
                        appId: appId,
                        status: true,
                        cookie: true,
                        xfbml: false,
                        autoLogAppEvents: false,
                        version: 'v6.0'
                    });
                });

                $(document.body).on('click', '.shareOnFb', function (e) {
                    e.preventDefault();

                    let $btn = $(this);
                    $btn.html(loaderHtml());

                    FB.ui({
                        method: 'share',
                        href: $btn.data('fb-post-link')
                    }, function (response) {
                        if (response && !response.error_message) {
                            $btn.removeClass('btn-info').addClass('btn-success').html('<i class="fas fa-check"></i>').prop('disabled', true);
                        } else {
                            $btn.removeClass('btn-info').addClass('btn-danger').html('<i class="fas fa-times"></i>').prop('disabled', true);
                        }
                    });
                });
            }
        });
    }
});