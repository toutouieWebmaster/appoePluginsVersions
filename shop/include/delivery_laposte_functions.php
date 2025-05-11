<?php
function calculeTva_laposte($valeurInitiale)
{
    return $valeurInitiale * (1 + (5.5 / 100));
}

function getPriceFromGrid_laposte($grid, $poids, $tva = 0)
{
    foreach ($grid as $grammes => $euros) {
        if ($poids <= $grammes) {
            return ($tva == 0) ? $euros : calculeTva_laposte($euros);
        }
    }

    return false;
}

function calculeTransport_laposte($country, $poids = null, $dimension = 5)
{
    if (array_key_exists($country, listPays())) {

        if ($poids > 30000) {
            return 'Le poids maximale autorisé pour une livraison est de 30 kilos !';
        }

        if ($poids == 0) {
            return 0;
        }

        return match ($country) {
            'FR' => getPriceByZone_laposte('FR', $poids, $dimension),
            'GP', 'MQ', 'GY', 'YT', 'RE', 'PM', 'SM' => getPriceByZone_laposte('OM1', $poids, $dimension),
            'NC', 'PF', 'WF' => getPriceByZone_laposte('OM2', $poids, $dimension),
            'DE', 'BE', 'LU', 'NL' => getPriceByZone_laposte(1, $poids, $dimension),
            'AT', 'ES', 'IE', 'IT', 'PT', 'GB', 'VA' => getPriceByZone_laposte(2, $poids, $dimension),
            'DK', 'EE', 'HU', 'LV', 'LT', 'PL', 'CZ', 'SK', 'SE', 'CH' => getPriceByZone_laposte(3, $poids, $dimension),
            'FI', 'GR', 'IS', 'MA', 'TN', 'DZ', 'MT', 'NO', 'LY', 'TR', 'BG', 'RO', 'UA' => getPriceByZone_laposte(4, $poids, $dimension),
            'AU', 'CA', 'C2', 'KR', 'US', 'HK', 'IN', 'IL', 'JP', 'RU', 'SG', 'TH', 'VN' => getPriceByZone_laposte(5, $poids, $dimension),
            default => false,
        };

    }

    return false;
}

function getPriceByZone_laposte($zone, $poids, $dimension)
{

    //Lettre / Petit paquet
    if ($dimension <= 3 && ($poids <= 2000 || ($poids <= 3000 && $zone == 'FR'))) {

        switch ($zone) {

            case 'FR':
                return getPriceFromGrid_laposte(prixLettreFR_laposte(), $poids);

            case 'OM1':
            case 'OM2':
            case 1:
            case 2:
            case 3:
            case 4:
                return getPriceFromGrid_laposte(prixPetitPaquetZone1_laposte(), $poids, 1);

            case 5:
            case 6:
                return getPriceFromGrid_laposte(prixPetitPaquetZone2_laposte(), $poids, 1);

        }

    } else {

        //Colis
        return match ($zone) {
            'FR' => getPriceFromGrid_laposte(prixColisFR_laposte(), $poids, 1),
            'OM1' => getPriceFromGrid_laposte(prixColisOM1_laposte(), $poids, 1),
            'OM2' => getPriceFromGrid_laposte(prixColisOM2_laposte(), $poids, 1),
            1 => getPriceFromGrid_laposte(prixColisZone1_laposte(), $poids, 1),
            2 => getPriceFromGrid_laposte(prixColisZone2_laposte(), $poids, 1),
            3 => getPriceFromGrid_laposte(prixColisZone3_laposte(), $poids, 1),
            4 => getPriceFromGrid_laposte(prixColisZone4_laposte(), $poids, 1),
            5 => getPriceFromGrid_laposte(prixColisZone5_laposte(), $poids, 1),
            6 => getPriceFromGrid_laposte(prixColisZone6_laposte(), $poids, 1),
            default => false,
        };

    }

    return false;
}

function prixColisFR_laposte()
{
    return array(
        250 => 5.41,
        500 => 6.10,
        750 => 6.83,
        1000 => 7.41,
        2000 => 8.21,
        3000 => 9.01,
        4000 => 9.83,
        5000 => 10.63,
        6000 => 11.45,
        7000 => 12.25,
        8000 => 13.05,
        9000 => 13.87,
        10000 => 14.67,
        11000 => 15.49,
        12000 => 16.29,
        13000 => 17.10,
        14000 => 17.91,
        15000 => 18.71,
        16000 => 19.53,
        17000 => 20.33,
        18000 => 21.14,
        19000 => 21.95,
        20000 => 22.75,
        21000 => 23.57,
        22000 => 24.37,
        23000 => 25.18,
        24000 => 25.99,
        25000 => 26.79,
        26000 => 27.61,
        27000 => 28.41,
        28000 => 29.22,
        29000 => 30.03,
        30000 => 30.83,
    );
}

