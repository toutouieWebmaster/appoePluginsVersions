<?php
const SHOP_PATH = WEB_PLUGIN_PATH . 'shop/';
const SHOP_URL = WEB_PLUGIN_URL . 'shop/';

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_shop_commandes',
	TABLEPREFIX.'appoe_plugin_shop_commandes_details',
	TABLEPREFIX.'appoe_plugin_shop_products',
	TABLEPREFIX.'appoe_plugin_shop_products_content',
	TABLEPREFIX.'appoe_plugin_shop_products_meta',
	TABLEPREFIX.'appoe_plugin_shop_stock'
);

const PRODUCT_STATUS = array(
    2 => 'En vedette',
    1 => 'Publié',
    0 => 'Archive'
);

const TYPE_PRODUCT = array(
    1 => 'Matérialisé',
    2 => 'Téléchargeable'
);

const DELIVERY_STATE = array(
    1 => 'En préparation',
    2 => 'Envoyé'
);

const ORDER_STATUS = array(
    1 => 'Annulé',
    2 => 'En cours',
    3 => 'Confirmé'
);
