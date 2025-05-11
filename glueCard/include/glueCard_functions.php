<?php

use App\Plugin\GlueCard\Content;
use App\Plugin\GlueCard\Item;
use App\Plugin\GlueCard\Plan;

/**
 * @param $idHandle
 * @param array $options
 * @return array
 */
function getCardsByHandle($idHandle, array $options = [])
{
    $cards = [];

    $options = array_merge(array(
        'archives' => false,
        'count' => null,
        'lang' => APP_LANG
    ), $options);

    $Plan = new Plan();
    $Plan->setIdHandle($idHandle);
    $plans = $Plan->showByHandle();

    $Content = new Content();

    $Item = new Item();
    $Item->setIdHandle($idHandle);

    if ($options['count']) {
        $Item->setCount($options['count']);
    }

    if ($items = $Item->showByHandle()) {

        foreach ($items as $c => $item) {

            if (!$options['archives'] && $item->status == 0) {
                continue;
            }

            $Content->setIdItem($item->id);
            $Content->setLang($options['lang']);
            $content = extractFromObjArr($Content->showByItem(), 'id_plan');

            foreach ($plans as $plan) {
                $cards[$item->id][slugify($plan->name)] = !empty($content[$plan->id]) ? htmlSpeCharDecode($content[$plan->id]->text) : '';
            }
        }
    }
    return $cards;
}

/**
 * @param array $idHandles
 * @param array $options
 * @return array
 */
function getCardsByHandles(array $idHandles, array $options = [])
{
    $cards = [];
    foreach ($idHandles as $idHandle) {
        $cards[$idHandle] = getCardsByHandle($idHandle, $options);
    }
    return $cards;
}