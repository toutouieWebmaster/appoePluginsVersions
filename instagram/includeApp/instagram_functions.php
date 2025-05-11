<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');

\App\Hook::add_action('core_admin_after_dashboard','instagram_dashboard', 10);

function instagram_getIdByToken()
{
    $return = getHttpRequest(INSTAGRAM_API_URL . 'me?fields=id,username&access_token=' . INSTAGRAM_TOKEN);
    debug($return);
}

function instagram_getRecentMedia()
{
    return getHttpRequest(INSTAGRAM_API_URL . 'me/media?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=' . INSTAGRAM_TOKEN);
}

function instagram_dashboard()
{
    if (defined('INSTAGRAM_USERNAME') && !empty(INSTAGRAM_USERNAME)):
        define('TIMELINE_FILE', WEB_PLUGIN_PATH . 'instagram/timeline.json'); ?>
        <div id="instagramContainer" class="row mb-3" data-instagram-username="<?= INSTAGRAM_USERNAME; ?>">
            <div class="d-flex col-12 col-lg-8">
                <div class="card border-0 w-100">
                    <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                        <h5 class="m-0 pl-4 colorPrimary">Instagram <?= trans('Timeline'); ?> <span
                                class="badge bgColorPrimary"><?= INSTAGRAM_USERNAME; ?></span></h5>
                        <hr class="mx-4">
                    </div>
                    <div class="card-body pt-0" id="instagramTimelineContainer">

                    </div>
                </div>
            </div>
            <div class="d-flex col-12 col-lg-4">
                <div class="card border-0 w-100">
                    <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                        <h5 class="m-0 pl-4 colorPrimary"><?= trans('Dernière mise à jour'); ?>
                            <button type="button" id="updateTimeline"
                                    class="btn btn-sm colorPrimary bgColorPrimary float-right">
                                Mettre à jour
                            </button>
                        </h5>
                        <hr class="mx-4">
                    </div>
                    <div class="card-body pt-0"
                         id="instagramTimelineInfos">
                        <p><?= file_exists(TIMELINE_FILE)
                                ? displayCompleteDate(getJsonContent(TIMELINE_FILE, 'lastUpdate'), true)
                                : trans('Le fichier de la timeline n\'existe pas'); ?></p>

                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function () {

                let $timelineContainer = $('#instagramTimelineContainer');

                $('#updateTimeline').on('click', function (event) {
                    event.preventDefault();

                    let $button = $(this);
                    $button.html(loaderHtml() + ' Chargement...');
                    busyApp();

                    let username = $('#instagramContainer').data('instagram-username');

                    $.post('/app/plugin/instagram/process/ajaxProcess.php', {updateTimeline: 'OK'}).done(function (data) {
                        if (data == 'true' || data === true) {

                            $button.remove();
                            $timelineContainer.html('<p>La timeline de <strong>' + username + '</strong> a été mise à jour !</p>');
                            setTimeout(function () {
                                $timelineContainer.html('');
                                showInstagramTimeline({container: '#instagramTimelineContainer', thumbnail: true});
                            }, 1000);

                            let d = new Date();

                            let month = d.getMonth() + 1;
                            let day = d.getDate();

                            let output = (day < 10 ? '0' : '') + day
                                + '/' + (month < 10 ? '0' : '') + month
                                + '/' + d.getFullYear()
                                + ' ' + d.getHours() + ':' + d.getMinutes();

                            $('#instagramTimelineInfos').html('<p>' + output + '</p>');

                        } else {
                            $timelineContainer.html('<p>Une erreur est survenue lors de la mise à jour !</p>');
                        }
                        availableApp();
                    });

                });
                showInstagramTimeline({container: '#instagramTimelineContainer', thumbnail: true, onlyImg: false});
            });
        </script>
    <?php endif;
}