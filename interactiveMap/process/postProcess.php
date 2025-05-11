<?php
require_once('../main.php');
require_once('../include/interMap_function.php');

use App\Plugin\InteractiveMap\InteractiveMap;
use App\Plugin\InteractiveMap\InterMapMedia;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDINTERMAP'])) {
        if (
            !empty($_POST['title'])
            && !empty($_POST['width'])
            && !empty($_POST['height'])
        ) {

            $jsonArray = json_encode(array(
                'mapwidth' => $_POST['width'],
                'mapheight' => $_POST['height'],
                'categories' => [],
                'levels' => []
            ));

            $InteractiveMap = new InteractiveMap();
            $InteractiveMap->feed($_POST);
            $InteractiveMap->setData($jsonArray);
            if (!$InteractiveMap->usedTitle($_POST['title'])) {
                if ($InteractiveMap->save()) {

                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());

                    //Delete post data
                    unset($_POST);
                    setPostResponse('La nouvelle carte a été enregistrée', 'success');

                } else {
                    setPostResponse('Un problème est survenu lors de l\'enregistrement de la carte');
                }
            } else {
                setPostResponse('Ce titre est déjà utilisé');
            }
        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['UPDATEINTERACTIVECARTE'])) {
        if (
            !empty($_POST['id'])
            && !empty($_POST['title'])
            && !empty($_POST['width'])
            && !empty($_POST['height'])
            && isset($_POST['status'])
        ) {
            $InteractiveMap = new InteractiveMap();
            $InteractiveMap->feed($_POST);
            if ($InteractiveMap->notExist(true)) {
                if ($InteractiveMap->update()) {

                    //Delete post data
                    unset($_POST);

                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    setPostResponse('La carte a été enregistrée', 'success');

                } else {
                    setPostResponse('Le titre de cette carte existe déjà');
                }
            }
        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['ADDINTERMAPLEVEL'])) {
        if (!empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['title'])
            && isset($_FILES)
        ) {
            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);
            $access = true;

            $File = new InterMapMedia($_POST['idMap']);
            $File->setUserId(getUserIdSession());

            if (!empty($_FILES['map']['name'])) {
                $File->setUploadFiles($_FILES['map']);
                $mapFile = $File->upload();
            }

            if (!empty($_FILES['minimap']['name'])) {
                $File->setUploadFiles($_FILES['minimap']);
                $minimapFile = $File->upload();
            }

            $newLevel = array(
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'map' => !empty($mapFile['filename'][0]) ? WEB_DIR_INCLUDE . $mapFile['filename'][0] : '',
                'minimap' => !empty($minimapFile['filename'][0]) ? WEB_DIR_INCLUDE . $minimapFile['filename'][0] : '',
                'locations' => []
            );

            foreach ($map['levels'] as $level) {
                if ($_POST['id'] == $level['id'] ||
                    $_POST['title'] == $level['id']) {
                    setPostResponse('Ce niveau existe déjà');

                    $access = false;
                    break;
                }
            }

            if ($access) {
                $map['levels'][] = $newLevel;

                $InteractiveMap->setData(json_encode($map));
                if ($InteractiveMap->updateData()) {

                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    setPostResponse('Le niveau a été enregistré', 'success');
                }
            }

        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['ADDOPTIONS'])) {
        if (!empty($_POST['idMap'])
            && !empty($_POST['action'])
            && !empty($_POST['maxscale'])
            && isset($_POST['mapfill'])
        ) {

            $InteractiveMap = new InteractiveMap($_POST['idMap']);
            $data = [];

            //get options checkbox
            if (isset($_POST['options'])) {
                $data['checkbox'] = $_POST['options'];
            }

            $data['action'] = $_POST['action'];
            $data['maxscale'] = $_POST['maxscale'];
            $data['mapfill'] = !empty($_POST['mapfill']) ? $_POST['mapfill'] : '';

            $InteractiveMap->setOptions(json_encode($data));

            if ($InteractiveMap->update()) {
                setPostResponse('Les options ont été enregistrées', 'success');
            } else {
                setPostResponse('Une erreur s\'est produite');
            }

        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }
}