<?php

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsMenu;

require_once('../include/cms_functions.php');

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDPAGE'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['slug'])
            && !empty($_POST['description'])
            && !empty($_POST['menuName'])
            && !empty($_POST['filename'])
            && !empty($_POST['type'])
        ) {

            $Cms = new Cms();

            //Add Page
            $Cms->setFilename($_POST['filename']);
            $Cms->setType($_POST['type']);

            if ($Cms->notExist()) {
                if ($Cms->save()) {

                    $CmsContent = new CmsContent();
                    $CmsContent->setIdCms($Cms->getId());
                    $CmsContent->saveHeaders($_POST);

                    //Delete post data
                    unset($_POST);

                    setPostResponse('La page a été enregistrée', 'success', ('<a href="' . getPluginUrl('cms/page/pageContent/', $Cms->getId()) . '">' . trans('Voir la page') . '</a>'));
                } else {
                    setPostResponse('Un problème est survenu lors de l\'enregistrement de la page');
                }
            } else {
                setPostResponse('Le fichier est utilisé pour une autre page');
            }
        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEPAGE'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['name'])
            && !empty($_POST['description'])
            && !empty($_POST['menuName'])
            && !empty($_POST['slug'])
        ) {

            $cmsUpdate = true;
            if (!empty($_POST['filename']) && !empty($_POST['type'])) {

                $cmsUpdate = false;

                $Cms = new Cms($_POST['id']);
                if ($Cms->getFilename() != $_POST['filename'] || $Cms->getType() != $_POST['type']) {

                    $Cms->setFilename($_POST['filename']);
                    $Cms->setType($_POST['type']);

                    if ($Cms->notExist(true)) {
                        if (!$Cms->update()) {
                            $cmsUpdate = false;
                        } else {
                            $cmsUpdate = true;
                        }
                    }
                } else {
                    $cmsUpdate = true;
                }
            }

            if ($cmsUpdate) {

                clearPageCache(APP_LANG, $_POST['slug'] . '.php');


                $CmsContent = new CmsContent();
                $CmsContent->setIdCms($_POST['id']);
                $CmsContent->setType('HEADER');
                $CmsContent->setMetaKey('name');
                $CmsContent->setLang(APP_LANG);

                if ($CmsContent->notExist()) {

                    if ($CmsContent->saveHeaders($_POST)) {

                        //Delete post data
                        unset($_POST);

                        setPostResponse('Les en-têtes ont été enregistrées', 'success');
                    } else {
                        setPostResponse('Un problème est survenu lors de la création des en-têtes de la page');
                    }

                } else {
                    if ($CmsContent->updateHeaders($_POST)) {

                        //Delete post data
                        unset($_POST);

                        setPostResponse('Les en-têtes ont été mises à jour', 'success');

                    } else {

                        setPostResponse('Un problème est survenu lors de la mise à jour des en-têtes de la page');
                    }
                }
            } else {

                setPostResponse('Le fichier est utilisé pour une autre page');
            }
        } else {

            setPostResponse('Tous les champs sont obligatoires');
        }
    }


    if (isset($_POST['ADDMENUPAGE'])) {
        if (!empty($_POST['parentId']) && !empty($_POST['location'])) {

            if (!empty($_POST['idArticle']) && !empty($_POST['slugArticlePage'])) {
                $_POST['idCms'] = $_POST['slugArticlePage'] . DIRECTORY_SEPARATOR . $_POST['idArticle'];
            }

            if (!empty($_POST['idCms'])) {

                if (!empty($_POST['radioBtnIdCMS']) && $_POST['radioBtnIdCMS'] == 'Page') {
                    $_POST['name'] = null;
                }

                //Add Menu
                $CmsMenu = new CmsMenu();
                $CmsMenu->feed($_POST);

                if ($CmsMenu->existParent() || $CmsMenu->getParentId() == 10) {

                    if ($CmsMenu->save()) {

                        //Delete post data
                        unset($_POST);

                        setPostResponse('Le menu a été enregistré', 'success');

                    } else {

                        setPostResponse('Un problème est survenu lors de l\'enregistrement du menu');
                    }
                } else {

                    setPostResponse('Ce menu n\'existe pas');
                }
            } else {

                setPostResponse('Aucune page n\'a été choisie');
            }
        } else {

            setPostResponse('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['UPDATEMENUPAGE'])) {
        if (!empty($_POST['id']) && !empty($_POST['parentId']) && !empty($_POST['location'])) {

            $CmsMenu = new CmsMenu($_POST['id']);

            if ($CmsMenu->existParent() || $CmsMenu->getParentId() == 10) {

                if ($CmsMenu->update()) {

                    //Delete post data
                    unset($_POST);

                    setPostResponse('Le menu a été mis à jour', 'success');

                } else {

                    setPostResponse('Un problème est survenu lors de la mise à jour du menu');
                }
            } else {

                setPostResponse('Ce menu n\'existe pas');
            }
        } else {

            setPostResponse('Tous les champs sont obligatoires');
        }
    }
}