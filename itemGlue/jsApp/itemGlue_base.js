const WEB_ITEMGLUE_URL = WEB_APP_URL + 'plugin/itemGlue/';
const WEB_ITEMGLUE_PROCESS_URL = WEB_ITEMGLUE_URL + 'process/';

function addMetaArticle(data) {
    return $.post(WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php', data);
}

function deleteMetaArticle(idMetaArticle) {

    return $.post(
        WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
        {
            DELETEMETAARTICLE: 'OK',
            idMetaArticle: idMetaArticle
        }
    );
}

function resetMetas() {
    $('form#addArticleMetaForm input[name="UPDATEMETAARTICLE"]').val('');
    $('form#addArticleMetaForm input#metaKey').val('');
    $('form#addArticleMetaForm textarea#metaValue').val('');
    var idEditor = $('textarea#metaValue').data('editor-id');
    $('div.inlineAppoeditor[data-editor-id="' + idEditor + '"]').html('');
    $('form#addArticleMetaForm').trigger("reset").blur();
    return true;
}

$(document).ready(function () {

    /**
     * Update article content
     */

    if ($("#allMediaModalContainer").length) {

        let $articleMetaContainer = $('#metaArticleContenair');

        $('#allMediaModalContainer').load('/app/ajax/media.php?getAllMedia');

        $articleMetaContainer.load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'));

        $('form#galleryArticleForm').submit(function () {
            $('#loader').fadeIn('fast');
        });

        $('input[name="categories[]"]').each(function () {
            if ($(this).next('label').text().charAt(0) !== '-') {
                $(this).parent('.checkCategories').wrap('<div class="mr-5 my-4 pb-2 border-bottom">');
            } else {
                $(this).parent('.checkCategories').prev('div').append($(this).parent('.checkCategories'));
            }
        }).eq(0).parent('.checkCategories').parent('div').parent('div')
            .addClass('d-flex flex-row justify-content-start flex-wrap my-3')
            .children('strong.inputLabel').addClass('w-100');

        $('#metaDataAvailable').change(function () {
            if ($('#metaDataAvailable').is(':checked')) {
                $('form#addArticleMetaForm input#metaKey').val(convertToSlug($('form#addArticleMetaForm input#metaKey').val()));
            }
        });

        $('form#addArticleMetaForm input#metaKey').keyup(function () {
            if ($('#metaDataAvailable').is(':checked')) {
                $('form#addArticleMetaForm input#metaKey').val(convertToSlug($('form#addArticleMetaForm input#metaKey').val()));
            }
        });

        $(document.body).on('click', '#resetmeta', function (e) {
            e.preventDefault();
            resetMetas();
        });

        $(document.body).on('input change', 'div.inlineAppoeditor', function (e) {
            e.stopPropagation();
            var id = $(this).data('editor-id');
            $('textarea.appoeditor[data-editor-id="' + id + '"]').val($(this).html());
        });

        $('form#addArticleMetaForm').on('submit', function (event) {
            event.preventDefault();

            if ($('#metaDataAvailable').is(':checked')) {
                if (!confirm('Vous êtes sur le point de supprimer la mise en forme')) {
                    return false;
                }
            }

            var $form = $(this);
            busyApp();

            var idEditor = $('textarea#metaValue').data('editor-id');
            var textareaEditor = $('div.inlineAppoeditor[data-editor-id="' + idEditor + '"]');
            let idMeta = $('input[name="UPDATEMETAARTICLE"]').val();

            var data = {
                ADDARTICLEMETA: 'OK',
                UPDATEMETAARTICLE: idMeta,
                idArticle: $('input[name="idArticle"]').val(),
                metaKey: $('input#metaKey').val(),
                metaValue: $('#metaDataAvailable').is(':checked')
                    ? textareaEditor.html().replace(/(<([^>]+)>)/ig, "")
                    : textareaEditor.html()
            };

            addMetaArticle(data).done(function (results) {
                if (results == 'true' || results === true) {

                    notification('<strong>' + $('input#metaKey').val() + '</strong> a été enregistré !');

                    //clear form
                    resetMetas();

                    $articleMetaContainer.html(loaderHtml())
                        .load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'), function (){
                            $('button.metaProductTitle-' + idMeta).trigger('click');
                        });
                } else {
                    notification('Une erreur est survenue lors de l\'enregistrement de ' + $('input#metaKey').val(), 'danger');
                }

                $('[type="submit"]', $form).attr('disabled', false).html('Enregistrer').removeClass('disabled');
                availableApp();
            });
        });

        $articleMetaContainer.on('click', '.metaProductUpdateBtn', function () {
            var $btn = $(this);
            var idMetaArticle = $btn.data('idmetaproduct');

            var $contenair = $('div.card[data-idmetaproduct="' + idMetaArticle + '"]');
            var title = $contenair.find('h5 button.metaProductTitle-' + idMetaArticle).text();
            var content = $contenair.find('div.metaProductContent-' + idMetaArticle).html();

            $('input[name="UPDATEMETAARTICLE"]').val(idMetaArticle);
            $('input#metaKey').val($.trim(title));
            var idEditor = $('textarea#metaValue').data('editor-id');
            $('div.inlineAppoeditor[data-editor-id="' + idEditor + '"]').html(content);
            $('textarea#metaValue').val(content)
        });

        $articleMetaContainer.on('click', '.metaProductDeleteBtn', function () {
            var $btn = $(this);
            var idMetaArticle = $btn.data('idmetaproduct');

            if (confirm('Êtes-vous sûr de vouloir supprimer cette métadonnée ?')) {
                busyApp();

                deleteMetaArticle(idMetaArticle).done(function (data) {
                    if (data == 'true') {

                        $articleMetaContainer.html(loaderHtml())
                            .load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'));
                    }
                    availableApp();
                });
            }
        });

        $('.otherArticlesSelect').change(function () {
            var otherEventslink = $('option:selected', this).data('href');
            location.assign(otherEventslink);
        });

        $(document.body).on('click', '#clearArticleCache', function () {

            if (confirm('Vous êtes sur le point de vider le cache de l\'article')) {

                var $btn = $(this);
                $btn.html(loaderHtml());

                busyApp(false);
                $.post('/app/plugin/cms/process/ajaxProcess.php', {
                    clearPageCache: 'OK',
                    pageSlug: $btn.data('page-slug'),
                    pageLang: $btn.data('page-lang')
                }).done(function (data) {
                    if (data == 'true' || data === true) {
                        $btn.html('<i class="fas fa-check"></i> Cache vidé!').blur()
                            .removeClass('btn-outline-danger').addClass('btn-success');
                    } else {
                        alert('Un problème est survenu lors de la vidange du cache');
                    }
                    availableApp();
                });
            }
        });

        $('#updateSlugAuto').on('change', function () {
            $('form#updateArticleHeadersForm input#slug').val(convertToSlug($('form#updateArticleHeadersForm input#name').val()));
        });

        $('form#updateArticleHeadersForm input#name').on('input', function () {
            if ($('form#updateArticleHeadersForm #updateSlugAuto').is(':checked')) {
                $('form#updateArticleHeadersForm input#slug').val(convertToSlug($(this).val()));
                countChars($('form#updateArticleHeadersForm input#slug'), 'slug');
            }
        });
    }

    /**
     * Add article
     */
    //Focus on input for add an article
    setTimeout(function () {
        $('form#addArticleForm input#name').focus();
    }, 100);


    //Add automatically a slug and a description from name of the article, when add new article
    $('form#addArticleForm input#name').keyup(function () {
        $('form#addArticleForm input#slug').val(convertToSlug($(this).val()));
        $('form#addArticleForm textarea#description').val($(this).val());

        countChars($('form#addArticleForm input#slug'), 'slug');
        countChars($('form#addArticleForm textarea#description'), 'description');
    });

    //Stop adding automaticly slug and description from the name of article
    $('form#addArticleForm input#slug, form#addArticleForm textarea#description').on('focus', function () {
        $('form#addArticleForm input#name').unbind('keyup');
    });

    /**
     * All articles
     */

    //Archive an article
    $(document).on('click', '.archiveArticle', function (e) {
        e.preventDefault();

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        if (confirm($btn.data('confirm-msg'))) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    archiveArticle: 'OK',
                    idArticleArchive: idArticle
                },
                function (data) {
                    if (data === true || data === 'true') {
                        $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                        $('div.card[data-idarticle="' + idArticle + '"]').fadeOut();
                        availableApp();
                    }
                }
            );
        }
    });

    //Highlight an article
    $(document).on('click', '.featuredArticle', function () {

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        let $btns = $('.featuredArticle[data-idarticle="' + idArticle + '"');

        let titleStandard = $btn.attr('data-title-standard');
        let titleFeatured = $btn.attr('data-title-vedette');
        let confirmStandard = $btn.attr('data-confirm-standard');
        let confirmFeatured = $btn.attr('data-confirm-vedette');

        let nowStatut = $btn.attr('data-statutarticle') == 2 ? 1 : 2;

        let $iconContainer = $btns.children('span');

        let iconFeatured = nowStatut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';

        let textConfirmFeatured = nowStatut == 2 ? confirmFeatured : confirmStandard;
        let textTitleFeatured = nowStatut == 2 ? titleFeatured : titleStandard;

        if (confirm(textConfirmFeatured)) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    featuredArticle: 'OK',
                    idArticleFeatured: idArticle,
                    newStatut: nowStatut
                },
                function (data) {
                    if (data === true || data === 'true') {

                        $btns.attr('data-statutarticle', nowStatut);
                        $btns.attr('title', textTitleFeatured);
                        $iconContainer.html(iconFeatured);
                        availableApp();
                    }
                }
            );
        }
    });

    let artcileGridPreference = window.localStorage.getItem('articleGridPreferences');
    if (artcileGridPreference === 'grid') {
        createArticleGridView();
        showArticlesGrid();
    }

    $('#displayArticleAsGrid').on('click', function () {
        createArticleGridView();
        showArticlesGrid();
    });

    $('#displayArticleAsTable').on('click', function () {
        showArticlesTable();
    });

    function showArticlesTable() {

        $('#displayArticleAsTable').prop('disabled', true);
        window.localStorage.setItem('articleGridPreferences' , 'table');

        $('#articlesGridContainer').fadeOut('fast', function () {
            if (!$('#articlesTable').is(":visible")) {
                $('#articlesTable').fadeIn('fast');
                $('#displayArticleAsGrid').prop('disabled', false);
            }
        });
    }

    function showArticlesGrid() {

        $('#displayArticleAsGrid').prop('disabled', true);
        window.localStorage.setItem('articleGridPreferences' , 'grid');
        $('#articlesTable').fadeOut('fast', function () {
            if (!$('#articlesGridContainer').is(":visible")) {
                $('#articlesGridContainer').fadeIn('slow');
                $('#displayArticleAsTable').prop('disabled', false);
            }
        });
    }

    function createArticleGridView() {

        if (!$('#articlesGridContainer').length) {
            $('<div id="articlesGridContainer" class="card-columns"></div>').hide().insertAfter('#articlesTable');

            $('#articlesTable table tr:has(td)').each(function (index, tr) {
                let $tr = $(tr);
                let html = '<div class="card my-3" data-idarticle="' + $tr.data('idarticle') + '">';

                html += '<img src="' + $tr.data('img') + '">';
                html += '<div class="card-body">';
                html += '<p class="card-title"><b>Titre</b><br>' + $tr.find('td[data-col="name"]').text() + '</p>';
                html += '<p class="card-text"><b>Description</b><br>' + $tr.data('description') + '</p>';
                html += '<p class="card-text"><b>Slug</b><br>' + $tr.find('td[data-col="slug"]').text() + '</p>';
                html += '<p class="card-text"><b>Catégories</b><br>' + $tr.find('td[data-col="categories"]').text() + '</p>';
                html += '<p class="card-text"><small class="text-muted">' + $tr.find('td[data-col="date"]').text() + '</small></p>';
                html += '<p class="card-text d-flex justify-content-between btn-group">' + $tr.find('td[data-col="buttons"]').html() + '</p>';
                html += '</div></div></div>';

                $('#articlesGridContainer').append(html);
            });
        }
    }

    /**
     * Articles archives
     */

    //Unpack an article
    $('.unpackArticle').on('click', function () {

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        if (confirm($btn.data('confirm-msg'))) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    unpackArticle: 'OK',
                    idUnpackArticle: idArticle
                },
                function (data) {
                    if (data === true || data === 'true') {
                        $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });

    //Delete definitively an article
    $('.deleteArticle').on('click', function () {

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        if (confirm($btn.data('confirm-msg'))) {
            busyApp();
            $.post(
                WEB_PLUGIN_URL + 'itemGlue/process/ajaxProcess.php',
                {
                    deleteArticle: 'OK',
                    idArticleDelete: idArticle
                },
                function (data) {
                    if (data === true || data === 'true') {
                        $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });
});