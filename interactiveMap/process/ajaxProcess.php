<?php

use App\Plugin\InteractiveMap\InteractiveMap;
use App\Plugin\InteractiveMap\InterMapMedia;

require_once('../main.php');
require_once('../include/interMap_function.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        //Add new map
        if (!empty($_POST['addMapCategory'])
            && !empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['title'])
            && !empty($_POST['color'])
            && !empty($_POST['show'])
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            $_POST['id'] = slugify($_POST['id']);

            $newCategory = array(
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'color' => $_POST['color'],
                'show' => $_POST['show']
            );

            //check if category exist
            foreach ($map['categories'] as $category) {
                if ($_POST['id'] == $category['id'] ||
                    $_POST['title'] == $category['id']) {
                    echo trans('Cette catégorie existe déjà');
                    exit();
                }
            }

            $map['categories'][] = $newCategory;
            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('La nouvelle catégorie a été enregistrée');
            }
            exit();
        }

        //Archive Map
        if (!empty($_POST['idMapDelete'])) {
            $InteractiveMap = new InteractiveMap($_POST['idMapDelete']);

            $InteractiveMap->setStatus(0);
            if ($InteractiveMap->update()) {
                echo 'true';
            }
            exit();
        }

        //Update Map details
        if (
            !empty($_POST['updateInterMap'])
            && !empty($_POST['idMap'])
            && !empty($_POST['parent'])
            && !empty($_POST['id'])
            && !empty($_POST['name'])
            && isset($_POST['value'])
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            //get details
            foreach ($map[$_POST['parent']] as $key => $category) {
                if ($_POST['id'] == $category['id']) {
                    $map[$_POST['parent']][$key][$_POST['name']] = $_POST['value'];
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo 'true';
            }
            exit();
        }

        //Delete primary details form Map
        if (
            !empty($_POST['deleteInterMapArr'])
            && !empty($_POST['idMap'])
            && !empty($_POST['parent'])
            && !empty($_POST['id'])
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            //get details
            foreach ($map[$_POST['parent']] as $key => $category) {
                if ($_POST['id'] == $category['id']) {
                    unset($map[$_POST['parent']][$key]);
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo 'true';
            }
            exit();
        }

        // Add map location
        if (!empty($_POST['addMapLocation'])
            && !empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['currentLevel'])
            && !empty($_POST['xPoint'])
            && !empty($_POST['yPoint'])
            && isset($_POST['title'])
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            $newPoint = array(
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'about' => '',
                'description' => '',
                'category' => '',
                'thumbnail' => '',
                'x' => $_POST['xPoint'],
                'y' => $_POST['yPoint'],
                'fill' => '',
                'pin' => 'hidden'
            );

            $fountCount = 0;
            foreach ($map['levels'] as $key => $level) {
                if ($level['id'] == $_POST['currentLevel']) {
                    foreach ($map['levels'][$key]['locations'] as $locKey => $location) {
                        if ($_POST['id'] == $location['id']) {
                            $fountCount++;
                            break;
                        }
                    }
                    break;
                }
            }

            if ($fountCount == 0) {
                $map['levels'][$key]['locations'][] = $newPoint;

                $InteractiveMap->setData(json_encode($map));
                if ($InteractiveMap->updateData()) {
                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    echo 'true';
                }
            } else {
                echo 'false';
            }
            exit();
        }

        //Update Map location details
        if (!empty($_POST['updateMapLocation'])
            && !empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['level'])
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            foreach ($map['levels'] as $firstKey => $level) {
                if ($level['id'] == $_POST['level']) {
                    foreach ($map['levels'][$firstKey]['locations'] as $key => $location) {
                        if ($location['id'] == $_POST['id']) {
                            $map['levels'][$firstKey]['locations'][$key]['title'] = !empty($_POST['title']) ? $_POST['title'] : '';
                            $map['levels'][$firstKey]['locations'][$key]['about'] = !empty($_POST['about']) ? $_POST['about'] : '';
                            $map['levels'][$firstKey]['locations'][$key]['description'] = !empty($_POST['description']) ? $_POST['description'] : '';
                            $map['levels'][$firstKey]['locations'][$key]['category'] = !empty($_POST['category']) ? $_POST['category'] : '';
                            $map['levels'][$firstKey]['locations'][$key]['pin'] = !empty($_POST['pin']) ? $_POST['pin'] : 'hidden';
                            $map['levels'][$firstKey]['locations'][$key]['fill'] = !empty($_POST['fill']) ? $_POST['fill'] : '';
                            break;
                        }
                    }
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('Le point a été mis à jour');
            }
            exit();
        }

        // Delete Map location
        if (!empty($_POST['deleteMapLocation'])
            && !empty($_POST['idMap'])
            && !empty($_POST['locationId'])
            && !empty($_POST['level'])
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            foreach ($map['levels'] as $firstKey => $level) {
                if ($level['id'] == $_POST['level']) {
                    foreach ($map['levels'][$firstKey]['locations'] as $key => $location) {
                        if ($location['id'] == $_POST['locationId']) {
                            unset($map['levels'][$firstKey]['locations'][$key]);
                            break;
                        }
                    }
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('Le point a été supprimé');
            }
            exit();
        }

        //Restart Map details
        if (!empty($_POST['rebootInterMap']) && !empty($_POST['idMap'])) {

            $InteractiveMap = new InteractiveMap($_POST['idMap']);

            $jsonArray = json_encode(array(
                'mapwidth' => $InteractiveMap->getWidth(),
                'mapheight' => $InteractiveMap->getHeight(),
                'categories' => [],
                'levels' => []
            ));

            $InteractiveMap->setData($jsonArray);
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo 'true';
            }
            exit();
        }

        // Add / Update location's thumbnail
        if (isset($_GET['uploadThumbnail'])
            && !empty($_GET['idMap'])
            && !empty($_GET['level'])
            && !empty($_GET['idLocation'])) {

            $File = new InterMapMedia($_GET['idMap']);
            $File->setUserId(getUserIdSession());

            $arrayFiles = [];
            foreach ($_FILES as $file) {
                $arrayFiles = array(
                    'name' => array($file['name']),
                    'type' => array($file['type']),
                    'tmp_name' => array($file['tmp_name']),
                    'error' => array($file['error']),
                    'size' => array($file['size']),
                );

                $File->setUploadFiles($arrayFiles);
                $thumbnail = $File->upload();
            }

            $thumbnailSrc = !empty($thumbnail['filename'][0]) ? WEB_DIR_INCLUDE . $thumbnail['filename'][0] : '';

            $InteractiveMap = new InteractiveMap($_GET['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            foreach ($map['levels'] as $firstKey => $level) {
                if ($level['id'] == $_GET['level']) {
                    foreach ($map['levels'][$firstKey]['locations'] as $key => $location) {
                        if ($location['id'] == $_GET['idLocation']) {
                            $map['levels'][$firstKey]['locations'][$key]['thumbnail'] = $thumbnailSrc;
                            break;
                        }
                    }
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('L\'image a été enregistrée');
            }
            exit();
        }
    }
}