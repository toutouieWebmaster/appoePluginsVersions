<?php

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsTemplate;

if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . 'static/');
}

/**
 * Load page as include
 *
 * @param string $slug
 * @param string $folder
 * @param string $lang
 * @return mixed|string
 */
function loadPage($slug = 'home', $folder = 'html', $lang = LANG)
{
    $Cms = new Cms();

    //Get Page parameters
    $existPage = $Cms->showBySlug($slug, $lang);

    //Check if Page exist and accessible
    if (!$existPage || $Cms->getStatut() != 1) {
        if (!inc(WEB_PUBLIC_PATH . $folder . DIRECTORY_SEPARATOR . $slug . '.php')) {
            return 'La page ' . $slug . ' n\'existe pas.';
        }
        return '';
    }

    $CmsContent = new CmsContent($Cms->getId(), $lang);

    //Get page content in template
    $Template = new CmsTemplate(WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData(), true);
    return $Template->get();
}

/**
 * Display file as include
 *
 * @param string $filename
 * @param string $folder
 * @param string $lang
 * @return mixed|string
 */
function LoadFile($filename = 'index', $folder = 'html', $lang = LANG)
{
    $Cms = new Cms();

    //Get file parameters
    $Cms->setFilename($filename);
    $Cms->setLang($lang);
    $existPage = $Cms->showByFilename();

    //Check if file exist and accessible
    if (!$existPage || $Cms->getStatut() != 1) {
        if (!inc(WEB_PUBLIC_PATH . $folder . DIRECTORY_SEPARATOR . $filename . '.php')) {
            return 'Le fichier ' . $filename . ' n\'existe pas.';

        }
        return '';
    }

    $CmsContent = new CmsContent($Cms->getId(), $lang);

    //Get file content in template
    $Template = new CmsTemplate(WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData(), true);
    return $Template->get();
}

/**
 * Load Page from public folder with param in array, who key is the name of the zone and value to replace it
 * @param string $pathFromPublic
 * @param array $params
 * @example loadPageContent("html/header.php", ['en-tete' => $Article->getName()]);
 */
function loadPageContent(string $pathFromPublic, array $params)
{
    $path = WEB_PUBLIC_PATH . $pathFromPublic;

    ob_start();
    inc($path);
    $pageContent = ob_get_clean();

    if (preg_match_all("/{{(.*?)}}/", $pageContent, $match)) {

        foreach ($match[1] as $i => $zone) {
            $zone = strpos($zone, '_') ? strstr($zone, '_', true) : $zone;
            $pageContent = str_replace($match[0][$i], sprintf('%s', !empty($params[$zone]) ? $params[$zone] : ''), $pageContent);
        }
    }

    echo trim($pageContent);
}

/**
 * @param $filename
 * @param mixed|string $lang
 * @return Cms|false
 */
function getPageByFilename($filename, $lang = LANG)
{
    if (!empty($filename)) {

        $Cms = new Cms();
        $Cms->setFilename($filename);
        $Cms->setLang($lang);

        //Get Page parameters
        if ($Cms->showByFilename()) {
            return $Cms;
        }
    }
    return false;
}

/**
 * Load plugin page with "loadPage"
 *
 * @param string $slug
 * @param string $lang
 * @return mixed|string
 */
function getPluginPage($slug = 'home', $lang = LANG)
{
    return loadPage($slug, 'plugins', $lang);
}

/**
 * Load cms headers
 *
 * @param $idCms
 * @param $lang
 * @return mixed
 */
function getCmsHeaders($idCms, $lang)
{
    $CmsContent = new CmsContent($idCms, $lang, true);
    return $CmsContent->getData();
}

/**
 * @param $idCms
 * @return Cms|bool
 */
function getCmsById($idCms)
{
    $Cms = new Cms();
    $Cms->setId($idCms);
    $Cms->setLang(LANG);
    if ($Cms->show()) {
        return $Cms;
    }
    return false;
}

/**
 * delete all cache in folders
 */
function clearCache()
{

    if (is_dir(CACHE_PATH)) {
        foreach (getLangs() as $lang => $language) {
            if (is_dir(CACHE_PATH . $lang)) {
                foreach (glob(CACHE_PATH . $lang . '/*') as $file) {
                    unlink($file);
                }
            }
        }

        return true;
    }

    return false;
}

/**
 * delete cache file
 *
 * @param $lang
 * @param $file
 * @return bool
 */
function clearPageCache($lang, $file)
{

    if (is_dir(CACHE_PATH . $lang)) {

        if (file_exists(CACHE_PATH . $lang . DIRECTORY_SEPARATOR . $file)) {
            unlink(CACHE_PATH . $lang . DIRECTORY_SEPARATOR . $file);
        }

        return true;
    }

    return false;
}