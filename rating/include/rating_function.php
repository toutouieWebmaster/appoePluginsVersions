<?php

use App\Plugin\Cms\Cms;
use App\Plugin\ItemGlue\Article;
use App\Plugin\Rating\Rating;
use App\Plugin\Shop\Product;

function getAverage($data)
{
    return array_sum($data) / count($data);
}

function getRate($dbRates, $moreVotes = 0, $moreSum = 0)
{
    $data = [];
    $numberVotes = count($dbRates) + $moreVotes;
    $sum = $moreSum;

    if ($numberVotes > 0) {
        foreach ($dbRates as $key => $values) {
            $sum += $values->score;
        }

        $data['number_votes'] = $numberVotes;
        $data['total_points'] = $sum;
        $data['dec_avg'] = round($data['total_points'] / $data['number_votes'], 1);
        $data['whole_avg'] = round($data['dec_avg']);
    }
    return $data;
}

function getAllRates($status = 1)
{

    $Rating = new Rating();
    $allRating = $Rating->showAll(false, $status);

    $types = [];
    foreach ($allRating as $rating) {

        if (!array_key_exists($rating->type, $types)) {
            $types[$rating->type] = [];
        }

        if (!array_key_exists($rating->typeId, $types[$rating->type])) {
            $types[$rating->type][$rating->typeId]['score'] = 0;
            $types[$rating->type][$rating->typeId]['nbVotes'] = 0;
            $types[$rating->type][$rating->typeId]['average'] = 0;
        }

        $types[$rating->type][$rating->typeId]['score'] += $rating->score;
        $types[$rating->type][$rating->typeId]['nbVotes']++;
        $types[$rating->type][$rating->typeId]['average'] = round(
            $types[$rating->type][$rating->typeId]['score'] / $types[$rating->type][$rating->typeId]['nbVotes'], 1);
    }

    return $types;
}

function getUnconfirmedRates()
{
    $Rating = new Rating();
    return $Rating->showAll(false, 0);
}

/**
 * @param $type
 * @param $typeId
 * @param bool $clicable
 * @param string $sizeClass (largeStars | mediumStars | littleStars)
 * @param bool $minimize
 * @return string
 */
function showRatings($type, $typeId, $clicable = true, $sizeClass = 'largeStars', $minimize = false)
{
    $html = '<div class="movie_choice" id="' . $type . '_' . $typeId . '">
                <div id="' . strtoupper($type) . '-item-' . $typeId . '" data-type="' . $type . '" class="rate_widget" data-idstars="' . $type . '_' . $typeId . '">
                    <div class="star_1 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_2 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_3 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_4 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>
                    <div class="star_5 ratings_stars ' . ($clicable ? ' starClick ' : '') . $sizeClass . '"></div>';
    if (!$minimize) {
        $html .= '<div class="total_votes" >...</div>';
    }
    $html .= '</div></div>';

    return $html;
}

function getObj($type)
{

    return match ($type) {
        'ITEMGLUE' => new Article(),
        'CMS' => new Cms(),
        'SHOP' => new Product(),
        default => false,
    };
}