<?php

namespace App\Plugin\Cms;

use App\Form;

class CmsTemplate
{
    protected $pageDbData;
    protected $pageSlug;
    protected $pageHtmlContent;
    protected $pageHtmlZones;

    protected $defaultCol = '12';
    protected $allMetaKeys = [];
    protected $html = '';

    public function __construct($pageSlug, $pageDbData, $getHtmlContent = false)
    {
        $this->pageSlug = $pageSlug;
        $this->pageDbData = extractFromObjArr($pageDbData, 'metaKey');
        $this->pageHtmlContent = getFileContent($this->pageSlug);
        $this->set($getHtmlContent);
    }

    /**
     * Show content
     */
    public function show()
    {
        echo !empty($this->html) ? $this->html : '';
    }

    /**
     * @return string
     */
    public function get()
    {
        return !empty($this->html) ? $this->html : $this->pageHtmlContent;
    }

    /**
     * @param bool $getHtmlContent
     */
    public function set($getHtmlContent = false)
    {

        //Check zones types
        if (preg_match_all("/{{(.*?)}}/", $this->pageHtmlContent, $match)) {

            //Check if return admin zone template or content
            if (!$getHtmlContent) {

                //Get zones types
                $this->pageHtmlZones = $this->getZones($match[1]);

                foreach ($this->pageHtmlZones as $adminZone) {

                    $this->html .= $this->buildHtmlAdminZone($adminZone);
                }

            } else {

                //Get zones content
                $this->pageHtmlZones = $match;
                $this->html = $this->buildHtmlFrontZone();

            }
        }
    }

    /**
     * @return mixed
     */
    public function buildHtmlFrontZone()
    {

        foreach ($this->pageHtmlZones[1] as $i => $adminZone) {

            if (strpos($adminZone, '_')) {

                //Get data
                list($metaKey, $formType, $params) = array_pad(explode('_', $adminZone), 3, '');

                $DbZone = array_key_exists($metaKey, $this->pageDbData) ? $this->pageDbData[$metaKey] : false;
                $value = !empty($DbZone) ? $this->formatText($DbZone->metaValue, $formType) : '';

                //Get input params
                if (!empty($value) && preg_match_all("/\[(.*?)\]/", $params, $match)) {

                    $zoneAddedOptions = $this->getParams($match[1][0]);
                    $value = $this->buildHtmlFrontAdded($formType, $zoneAddedOptions, $value);
                }

                //Set data
                $this->pageHtmlContent = str_replace($this->pageHtmlZones[0][$i], sprintf('%s', $value), $this->pageHtmlContent);

            } else {

                $this->pageHtmlContent = str_replace($this->pageHtmlZones[0][$i], '', $this->pageHtmlContent);
            }
        }

        return $this->pageHtmlContent;
    }

    /**
     * @param $zone
     * @return string
     */
    public function buildHtmlAdminZone($zone)
    {
        $html = '';
        $added = '';
        $col = $this->defaultCol;

        //Check for form types
        if (str_contains($zone, '_')) {

            //Get data
            list($metaKey, $formType, $params) = array_pad(explode('_', $zone), 3, '');

            //Get input value
            $metaKeyDisplay = ucfirst(str_replace('-', ' ', $metaKey));
            $idCmsContent = !empty($this->pageDbData[$metaKey]) ? $this->pageDbData[$metaKey]->id : '';
            $valueCmsContent = !empty($this->pageDbData[$metaKey]) ? $this->pageDbData[$metaKey]->metaValue : '';

            //Get input params
            if (preg_match_all("/\[(.*?)\]/", $params, $match)) {

                $zoneAddedOptions = $this->getParams($match[1][0]);

                $added .= array_key_exists('size', $zoneAddedOptions) ? '<span class="badge badge-info">' . $zoneAddedOptions['size'] . 'px</span>&nbsp' : '';
                $added .= $zoneAddedOptions['webp'] ? '<span class="badge badge-info">webp</span>' : '';
                $added = '<span class="float-right">' . $added . '</span>';

                if (array_key_exists('col', $zoneAddedOptions)) {
                    $col = $zoneAddedOptions['col'];
                }

            } elseif (is_numeric($params)) {
                $col = $params;
            }

            //Display input zone
            $html .= '<div class="col-12 col-lg-' . $col . ' my-2 templateZoneInput">' . $added;


            //Check unique input
            if (!in_array($metaKey, $this->allMetaKeys)) {

                //Display form input
                if (str_contains($formType, ':')) {

                    //Get form options
                    $options = explode(':', $formType);

                    //Get form type
                    $formType = array_shift($options);

                    if ($formType == 'select') {
                        $html .= Form::select($metaKeyDisplay, $metaKey, array_combine($options, $options), $valueCmsContent, false, 'data-idcmscontent="' . $idCmsContent . '"');
                    }
                } else {
                    if ($formType == 'textBig') {
                        $html .= Form::textarea($metaKeyDisplay, $metaKey, $valueCmsContent, 8, false, 'data-idcmscontent="' . $idCmsContent . '"');
                    } elseif ($formType == 'textarea') {
                        $html .= Form::textarea($metaKeyDisplay, $metaKey, htmlSpeCharDecode($valueCmsContent), 8, false, 'data-idcmscontent="' . $idCmsContent . '"', 'appoeditor');
                    } elseif ($formType == 'urlFile') {
                        $html .= Form::text($metaKeyDisplay, $metaKey, 'url', $valueCmsContent, false, 250, 'data-idcmscontent="' . $idCmsContent . '" rel="cms-img-popover"', '', 'urlFile');
                    } else {
                        $html .= Form::text($metaKeyDisplay, $metaKey, $formType, $valueCmsContent, false, 250, 'data-idcmscontent="' . $idCmsContent . '"');
                    }
                }

                $this->allMetaKeys[] = $metaKey;
            }

            $html .= '</div>';

        } else {
            $html .= $zone;
        }

        return $html;
    }

