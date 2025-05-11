function getInteractiveMap(filename, element, options = []) {

    let interMapOptions = {
        source: '/app/plugin/interactiveMap/' + filename + '.json',
        sidebar: false,
        search: false,
        minimap: false,
        markers: false,
        clearbutton: false,
        zoombuttons: false,
        zoomoutclose: false,
        fillcolor: '',
        fullscreen: false,
        maxscale: 3,
        developer: false,
        mapfill: true,
        lightbox: true,
        landmark: true,
        action: 'tooltip',
        tooltip: {
            thumb: true,
            desc: true,
            link: true
        }
    };

    $.extend(interMapOptions, options);

    $(element).mapplic(interMapOptions);
}