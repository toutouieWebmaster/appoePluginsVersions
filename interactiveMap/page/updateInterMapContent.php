<?php

use App\Plugin\InteractiveMap\InteractiveMap;

require('header.php');
if (!empty($_GET['id'])):
    require(INTERACTIVE_MAP_PATH . 'process/postProcess.php');
    $InteractiveMap = new InteractiveMap();
    $InteractiveMap->setId($_GET['id']);
    if ($InteractiveMap->show()) :
        interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
        echo getTitle(getAppPageName(), getAppPageSlug()); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMap/', $InteractiveMap->getId()) ?>"
                       class="btn btn-warning btn-sm">
                        <span class="fas fa-cog"></span> <?= trans('Modifier la carte'); ?>
                    </a>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped bg-white">
                            <tr class="table-info-light">
                                <th><?= trans('Largeur'); ?></th>
                                <th><?= trans('Hauteur'); ?></th>
                                <th><?= trans('Statut de la carte'); ?></th>
                            </tr>
                            <tr>
                                <td><?= $InteractiveMap->getWidth(); ?></td>
                                <td><?= $InteractiveMap->getHeight(); ?></td>
                                <td><?= INTERACTIVE_MAP_STATUS[$InteractiveMap->getStatus()] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="my-2"></div>
            <?php showPostResponse(); ?>
            <div class="my-1"></div>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h5 class="strong py-2 border-bottom text-uppercase text-vert">
                                <?= trans('La carte'); ?>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="mapplic"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="custom-control custom-checkbox mt-4 mb-2">
                                <input type="checkbox" class="custom-control-input" id="addPointsChecker"
                                       aria-describedby="pointCheckHelpBlock">
                                <label class="custom-control-label" for="addPointsChecker">
                                    <?= trans('Insérer des emplacements à chaque click sur la carte'); ?>
                                </label>
                                <small id="pointCheckHelpBlock" class="form-text text-muted"></small>
                            </div>
                            <div class="custom-control custom-checkbox my-2">
                                <input type="checkbox" class="custom-control-input" id="addPointsCheckerByXy">
                                <label class="custom-control-label" for="addPointsCheckerByXy">
                                    <?= trans('Définir l\'emplacement par point et non par zone'); ?>
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox my-2">
                                <input type="checkbox" class="custom-control-input" id="addPointsCheckerSameTitle">
                                <label class="custom-control-label" for="addPointsCheckerSameTitle">
                                    <?= trans('Définir le titre par le nom de l\'emplacement'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h5 class="strong py-2 border-bottom text-uppercase text-vert">
                                <?= trans('Contenu de la carte'); ?>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="pointContenair"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
        <script type="text/javascript">

            $(document).ready(function () {

                var idMap = '<?= $InteractiveMap->getId(); ?>';

                var freeToAdd = true;

                var interMapOptions = {
                    source: '<?= INTERACTIVE_MAP_URL . slugify($InteractiveMap->getTitle()); ?>.json',
                    sidebar: true, 			// Enable sidebar
                    minimap: true, 			// Enable minimap
                    markers: true, 		// Disable markers
                    fillcolor: '', 		// Disable default fill color
                    mapfill: true,
                    lightbox: true,
                    fullscreen: true, 		// Enable fullscreen
                    maxscale: 3, 			// Setting maxscale to 3 times bigger than the original file
                    developer: false,
                    landmark: true,
                    tooltip: {
                        thumb: true,
                        desc: true,
                        link: false
                    }
                };

                var mapplic = $('#mapplic').mapplic(interMapOptions);

                var self = mapplic.data('mapplic');

                mapplic.on('mapready', function (e, self) {
                    window.currentLevel = self.currentLevel;
                });

                mapplic.on('locationopened', function (e, location) {
                    window.currentLevel = self.currentLevel;
                    if (!$('#addPointsChecker').is(':checked')) {
                        reloadPointContainer(location.id);
                    }
                });

                mapplic.on('levelswitched', function (e, level) {
                    window.currentLevel = level;
                    self.moveTo(0, 0, 0, 0);
                    self.hideLocation();
                });

                $('#pointContenair').on('click', 'button.refreshInterMapPoint', function (event) {
                    $('#loader').fadeIn('fast');
                    location.reload(true);
                });

                $('#pointContenair').on('click', 'button.deleteInterMapPoint', function (event) {
                    event.preventDefault();
                    busyApp();
                    var $btn = $(this);
                    var idMap = $btn.data('idmap');
                    var level = $btn.data('level');
                    var locationId = $btn.data('id');

                    if (confirm('<?= trans('Vous allez supprimer ce point de la carte'); ?>')) {
                        $('#pointContenair').html('<i class="fas fa-circle-notch fa-spin"></i>');
                        $.post(
                            '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                            {
                                deleteMapLocation: 'OK',
                                idMap: idMap,
                                locationId: locationId,
                                level: level
                            }, function (data) {
                                if (data) {
                                    $('a.mapplic-pin[data-location="' + locationId + '"]').remove();
                                    self.hideLocation();
                                    $('#pointContenair').html('');
                                    $('li.mapplic-list-location[data-location="' + locationId + '"]').remove();
                                    availableApp();
                                }
                            }
                        );
                    }
                });

                $(document).on('click', 'a.mapplic-pin, li.mapplic-list-location', function (e) {
                    var locationPointer = $(this);
                    $(".mapplic-levels option").each(function () {
                        if ($(this).val() == locationPointer.data('location')) {
                            e.preventDefault();
                            self.switchLevel(locationPointer.data('location'));
                            self.moveTo(0, 0, 0, 0);
                            return false;
                        }
                    });
                });

                $('#addPointsChecker').change(function () {
                    if (this.checked) {
                        $('#pointCheckHelpBlock').html('<?= trans("Un rechargement de la carte est nécessaire"); ?>');
                        $('#addPointsCheckerSameTitle').prop("checked", true);
                    } else {
                        $('#pointCheckHelpBlock').html('');
                        $('#addPointsCheckerSameTitle').prop("checked", false);
                        $('#addPointsCheckerByXy').prop("checked", false);
                    }
                });

                $(document).on('click', '.mapplic-layer', function (e) {

                    if ($('#addPointsChecker').is(':checked')) {

                        if (freeToAdd) {
                            busyApp();
                            freeToAdd = false;
                            var currentLevel = '';
                            $('#pointContenair').html('<i class="fas fa-circle-notch fa-spin"></i>');

                            var element = $(this).children('.mapplic-map-image').prop("tagName");
                            var id = uniqId();
                            if (element == 'DIV') {
                                if (!$('#addPointsCheckerByXy').is(':checked')) {
                                    id = e.target.id;
                                }
                            }

                            var map = $('.mapplic-map'),
                                x = (e.pageX - map.offset().left) / map.width(),
                                y = (e.pageY - map.offset().top) / map.height();

                            var xPoint = parseFloat(x).toFixed(4);
                            var yPoint = parseFloat(y).toFixed(4);

                            if ($('.mapplic-levels option:selected').length) {
                                currentLevel = $('.mapplic-levels option:selected').val();
                            } else {
                                currentLevel = window.currentLevel;
                            }

                            var title = '';
                            if ($('#addPointsCheckerSameTitle').is(':checked')) {
                                title = id;
                            }
                            $.post(
                                '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                                {
                                    addMapLocation: 'OK',
                                    idMap: idMap,
                                    id: id,
                                    currentLevel: currentLevel,
                                    xPoint: xPoint,
                                    yPoint: yPoint,
                                    title: title
                                }, function (data) {
                                    if (data && (data == 'true' || data === true)) {
                                        reloadPointContainer(id);
                                        var top = yPoint * 100,
                                            left = xPoint * 100;

                                        $('.mapplic-layer a').removeClass('mapplic-active');
                                        $('.mapplic-layer[data-floor="' + currentLevel + '"]')
                                            .append('<a href="#" class="mapplic-pin default mapplic-active" style="top: ' +
                                                parseFloat(top).toFixed(4) +
                                                '%; left: ' + parseFloat(left).toFixed(4) +
                                                '%;" data-location="' + id + '"></a>');
                                        freeToAdd = true;
                                    } else if (data == 'false' || data === false) {
                                        $('#pointContenair').html('<?= trans('Cet emplacement est déjà réservé'); ?>');
                                        freeToAdd = true;
                                    } else {
                                        $('#pointContenair').html('<?= trans('Cet emplacement est déjà réservé'); ?>');
                                        freeToAdd = true;
                                    }
                                    availableApp();
                                }
                            );
                        }
                    }
                });

                function reloadPointContainer(location) {

                    if (location && !$('#mapplic').hasClass('mapplic-fullscreen')) {
                        busyApp();
                        var currentLevel = '';
                        if ($('.mapplic-levels option:selected').length) {
                            currentLevel = $('.mapplic-levels option:selected').val();
                        } else {
                            currentLevel = window.currentLevel;
                        }

                        $('#pointContenair').html('<i class="fas fa-circle-notch fa-spin"></i>');

                        var src = '<?= INTERACTIVE_MAP_URL . 'allPoints.php?'; ?>';
                        var data = 'id=' + idMap + '&level=' + currentLevel + '&location=' + location;
                        $('#pointContenair').load(src + data, function () {
                            availableApp();
                            appoEditor();
                        });
                    }
                }

                function uniqId() {
                    return Math.round(new Date().getTime() + (Math.random() * 100));
                }
            });
        </script>
    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>