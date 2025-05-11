<?php
const CMS_PATH = WEB_PLUGIN_PATH . 'cms/';
const CMS_URL = WEB_PLUGIN_URL . 'cms/';

if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . 'static/');
}

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_cms',
	TABLEPREFIX.'appoe_plugin_cms_menu',
	TABLEPREFIX.'appoe_plugin_cms_content',
);

const CMS_PAGE_STATUS = array(
    1 => 'Publié',
    0 => 'Archive'
);

const CMS_LOCATIONS = array(
    1 => 'Menu Principal',
    2 => 'Menu Secondaire',
    3 => 'Menu En tête 1',
    4 => 'Menu En tête 2',
    5 => 'Menu En tête 3',
    6 => 'Menu En tête 4',
    7 => 'Menu Pied de page 1',
    8 => 'Menu Pied de page 2',
    9 => 'Menu Pied de page 3',
    10 => 'Menu Pied de page 4',
    11 => 'Menu Latéral 1',
    12 => 'Menu Latéral 2',
    13 => 'Menu Latéral 3',
    14 => 'Menu Latéral 4',
    15 => 'Menu Special',
    16 => 'Menu Autre 1',
    17 => 'Menu Autre 2',
    18 => 'Menu Autre 3',
    19 => 'Menu Autre 4',
    20 => 'Menu Autre 5',
    21 => 'Menu Autre 6'
);

const CMS_TYPES = array(
    'PAGE',
    'HEADER',
    'FOOTER',
    'SIDEBAR',
    'INCLUDE CONTENT',
    'BLOG',
    'OTHER'
);