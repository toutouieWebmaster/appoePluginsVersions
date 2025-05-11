<?php

use App\Plugin\People\People;

/**
 *
 */
const PEOPLE_NATURE = array(
    1 => 'Société',
    2 => 'Monsieur',
    3 => 'Madame'
);

/**
 * @param array $excludesFields
 * @param array $dataFields
 * @param array $requiredFields
 * @param string $formName
 * @param bool $showType
 * @param bool $showSaveBtn
 * @return string
 * @throws \Random\RandomException
 */
function people_addPersonFormFields(array $excludesFields = [], array $dataFields = [], array $requiredFields = [], $formName = 'ADDPERSON', $showType = true, $showSaveBtn = true)
{
    //defaults fields
    $natureF = $nameF = $firstNameF = $birthDateF = $emailF = $telF = $addressF = $zipF = $cityF = $countryF = true;

    //defaults required fields
    $natureR = $nameR = $firstNameR = $birthDateR = $emailR = $telR = $addressR = $zipR = $cityR = $countryR = false;

    //default fields value
    $type = $nature = $name = $firstName = $birthDate = $email = $tel = $address = $zip = $city = '';
    $country = 'FR';

    //replaces defaults values from function's arguments
    extract($excludesFields);
    extract($dataFields);
    extract($requiredFields);

    //html form
    $html = getTokenField();
    $html .= '<div class="my-4"></div><div class="row">';

    if ($showType) {
        $html .= '<div class="col my-2">' . App\Form::text('Enregistrement de type', 'type', 'text', $type, true, 150, 'list="typeList" autocomplete="off"') . '</div>';

        $html .= '<datalist id="typeList">';
        foreach (getAppTypes() as $typeName) {
            $html .= '<option value="' . $typeName . '">' . $typeName . '</option>';
        }
        $html .= '</datalist>';
    }

    $html .= $natureF ? '<div class="col my-2">' . App\Form::select('Nature', 'nature', getPeopleNatureName(), $nature, $natureR) . '</div>' : '';
    $html .= $nameF ? '<div class="col my-2">' . App\Form::text('Nom', 'name', 'text', $name, $nameR, 150) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $firstNameF ? '<div class="col my-2">' . App\Form::text('Prénom', 'firstName', 'text', $firstName, $firstNameR, 150) . '</div>' : '';
    $html .= $birthDateF ? '<div class="col my-2">' . App\Form::text('Date de naissance', 'birthDate', 'date', $birthDate, $birthDateR, 10) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $emailF ? '<div class="col my-2">' . App\Form::text('Adresse Email', 'email', 'email', $email, $emailR, 255) . '</div>' : '';
    $html .= $telF ? '<div class="col my-2">' . App\Form::text('Téléphone', 'tel', 'tel', $tel, $telR, 10) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $addressF ? '<div class="col my-2">' . App\Form::text('Adresse postale', 'address', 'text', $address, $addressR, 255) . '</div>' : '';
    $html .= $zipF ? '<div class="col col-lg-3 my-2">' . App\Form::text('Code postal', 'zip', 'tel', $zip, $zipR, 7) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $cityF ? '<div class="col my-2">' . App\Form::text('Ville', 'city', 'text', $city, $cityR, 100) . '</div>' : '';
    $html .= $countryF ? '<div class="col my-2">' . App\Form::select('Pays', 'country', listPays(), $country, $countryR) . '</div>' : '';

    $html .= '</div>';
    $html .= App\Form::target($formName);
    $html .= $showSaveBtn ? '<div class="my-2"><div class="row"><div class="col-12">' . App\Form::submit('Enregistrer', $formName . 'SUBMIT') . '</div></div>' : '';

    return $html;
}

/**
 * @return array
 */
function getPeopleNatureName()
{
    return PEOPLE_NATURE;
}

/**
 * @param $natureId
 * @return bool|mixed
 */
function getPeopleNatureNameById($natureId)
{
    if (array_key_exists($natureId, getPeopleNatureName())) {
        return PEOPLE_NATURE[$natureId];
    }
    return $natureId;
}

/**
 * @param $type
 * @param array $data
 * @return array|int
 */
function getPeopleData($type = '', array $data = array('name'))
{

    $People = new People();
    $People->setType($type);
    return clearPeopleData($People->showDataForExport($data));
}

/**
 * @return array|int
 */
function getPeopleTypes()
{

    $People = new People();
    return $People->showTypes();
}

/**
 * @param $data
 * @return mixed
 */
function clearPeopleData($data)
{

    foreach ($data as &$people) {

        if (is_object($people)) {
            $people->nature = getPeopleNatureNameById($people->nature);
        } elseif (is_array($people)) {
            $people['nature'] = getPeopleNatureNameById($people['nature']);
        }
    }
    return $data;
}