<?php
const ITEMGLUE_PATH = WEB_PLUGIN_PATH . 'itemGlue/';
const ITEMGLUE_URL = WEB_PLUGIN_URL . 'itemGlue/';

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_itemGlue_articles',
	TABLEPREFIX.'appoe_plugin_itemGlue_articles_content',
	TABLEPREFIX.'appoe_plugin_itemGlue_articles_meta',
	TABLEPREFIX.'appoe_plugin_itemGlue_articles_relations'
);

const ITEMGLUE_ARTICLES_STATUS = array(
    2 => 'En vedette',
    1 => 'Publié',
    0 => 'Archive'
);