function prixColisOM1_laposte()
{
    return array(
        500 => 8.85,
        1000 => 13.43,
        2000 => 18.29,
        3000 => 23.17,
        4000 => 28.05,
        5000 => 32.92,
        6000 => 37.81,
        7000 => 42.68,
        8000 => 47.73,
        9000 => 53.43,
        10000 => 59.23,
        15000 => 81.03,
        20000 => 109.03,
        25000 => 137.75,
        30000 => 166.46
    );
}

function prixColisOM2_laposte()
{
    return array(
        500 => 10.52,
        1000 => 16.33,
        2000 => 29.02,
        3000 => 41.72,
        4000 => 54.42,
        5000 => 67.11,
        6000 => 79.81,
        7000 => 92.51,
        8000 => 105.20,
        9000 => 177.90,
        10000 => 130.59,
        15000 => 194.84,
        20000 => 259.08,
        25000 => 323.33,
        30000 => 387.56
    );
}

function prixColisZone1_laposte()
{
    return array(
        500 => 10.03,
        1000 => 11.80,
        2000 => 12.50,
        3000 => 13.26,
        4000 => 14.01,
        5000 => 14.17,
        6000 => 15.43,
        7000 => 16.13,
        8000 => 16.83,
        9000 => 17.54,
        10000 => 18.24,
        15000 => 22.28,
        20000 => 26.31,
        25000 => 30.34,
        30000 => 34.37
    );
}

function prixColisZone2_laposte()
{
    return array(
        500 => 11.22,
        1000 => 13.73,
        2000 => 15.13,
        3000 => 15.98,
        4000 => 16.73,
        5000 => 17.54,
        6000 => 18.29,
        7000 => 19.16,
        8000 => 19.91,
        9000 => 20.66,
        10000 => 21.42,
        15000 => 25.61,
        20000 => 29.69,
        25000 => 33.77,
        30000 => 37.85
    );
}

function prixColisZone3_laposte()
{
    return array(
        500 => 11.27,
        1000 => 13.77,
        2000 => 15.28,
        3000 => 16.57,
        4000 => 18.13,
        5000 => 19.73,
        6000 => 25.66,
        7000 => 27.06,
        8000 => 28.37,
        9000 => 29.69,
        10000 => 30.99,
        15000 => 38.40,
        20000 => 45.72,
        25000 => 53.07,
        30000 => 55.17
    );
}

function prixColisZone4_laposte()
{
    return array(
        500 => 13.82,
        1000 => 16.45,
        2000 => 17.89,
        3000 => 19.43,
        4000 => 21.50,
        5000 => 25.58,
        6000 => 28.69,
        7000 => 32.40,
        8000 => 36.91,
        9000 => 39.52,
        10000 => 42.73,
        15000 => 57.07,
        20000 => 66.60,
        25000 => 78.53,
        30000 => 90.77
    );
}

function prixColisZone5_laposte()
{
    return array(
        500 => 21.12,
        1000 => 23.35,
        2000 => 32.02,
        3000 => 42.49,
        4000 => 45.00,
        5000 => 47.32,
        6000 => 65.56,
        7000 => 73.63,
        8000 => 81.59,
        9000 => 85.02,
        10000 => 89.27,
        15000 => 133.48,
        20000 => 143.13,
        25000 => 183.74,
        30000 => 211.75
    );
}

function prixColisZone6_laposte()
{
    return array(
        500 => 24.41,
        1000 => 28.71,
        2000 => 39.43,
        3000 => 50.36,
        4000 => 61.07,
        5000 => 70.22,
        6000 => 80.67,
        7000 => 91.22,
        8000 => 101.67,
        9000 => 112.38,
        10000 => 122.83,
        15000 => 170.20,
        20000 => 218.59,
        25000 => 265.95,
        30000 => 313.31
    );
}

function prixLettreFR_laposte()
{
    return array(
        20 => 1.10,
        50 => 1.56,
        100 => 2.10,
        250 => 3.24,
        500 => 4.28,
        1000 => 5.41,
        2000 => 6.81,
        3000 => 7.47
    );
}


function prixPetitPaquetZone1_laposte()
{
    return array(
        50 => 6.43,
        100 => 7.07,
        250 => 9.20,
        500 => 11.98,
        750 => 13.20,
        1000 => 15.28,
        2000 => 18.69
    );
}

function prixPetitPaquetZone2_laposte()
{
    return array(
        50 => 6.97,
        100 => 7.60,
        250 => 10.96,
        500 => 14.11,
        750 => 16.51,
        1000 => 17.52,
        2000 => 24.18
    );
}