    /**
     * @param array $zones
     * @return array
     */
    public function getZones(array $zones)
    {
        //Clean data
        $zones = cleanRequest($zones);

        //Zones types array
        $pageHtmlZonesTypes = [];

        foreach ($zones as $i => $adminZone) {

            //Check for form type
            if (str_contains($adminZone, '_')) {

                //Get data
                list($metaKey, $formType, $col) = array_pad(explode('_', $adminZone), 3, '');

                //Check form type with options
                if (str_contains($formType, ':')) {

                    $options = explode(':', $formType);
                    $formType = array_shift($options);
                }

                //Check form authorised data
                if ($this->isAuthorisedFormType($formType)) {

                    //Filter uniques form zones
                    if (!in_array($adminZone, $pageHtmlZonesTypes)) {
                        $pageHtmlZonesTypes[] = $adminZone;
                    }
                }

            } else {
                if (str_contains($adminZone, '#')) {

                    //Get data
                    list($htmlTag, $text, $zoneName) = array_pad(explode('#', $adminZone), 3, random_int(99, 99999999));

                    //Get Container Classes
                    $extract = $this->extractClassFromHtmlTag($htmlTag);
                    $htmlTag = $extract['tag'];
                    $class = $extract['class'];

                    //Check container authorised data
                    if ($this->isAuthorisedHtmlContainer($htmlTag)) {

                        $pageHtmlZonesTypes[] = '<' . $htmlTag . ' class="templateZoneTag templateZoneTitle ' . $class . ' " id="' . $zoneName . '">' . ucfirst($text) . '</' . $htmlTag . '>';
                    }

                } else {

                    //Get closed html tag condition
                    $closeTag = false;
                    if (str_contains($adminZone, '/')) {
                        $closeTag = true;
                        $adminZone = str_replace('/', '', $adminZone);
                    }

                    //Get Container Classes
                    $extract = $this->extractClassFromHtmlTag($adminZone);
                    $htmlTag = $extract['tag'];
                    $class = $extract['class'];

                    //Check authorised html tag
                    if ($this->isAuthorisedHtmlContainer($htmlTag)) {
                        $pageHtmlZonesTypes[] = '<' . ($closeTag ? '/' : '') . $htmlTag . ' class="templateZoneTag ' . $class . ' ">';
                    }

                }
            }
        }

        return $pageHtmlZonesTypes;
    }

    /**
     * @param $formType
     * @param $options
     * @param $value
     * @return string
     */
    public function buildHtmlFrontAdded($formType, $options, $value)
    {

        if ($formType === 'urlFile') {

            $imgDefaultOptions = array(
                'webp' => false
            );

            $options = array_merge($imgDefaultOptions, $options);

            if (array_key_exists('size', $options) && strpos($value, '://')) {
                $value = str_replace('://', '', strstr($value, '://'));
                if (strpos($value, '/')) {
                    $url = explode('/', $value);
                    return getThumb(array_pop($url), $options['size'], $options['webp']);
                }
            }
        }

        return $value;
    }


    public function getParams($match)
    {

        $options = [];
        $params = array_filter(explode(';', $match));
        foreach ($params as $p => $param) {
            list($key, $val) = explode('=', $param);
            $options[$key] = $val;
        }
        return $options;
    }

    public function formatText(?string $text, string $type): string
    {
        $text = htmlSpeCharDecode($text ?? '');

        return $type === 'textBig' ? nl2br($text) : $text;
    }

    /**
     * @param $formType
     * @return bool
     */
    public function isAuthorisedFormType($formType)
    {

        //Authorised form manage data
        $acceptedFormType = array('text', 'textarea', 'textBig', 'email', 'tel', 'url', 'color', 'number', 'date', 'select', 'radio', 'checkbox', 'urlFile');

        return in_array($formType, $acceptedFormType);
    }

    /**
     * @param $formType
     * @return bool
     */
    public function isAuthorisedHtmlContainer($formType)
    {

        //Authorised HTML Container
        $acceptedHtmlContainer = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'strong', 'em', 'div', 'hr', 'br');

        return in_array($formType, $acceptedHtmlContainer);
    }

    /**
     * @param string $htmlTag
     * @return array
     */
    public function extractClassFromHtmlTag($htmlTag = '')
    {
        $class = '';
        if (strpos($htmlTag, '.')) {
            list($htmlTag, $class) = explode('.', $htmlTag, 2);

            if (strpos($class, '.')) {
                $class = str_replace('.', ' ', $class);
            }
        }
        return array('tag' => $htmlTag, 'class' => $class);
    }

    /**
     * @return string
     */
    public function showErrorPage()
    {
        return '<div class="container"><h4>' . trans('Cette page n\'existe pas') . '</h4></div>';
    }
}