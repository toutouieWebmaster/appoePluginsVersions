$.fn.mappoe = function () {
    return this.each(function (i, el) {

        let $container = $(el);
        if ($container.length) {

            if (!$container.attr('id')) {
                let randomId = Math.floor((Math.random() * 1000) + 1);
                $container.attr('id', 'mappoe-' + randomId);
            }
            if (!$container.hasClass('mappoe')) {
                $container.addClass('mappoe');
            }

            let mapOptions = {
                lng: $container.attr('data-lng'),
                lat: $container.attr('data-lat'),
                title: $container.attr('data-title') ? $container.attr('data-title') : null,
                html: $container.attr('data-html') ? $container.attr('data-html') : null,
                markerName: $container.attr('data-marker-name') ? $container.attr('data-marker-name') : null,
                markerSize: $container.attr('data-marker-size') ? parseInt($container.attr('data-marker-size')) : 40,
                otherTile: $container.attr('data-other-tile') ? $container.attr('data-other-tile') : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                zoom: $container.attr('data-zoom') ? parseInt($container.attr('data-zoom')) : 14,
                maxZoom: $container.attr('data-max-zoom') ? parseInt($container.attr('data-max-zoom')) : 19,
                minWidth: $container.attr('data-min-width') ? parseInt($container.attr('data-min-width')) : 100,
                openPopup: !($container.attr('data-popup') && $container.attr('data-popup') === 'close')
            };

            //Check for lngLat
            if (mapOptions.lng && mapOptions.lat) {

                //Get MAP
                var map = mappoe_getMap({
                    lngLat: [mapOptions.lng, mapOptions.lat],
                    zoom: mapOptions.zoom,
                    maxZoom: mapOptions.maxZoom,
                    id: $container.attr('id'),
                    otherTile: mapOptions.otherTile
                });

                if (map) {

                    //Get marker
                    var rootImg = WEB_PLUGIN_URL + 'leaflet/icons/';
                    var Icon = L.icon({
                        iconUrl: mapOptions.markerName ? mapOptions.markerName : rootImg + 'black.png',

                        iconSize: [mapOptions.markerSize, 'auto'],
                        iconAnchor: [(mapOptions.markerSize / 2), mapOptions.markerSize],
                        popupAnchor: [0, -mapOptions.markerSize]
                    });

                    var marker = new L.Marker([mapOptions.lng, mapOptions.lat], {
                        title: mapOptions.title,
                        icon: Icon
                    }).addTo(map);

                    //Add popup
                    if (mapOptions.html) {
                        marker.bindPopup(mapOptions.html, {minWidth: mapOptions.minWidth});
                        if (mapOptions.openPopup) {
                            marker.openPopup();
                        }
                    }

                    //Resize map
                    map.invalidateSize();
                }
            }
        }
    });
}

function mappoe_getMap(options = {}) {

    jQuery.extend({
        lngLat: '',
        zoom: 14,
        maxZoom: 19,
        id: '',
        otherTile: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    }, options);

    //Create Map
    var map = new L.Map(options.id, {
        scrollWheelZoom: false,
        attributionControl: false,
        zoom: options.zoom,
        maxZoom: options.maxZoom,
        gestureHandling: true
    }).setView(options.lngLat, options.zoom);

    //Add tiles
    map.addLayer(new L.TileLayer(options.otherTile, {
        attribution: '',
        maxZoom: options.maxZoom,
        maxNativeZoom: options.maxZoom
    }));

    return map;
}

function leaflet_getMap(lngLat, zoom, id = 'mapOSM', otherTile = '') {

    //Create Map
    var map = new L.Map(id, {
        scrollWheelZoom: false,
        attributionControl: false,
        zoom: zoom,
        maxZoom: 22,
        gestureHandling: true
    }).setView(lngLat, zoom);

    //Add tiles
    map.addLayer(new L.TileLayer(!otherTile.trim() ? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' : otherTile, {
        attribution: '',
        maxZoom: 22,
        maxNativeZoom: 22
    }));

    return map;
}

function leaflet_marker_show(options = []) {

    var mapOptions = {
        lngLat: [],
        title: null,
        html: null,
        markerName: null,
        markerSize: 40,
        otherTile: '',
        zoom: 14,
        minWidth: 100,
        openPopup: true
    };

    //Merge options
    jQuery.extend(mapOptions, options);

    //Check for lngLat
    if (!jQuery.isEmptyObject(mapOptions.lngLat)) {

        //Get MAP
        var map = leaflet_getMap(mapOptions.lngLat, mapOptions.zoom, 'mapOSM', mapOptions.otherTile);

        if (map) {

            //Get marker
            var rootImg = WEB_PLUGIN_URL + 'leaflet/icons/';
            var Icon = L.icon({
                iconUrl: mapOptions.markerName ? mapOptions.markerName : rootImg + 'black.png',

                iconSize: [mapOptions.markerSize, 'auto'],
                iconAnchor: [(mapOptions.markerSize / 2), mapOptions.markerSize],
                popupAnchor: [0, -mapOptions.markerSize]
            });

            var marker = new L.Marker(mapOptions.lngLat, {title: mapOptions.title, icon: Icon}).addTo(map);

            //Add popup
            if (mapOptions.html) {
                marker.bindPopup(mapOptions.html, {minWidth: mapOptions.minWidth});
                if (mapOptions.openPopup) {
                    marker.openPopup();
                }
            }

            //Resize map
            map.invalidateSize();
        }
    }
}

function leaflet_getGps(map) {
    new L.Control.Gps({

        //autoActive:true,
        autoCenter: true

    }).addTo(map);
}

function leaflet_developpement(map) {
    var popup = L.popup();

    function onMapClick(e) {
        popup
            .setLatLng(e.latlng)
            .setContent("Position : " + e.latlng.toString())
            .openOn(map);
    }

    map.on('click', onMapClick);
}

function leaflet_simpleMarker(map, lngLat, title, imgSrc) {

    var marker = L.marker(lngLat, {title: title}).addTo(map);
    marker.bindPopup('<img src="' + imgSrc + '" alt="' + title + '" style="max-width:100%">', {minWidth: 100}).openPopup();
    map.invalidateSize();
}

function leaflet_showImg(map, imgSrc = '/app/lib/template/images/logo_app.png', imgWidth = '128px', onclickUrl = 'https://aoe-communication.com', position = 'bottomleft') {

    L.Control.Watermark = L.Control.extend({

        onAdd: function (map) {

            var img = L.DomUtil.create('img');
            img.src = imgSrc;
            img.style.width = imgWidth;

            L.DomEvent.on(img, 'click', function () {
                window.open(onclickUrl, '_blank');
            });

            return img;
        },

        onRemove: function (map) {
        }
    });

    L.control.watermark = function (opts) {
        return new L.Control.Watermark(opts);
    };

    L.control.watermark({position: position}).addTo(map);
}

function leaflet_aoe(map) {
    var marker = new L.Marker([48.585863, 7.763], {title: 'P&P - Communication'}).addTo(map);
    marker.bindTooltip("<b>P&P</b><br>Communication");
}