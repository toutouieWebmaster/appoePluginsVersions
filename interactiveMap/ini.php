<?php
const INTERACTIVE_MAP_PATH = WEB_PLUGIN_PATH . 'interactiveMap/';
const INTERACTIVE_MAP_URL = WEB_PLUGIN_URL . 'interactiveMap/';

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_interactiveMap',
);

const INTERACTIVE_MAP_STATUS = array(
    0 => 'Non accessible',
    1 => 'Accessible'
);

const INTERACTIVE_MAP_PINS = array(
    'red' => 'pin.png',
    'orange' => 'pin-orange.png',
    'yellow' => 'pin-yellow.png',
    'green' => 'pin-green.png',
    'blue' => 'pin-blue.png',
    'purple' => 'pin-purple.png',
    'white' => 'pin-white.png',
    'circle' => 'circle',
    'circular' => 'circular',
    'transparent' => 'transparent',
    'iconpin' => 'iconpin'
);

const MAP_JS_OPTIONS = array(
    'sidebar' => 'Activer le SideBar',
    'search' => 'Activer la recherche',
    'minimap' => 'Afficher la mini-carte',
    'markers' => 'Afficher les marqueurs',
    'fullscreen' => 'Afficher les options plein écran',
    'zoombuttons' => 'Afficher les options zoom',
    'zoomoutclose' => 'Autoriser dézoomage automatique',
    'clearbutton' => 'Afficher les options de nettoyage',
    'mapfill' => 'Forcer le remplissage du container par la carte'
);

const MAP_JS_ACTIONS = array(
    'tooltip' => 'Info-bulle',
    'open-link' => 'Ouvrir le lien',
    'open-link-new-tab' => 'Ouvrir le lien, nouvel onglet',
    'lightbox' => 'Boite mise en avant',
    'none' => 'Sans'
);