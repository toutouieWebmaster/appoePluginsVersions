<?php

/**
 * write on Map json file
 *
 * @param $data
 * @param $title
 * @return string
 */
function interMap_writeMapFile($data, $title)
{
    $json_file = fopen(WEB_PLUGIN_PATH . 'interactiveMap/' . slugify($title) . '.json', 'w');
    fwrite($json_file, $data);
    fclose($json_file);

    return true;
}

/**
 * read the Map json file
 *
 * @param $title
 * @return array
 */
function interMap_readMapFile($title)
{
    $json = file_get_contents(WEB_PLUGIN_PATH . 'interactiveMap/' . slugify($title) . '.json');
    return json_decode($json, true);
}

/**
 * get map
 *
 * @param $idMap
 * @return bool|object
 */
function interMap_get($idMap)
{
    $InteractiveMap = new \App\Plugin\InteractiveMap\InteractiveMap();
    $InteractiveMap->setId($idMap);

    if ($InteractiveMap->show()) {
        $InteractiveMap->setOptions(interMap_getOptionJSON($InteractiveMap->getOptions()));
        $InteractiveMap->setTitle(slugify($InteractiveMap->getTitle()));
        return $InteractiveMap;
    }
    return false;
}

/**
 * return all map options in Json
 *
 * @param $optionsJSON
 * @return string
 */
function interMap_getOptionJSON($optionsJSON)
{
    $optionsJSON = json_decode($optionsJSON, true);

    $dataJSON = [];
    if (isset($optionsJSON['checkbox'])) {
        foreach ($optionsJSON['checkbox'] as $checkbox) {
            $dataJSON[$checkbox] = true;
        }
    }

    unset($optionsJSON['checkbox']);
    return json_encode(array_merge($dataJSON, is_array($optionsJSON) ? $optionsJSON : []));
}