<?php
require_once('../main.php');

use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleMeta;

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        includePluginsFiles();

        $_POST = cleanRequest($_POST);

        if (isset($_POST['archiveArticle']) && !empty($_POST['idArticleArchive'])) {
            $Article = new Article($_POST['idArticleArchive']);
            $Article->setStatut(0);
            if ($Article->update()) {
                echo 'true';
                exit();
            }
        }

        if (isset($_POST['unpackArticle']) && !empty($_POST['idUnpackArticle'])) {
            $Article = new Article($_POST['idUnpackArticle']);
            $Article->setStatut(1);
            if ($Article->update()) {
                echo 'true';
                exit();
            }
        }

        if (isset($_POST['deleteArticle']) && !empty($_POST['idArticleDelete'])) {
            $Article = new Article($_POST['idArticleDelete']);
            if ($Article->delete()) {
                echo 'true';
                exit();
            }
        }

        if (isset($_POST['featuredArticle']) && !empty($_POST['idArticleFeatured']) && !empty($_POST['newStatut'])) {
            $Article = new Article($_POST['idArticleFeatured']);
            $Article->setStatut($_POST['newStatut']);
            if ($Article->update()) {
                echo 'true';
                exit();
            }
        }

        if (isset($_POST['deleteImage']) && !empty($_POST['idImage'])) {

            $ArticleMedia = new ArticleMedia();
            $ArticleMedia->setId($_POST['idImage']);
            if ($ArticleMedia->show()) {
                if ($ArticleMedia->delete()) {
                    echo 'true';
                    exit();
                }
            }
        }

        /**
         * Meta Product
         */
        if (isset($_POST['DELETEMETAARTICLE']) && !empty($_POST['idMetaArticle'])) {
            $ArticleMeta = new ArticleMeta();
            $ArticleMeta->setId($_POST['idMetaArticle']);
            if ($ArticleMeta->delete()) {
                echo json_encode(true);
                exit();
            }
        }

        if (isset($_POST['ADDARTICLEMETA'])
            && !empty($_POST['idArticle'])
            && !empty($_POST['metaKey'])
            && isset($_POST['metaValue'])) {

            $ArticleMeta = new ArticleMeta();
            $ArticleMeta->feed($_POST);

            if (!empty($_POST['UPDATEMETAARTICLE'])) {

                $ArticleMeta->setId($_POST['UPDATEMETAARTICLE']);
                $ArticleMeta->setLang(APP_LANG);

                if ($ArticleMeta->notExist(true)) {
                    if ($ArticleMeta->update()) {

                        echo json_encode(true);
                        exit();
                    }
                }

            } else {

                if ($ArticleMeta->notExist()) {

                    foreach (getLangs() as $minLang => $largeLang) {

                        $ArticleMeta->setLang($minLang);
                        $ArticleMeta->save();
                    }

                    echo json_encode(true);
                    exit();
                }
            }
        }
    }
}