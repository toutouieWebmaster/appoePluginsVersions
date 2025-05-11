<?php

use App\CategoryRelations;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleContent;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleMeta;
use App\Plugin\ItemGlue\ArticleRelation;

$contentTabActive = true;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $mediaTabactive = false;
    $relationActive = false;

    if (isset($_POST['ADDARTICLE'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['description'])
            && !empty($_POST['slug'])
            && isset($_POST['statut'])
        ) {

            $_POST['statut'] = is_numeric($_POST['statut']) ? $_POST['statut'] : 0;

            $Article = new Article();

            $lastCharSlug = substr($_POST['slug'], -1);
            if ($lastCharSlug == '-') {
                $_POST['slug'] = substr($_POST['slug'], 0, -1);
            }

            $ArticleContent = new ArticleContent();
            if (!$ArticleContent->usedSlug($_POST['slug'])) {

                //Add Article
                $Article->setStatut($_POST['statut']);
                if ($Article->save()) {

                    $ArticleContent->setIdArticle($Article->getId());

                    $headers = array(
                        'NAME' => $_POST['name'],
                        'DESCRIPTION' => $_POST['description'],
                        'SLUG' => $_POST['slug']
                    );

                    $ArticleContent->saveHeaders($headers);

                    //Add Meta
                    if (defined('ARTICLE_META') && is_array(ARTICLE_META)) {

                        $ArticleMeta = new ArticleMeta();

                        foreach (getLangs() as $minLang => $largeLang) {
                            foreach (ARTICLE_META as $metaKey => $metaValue) {
                                $ArticleMeta->setIdArticle($Article->getId());
                                $ArticleMeta->setMetaKey($metaKey);
                                $ArticleMeta->setMetaValue($metaValue);
                                $ArticleMeta->setLang($minLang);
                                $ArticleMeta->save();
                            }
                        }

                    }

                    //Delete post data
                    unset($_POST);
                    setPostResponse('L\'article a été enregistré', 'success', ('<a href="' . getPluginUrl('itemGlue/page/articleContent/', $Article->getId()) . '">' . trans('Voir l\'article') . '</a>'));

                } else {
                    setPostResponse('Un problème est survenu lors de l\'enregistrement de l\'article');
                }
            } else {
                setPostResponse('Le slug "' . $_POST['slug'] . '" est déjà utilisé');
            }

        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEARTICLEHEADERS'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['name'])
            && !empty($_POST['description'])
            && !empty($_POST['slug'])
            && !empty($_POST['createdAt'])
            && isset($_POST['statut'])
        ) {

            $_POST['statut'] = is_numeric($_POST['statut']) ? $_POST['statut'] : 0;

            $Article = new Article();
            $Article->setId($_POST['id']);

            if ($Article->show()) {
                $Article->setStatut($_POST['statut']);
                $Article->setCreatedAt($_POST['createdAt']);

                if ($Article->update()) {

                    $ArticleContent = new ArticleContent();
                    $ArticleContent->setIdArticle($Article->getId());

                    $lastCharSlug = substr($_POST['slug'], -1);
                    if ($lastCharSlug == '-') {
                        $_POST['slug'] = substr($_POST['slug'], 0, -1);
                    }

                    $headers = array(
                        'NAME' => $_POST['name'],
                        'DESCRIPTION' => $_POST['description'],
                        'SLUG' => $_POST['slug']
                    );

                    //Update Headers
                    if ($ArticleContent->updateHeaders($headers)) {

                        clearPageCache(APP_LANG, $_POST['slug'] . '.php');

                        //Delete post data
                        unset($_POST);
                        setPostResponse('Les en-têtes de l\'article ont été mises à jour', 'success');

                    } else {

                        setPostResponse('Un problème est survenu lors de la mise à jour des en-têtes de l\'article');
                    }
                } else {

                    setPostResponse('Un problème est survenu lors de la mise à jour du statut de l\'article');
                }
            } else {
                setPostResponse('Cet article n\'existe pas');
            }
        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['SAVEARTICLECONTENT'])) {

        if (!empty($_POST['articleContent']) && !empty($_POST['articleId'])) {

            $ArticleContent = new ArticleContent($_POST['articleId'], 'BODY', APP_LANG);
            $ArticleContent->setContent($_POST['articleContent']);
            if (!empty($ArticleContent->getId())) {
                if ($ArticleContent->update()) {

                    setPostResponse('Le contenu de l\'article a été mis à jour', 'success');
                }
            } else {
                if ($ArticleContent->save()) {

                    setPostResponse('Le contenu de l\'article a été enregistré', 'success');
                }
            }

            $CategoryRelation = new CategoryRelations('ITEMGLUE', $_POST['articleId']);
            $allCategories = $CategoryRelation->getData();
            $allSimpleCategories = extractFromObjToSimpleArr($allCategories, 'id', 'categoryId');

            if (!empty($_POST['categories'])) {

                if (!is_null($allCategories)) {
                    foreach ($allCategories as $category) {
                        if (!in_array($category->categoryId, $_POST['categories'])) {
                            $CategoryRelation->setId($category->id);
                            $CategoryRelation->delete();
                        }
                    }
                }

                foreach ($_POST['categories'] as $chosenCategory) {
                    if (!in_array($chosenCategory, $allSimpleCategories)) {
                        $CategoryRelation->setCategoryId($chosenCategory);
                        $CategoryRelation->save();
                    }
                }

            } else {

                if (!is_null($allCategories)) {
                    foreach ($allCategories as $category) {
                        $CategoryRelation->setId($category->id);
                        $CategoryRelation->delete();
                    }
                }
            }

            //Delete post data
            unset($_POST);

        } else {

            setPostResponse('Le contenu de l\'article est obligatoire');
        }
    }

    if (isset($_POST['RELATIONUSERS'])) {

        if (!empty($_POST['articleId'])) {

            $ArticleRelation = new ArticleRelation($_POST['articleId'], 'USERS');

            $allRelations = $ArticleRelation->getData();
            $allSimpleRelations = extractFromObjToSimpleArr($allRelations, 'id', 'typeId');

            if (!empty($_POST['userRelation'])) {

                if (!is_null($allRelations)) {
                    foreach ($allRelations as $relation) {
                        if (!in_array($relation->typeId, $_POST['userRelation'])) {
                            $ArticleRelation->setId($relation->id);
                            $ArticleRelation->delete();
                        }
                    }
                }

                foreach ($_POST['userRelation'] as $chosenUser) {
                    if (!in_array($chosenUser, $allSimpleRelations)) {
                        $ArticleRelation->setType('USERS');
                        $ArticleRelation->setTypeId($chosenUser);
                        $ArticleRelation->save();
                    }
                }

            } else {

                if (!is_null($allRelations)) {
                    foreach ($allRelations as $relation) {
                        $ArticleRelation->setId($relation->id);
                        $ArticleRelation->delete();
                    }
                }
            }

            //Delete post data
            unset($_POST);

            setPostResponse('Les modifications ont été enregistrées', 'success');

        } else {
            setPostResponse('Vous devez sélectionner un utilisateur à associer à l\'article !');
        }

        $contentTabActive = false;
        $relationActive = true;
    }

    if (isset($_POST['ADDIMAGESTOARTICLE']) && !empty($_POST['articleId'])) {

        $html = '';
        $selectedFilesCount = 0;

        $ArticleMedia = new ArticleMedia($_POST['articleId']);
        $ArticleMedia->setUserId(getUserIdSession());

        //Get uploaded files
        if (!empty($_FILES)) {
            $ArticleMedia->setUploadFiles($_FILES['inputFile']);
            $files = $ArticleMedia->upload();
            $html .= trans('Fichiers importés') . ' : <strong>' . $files['countUpload'] . '</strong><br>'
                . trans('Fichiers enregistrés dans la BDD') . ' : <strong>' . $files['countDbSaved'] . '</strong><br>'
                . (!empty($files['errors']) ? '<br><span class="text-danger">' . $files['errors'] . '</span>' : '');
        }

        //Get selected files
        if (!empty($_POST['textareaSelectedFile'])) {

            $selectedFiles = $_POST['textareaSelectedFile'];

            if (strpos($selectedFiles, '|||')) {
                $files = explode('|||', $selectedFiles);
            } else {
                $files = array($selectedFiles);
            }

            foreach ($files as $key => $file) {
                $ArticleMedia->setName($file);
                if ($ArticleMedia->save()) $selectedFilesCount++;
            }

            $html .= trans('Fichiers sélectionnés enregistrés') . ' <strong>' . $selectedFilesCount . '</strong>.';
        }

        setPostResponse($html, 'info');

        $contentTabActive = false;
        $mediaTabactive = true;
    }
}