function leaflet_getMap(lngLat, zoom, otherTile = '') {

    if (jQuery('#mapOSM').length) {

        //Create Map
        var map = new L.Map('mapOSM', {
            scrollWheelZoom: false,
            zoom: zoom,
            maxZoom: 22,
            gestureHandling: true
        }).setView(lngLat, zoom);

        //Add tiles
        map.addLayer(new L.TileLayer(!otherTile.trim() ? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' : otherTile, {
            attribution: '<a href="https://www.openstreetmap.org/" target="_blank">OSM</a> | <a href="https://aoe-communication.com" target="_blank" title="Art Of Event - Communication">AOE</a>',
            maxZoom: 22,
            maxNativeZoom: 22
        }));

        return map;
    }
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
        var map = leaflet_getMap(mapOptions.lngLat, mapOptions.zoom, mapOptions.otherTile);

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

    if (jQuery('#mapOSM').length) {

        new L.Control.Gps({

            //autoActive:true,
            autoCenter: true

        }).addTo(map);
    }
}

function leaflet_developpement(map) {

    if (jQuery('#mapOSM').length) {
        var popup = L.popup();

        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent("Position : " + e.latlng.toString())
                .openOn(map);
        }

        map.on('click', onMapClick);
    }
}

function leaflet_simpleMarker(map, lngLat, title, imgSrc) {

    if (jQuery('#mapOSM').length) {

        var marker = L.marker(lngLat, {title: title}).addTo(map);
        marker.bindPopup('<img src="' + imgSrc + '" alt="' + title + '" style="max-width:100%">', {minWidth: 100}).openPopup();
        map.invalidateSize();
    }
}

function leaflet_showImg(map, imgSrc = '/app/lib/template/images/logo_app.png', imgWidth = '128px', onclickUrl = 'https://aoe-communication.com', position = 'bottomleft') {

    if (jQuery('#mapOSM').length) {

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
}

function leaflet_aoe(map) {

    if (jQuery('#mapOSM').length) {

        var marker = new L.Marker([48.585863, 7.763], {title: 'Art Of Event - Communication'}).addTo(map);
        marker.bindTooltip("<b>Art Of Event</b><br>Communication");
    }
}