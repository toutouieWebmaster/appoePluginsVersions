<?php
const GLUECARD_PATH = WEB_PLUGIN_PATH . 'glueCard/';
const GLUECARD_URL = WEB_PLUGIN_URL . 'glueCard/';

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_glueCard_handles',
	TABLEPREFIX.'appoe_plugin_glueCard_plans',
	TABLEPREFIX.'appoe_plugin_glueCard_items',
	TABLEPREFIX.'appoe_plugin_glueCard_contents'
);

const GLUECARD_ITEMS_STATUS = array(
    2 => 'En vedette',
    1 => 'Publié',
    0 => 'Archive'
);