<?php

namespace App\Plugin\Tracker;

use App\DB;
use PDO;

class Tracker
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_tracker`';
    private $id;
    private $date;
    private $ip;
    private $pageId;
    private $pageType;
    private $pageName;
    private $pageSlug;
    private $referer = null;
    private $device = null;
    private $browserName = null;
    private $browserVersion = null;
    private $osName = null;
    private $osVersion = null;

    public function __construct($save = false)
    {
        if ($save) {
            $this->date = date('Y-m-d H:i:s');
            $this->ip = getIP();
            $this->pageId = getPageParam('currentPageID');
            $this->pageType = getPageParam('currentPageType');
            $this->pageName = getPageParam('currentPageName');
            $this->pageSlug = getPageParam('currentPageSlug');

            $Browser = new Browser();
            $result = $Browser->getAll($_SERVER['HTTP_USER_AGENT']);

            $this->referer = $_SERVER['HTTP_REFERER'] ?? null;
            $this->device = $result['device_type'];
            $this->browserName = $result['browser_name'];
            $this->browserVersion = $result['browser_version'];
            $this->osName = $result['os_name'];
            $this->osVersion = $result['os_version'];

            $this->save();
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param mixed $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return mixed
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * @param mixed $pageType
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;
    }

    /**
     * @return mixed
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @param mixed $pageName
     */
    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
    }

    /**
     * @return mixed
     */
    public function getPageSlug()
    {
        return $this->pageSlug;
    }

    /**
     * @param mixed $pageSlug
     */
    public function setPageSlug($pageSlug)
    {
        $this->pageSlug = $pageSlug;
    }

    /**
     * @return mixed
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * @param mixed $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice($device)
    {
        $this->device = $device;
    }

    /**
     * @return mixed
     */
    public function getBrowserName()
    {
        return $this->browserName;
    }

    /**
     * @param mixed $browserName
     */
    public function setBrowserName($browserName)
    {
        $this->browserName = $browserName;
    }

    /**
     * @return mixed
     */
    public function getBrowserVersion()
    {
        return $this->browserVersion;
    }

    /**
     * @param mixed $browserVersion
     */
    public function setBrowserVersion($browserVersion)
    {
        $this->browserVersion = $browserVersion;
    }

    /**
     * @return mixed
     */
    public function getOsName()
    {
        return $this->osName;
    }

    /**
     * @param mixed $osName
     */
    public function setOsName($osName)
    {
        $this->osName = $osName;
    }

    /**
     * @return mixed
     */
    public function getOsVersion()
    {
        return $this->osVersion;
    }

    /**
     * @param mixed $osVersion
     */
    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `date` DATE NOT NULL,
                `ip` VARCHAR(100) NOT NULL,
                `pageId` INT(11) UNSIGNED NOT NULL,
                `pageType` VARCHAR(50) NOT NULL,
                `pageName` VARCHAR(100) NOT NULL,
                `pageSlug` VARCHAR(100) NOT NULL,
                `referer` VARCHAR(255) NULL DEFAULT NULL,
                `device` VARCHAR(50) NULL DEFAULT NULL,
                `browserName` VARCHAR(100) NULL DEFAULT NULL,
                `browserVersion` VARCHAR(50) NULL DEFAULT NULL,
                `osName` VARCHAR(50) NULL DEFAULT NULL,
                `osVersion` VARCHAR(50) NULL DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
				CREATE INDEX date ON ' . $this->tableName . ' (`date`);';
        return (bool)DB::exec($sql);
    }

    /**
     * @return bool
     */
    public function save()
    {

        $attributeToSave = array('date', 'ip', 'pageId', 'pageType', 'pageName', 'pageSlug', 'referer', 'device', 'browserName', 'browserVersion', 'osName', 'osVersion');
        $params = [];
        $sql = 'INSERT INTO ' . $this->tableName . ' (' . implode(', ', $attributeToSave) . ') 
                VALUES (:' . implode(', :', $attributeToSave) . ')';
        foreach ($attributeToSave as $value) {
            $params[':' . $value] = $value ? $this->$value : null;
        }
        return DB::exec($sql, $params);
    }

    /**
     * @param bool|mixed $dateStart
     * @param bool|mixed $dateEnd
     * @return bool|mixed
     */
    public function showBetweenDates($dateStart = null, $dateEnd = null)
    {
        $params = [];
        $sql = 'SELECT date, ip, pageType, pageName FROM ' . $this->tableName;

        $sql .= !is_null($dateStart) || !is_null($dateEnd) ? ' WHERE ' : '';

        if (!is_null($dateStart)) {
            $sql .= ' date >= :dateStart ';
            $params[':dateStart'] = $dateStart;
        }

        $sql .= !is_null($dateStart) && !is_null($dateEnd) ? ' AND ' : '';

        if (!is_null($dateEnd)) {
            $sql .= ' date <= :dateEnd ';
            $params[':dateEnd'] = $dateEnd;
        }

        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param bool|mixed $dateStart
     * @param bool|mixed $dateEnd
     * @return bool|mixed
     */
    public function showCountVisitorsBetweenDates($dateStart = null, $dateEnd = null)
    {
        $params = [];
        $sql = 'SELECT COUNT(`date`) AS consultedPages, COUNT(DISTINCT `ip`) AS ips FROM ' . $this->tableName;

        $sql .= !is_null($dateStart) || !is_null($dateEnd) ? ' WHERE ' : '';

        if (!is_null($dateStart)) {
            $sql .= ' date >= :dateStart ';
            $params[':dateStart'] = $dateStart;
        }

        $sql .= !is_null($dateStart) && !is_null($dateEnd) ? ' AND ' : '';

        if (!is_null($dateEnd)) {
            $sql .= ' date <= :dateEnd ';
            $params[':dateEnd'] = $dateEnd;
        }

        if ($return = DB::exec($sql, $params)) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param bool|mixed $dateStart
     * @param bool|mixed $dateEnd
     * @return bool|mixed
     */
    public function showCountPagesTypeBetweenDates($dateStart = null, $dateEnd = null)
    {
        $params = [];
        $sql = 'SELECT COUNT( CASE WHEN `pageType` = "PAGE" THEN 1 END) AS PAGE, 
        COUNT( CASE WHEN `pageType` = "ARTICLE" THEN 1 END) as ARTICLE, 
        COUNT( CASE WHEN `pageType` = "SHOP" THEN 1 END) as SHOP FROM ' . $this->tableName;

        $sql .= !is_null($dateStart) || !is_null($dateEnd) ? ' WHERE ' : '';

        if (!is_null($dateStart)) {
            $sql .= ' date >= :dateStart ';
            $params[':dateStart'] = $dateStart;
        }

        $sql .= !is_null($dateStart) && !is_null($dateEnd) ? ' AND ' : '';

        if (!is_null($dateEnd)) {
            $sql .= ' date <= :dateEnd ';
            $params[':dateEnd'] = $dateEnd;
        }

        if ($return = DB::exec($sql, $params)) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param bool|mixed $dateStart
     * @param bool|mixed $dateEnd
     * @return bool|mixed
     */
    public function showCountPagesBetweenDates($dateStart = null, $dateEnd = null)
    {
        $params = [];
        $sql = 'SELECT `pageType`, `pageName`, COUNT(`pageName`) AS count FROM ' . $this->tableName;

        $sql .= !is_null($dateStart) || !is_null($dateEnd) ? ' WHERE ' : '';

        if (!is_null($dateStart)) {
            $sql .= ' date >= :dateStart ';
            $params[':dateStart'] = $dateStart;
        }

        $sql .= !is_null($dateStart) && !is_null($dateEnd) ? ' AND ' : '';

        if (!is_null($dateEnd)) {
            $sql .= ' date <= :dateEnd ';
            $params[':dateEnd'] = $dateEnd;
        }

        $sql .= ' GROUP BY `pageName`, `pageType` ';

        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param mixed $dateStart
     * @param mixed $dateEnd
     * @return array
     */
    public function getData($dateStart = null, $dateEnd = null)
    {

        $data['visitors'] = $this->showCountVisitorsBetweenDates($dateStart, $dateEnd);
        $data['pagesType'] = $this->showCountPagesTypeBetweenDates($dateStart, $dateEnd);
        $data['pagesName'] = $this->showCountPagesBetweenDates($dateStart, $dateEnd);

        if (is_array($data['pagesName'])) {
            $data['pagesName'] = array_sort($data['pagesName'], 'count', SORT_DESC);
            $data['pagesName'] = groupMultipleKeysObjectsArray($data['pagesName'], 'pageType');
        }

        return $data;
    }
}