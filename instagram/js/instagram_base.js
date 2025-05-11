function getInstagramTimelineFile() {
    return $.getJSON('/app/plugin/instagram/timeline.json');
}

function showInstagramTimeline(preferences = {}) {

    let options = {
        count: 5,
        container: '#instagramTimelineContainer',
        thumbnail: false,
        onlyImg: true
    }
    $.extend(options, preferences);
    let obj;
    let $timelineContainer = $(options.container);

    getInstagramTimelineFile().done(function (timeline) {
        if (timeline) {
            let realC = 0;
            let item, img, imgUrl, video;
            for (let c = 0; c < timeline.data.length; c++) {
                if (timeline.data[c] && realC < options.count) {
                    obj = timeline.data[c];

                    if (options.onlyImg && obj.media_type === 'VIDEO') {
                        continue
                    }

                    if (obj.media_type !== 'VIDEO') {
                        imgUrl = (options.thumbnail && obj.thumbnail_url) ? obj.thumbnail_url : obj.media_url;
                        img = '<img src="' + imgUrl + '" alt="' + obj.caption + '">';
                        item = '<a href="' + obj.permalink + '" target="_blank">' + img + '</a>';
                    } else {
                        video = '<video autoplay="autoplay"><source src="' + obj.media_url + '" type="video/mp4"></video>';
                        item = '<a href="' + obj.permalink + '" target="_blank">' + video + '</a>';

                    }
                    $timelineContainer.append(item);
                    realC++;
                }
            }
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR, textStatus, errorThrown);
    });
}