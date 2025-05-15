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
            $this->pageId = getPageId();
            $this->pageType = getPageType();
            $this->pageName = getPageName();
            $this->pageSlug = getPageSlug();

            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            if ($userAgent) {
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
    }

    /**
     * @return bool
     */
    public function save(): bool
    {

        $attributeToSave = array('date', 'ip', 'pageId', 'pageType', 'pageName', 'pageSlug', 'referer', 'device', 'browserName', 'browserVersion', 'osName', 'osVersion');
        $params = array();
        $sql = 'INSERT INTO ' . $this->tableName . ' (' . implode(', ', $attributeToSave) . ') 
                VALUES (:' . implode(', :', $attributeToSave) . ')';
        foreach ($attributeToSave as $value) {
            $params[':' . $value] = $value ? $this->$value : null;
        }
        return DB::exec($sql, $params);
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDate(): mixed
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate(mixed $date): void
    {
        $this->date = $date;
    }

    /**
     * @return bool
     */
    public function getIp(): bool
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp(mixed $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getPageId(): mixed
    {
        return $this->pageId;
    }

    /**
     * @param mixed $pageId
     */
    public function setPageId(mixed $pageId): void
    {
        $this->pageId = $pageId;
    }

    /**
     * @return mixed
     */
    public function getPageType(): mixed
    {
        return $this->pageType;
    }

    /**
     * @param mixed $pageType
     */
    public function setPageType(mixed $pageType): void
    {
        $this->pageType = $pageType;
    }

    /**
     * @return mixed
     */
    public function getPageName(): mixed
    {
        return $this->pageName;
    }

    /**
     * @param mixed $pageName
     */
    public function setPageName(mixed $pageName): void
    {
        $this->pageName = $pageName;
    }

    /**
     * @return mixed
     */
    public function getPageSlug(): mixed
    {
        return $this->pageSlug;
    }

    /**
     * @param mixed $pageSlug
     */
    public function setPageSlug(mixed $pageSlug): void
    {
        $this->pageSlug = $pageSlug;
    }

    /**
     * @return mixed
     */
    public function getReferer(): mixed
    {
        return $this->referer;
    }

    /**
     * @param mixed $referer
     */
    public function setReferer(mixed $referer): void
    {
        $this->referer = $referer;
    }

    /**
     * @return mixed
     */
    public function getDevice(): mixed
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice(mixed $device): void
    {
        $this->device = $device;
    }

    /**
     * @return mixed
     */
    public function getBrowserName(): mixed
    {
        return $this->browserName;
    }

    /**
     * @param mixed $browserName
     */
    public function setBrowserName(mixed $browserName): void
    {
        $this->browserName = $browserName;
    }

    /**
     * @return mixed
     */
    public function getBrowserVersion(): mixed
    {
        return $this->browserVersion;
    }

    /**
     * @param mixed $browserVersion
     */
    public function setBrowserVersion(mixed $browserVersion): void
    {
        $this->browserVersion = $browserVersion;
    }

    /**
     * @return mixed
     */
    public function getOsName(): mixed
    {
        return $this->osName;
    }

    /**
     * @param mixed $osName
     */
    public function setOsName(mixed $osName): void
    {
        $this->osName = $osName;
    }

    /**
     * @return mixed
     */
    public function getOsVersion(): mixed
    {
        return $this->osVersion;
    }

    /**
     * @param mixed $osVersion
     */
    public function setOsVersion(mixed $osVersion): void
    {
        $this->osVersion = $osVersion;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
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
     * @param mixed|null $dateStart
     * @param mixed|null $dateEnd
     * @return bool|mixed
     */
    public function showBetweenDates(mixed $dateStart = null, mixed $dateEnd = null): mixed
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
     *
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return array
     */
    public function getData(string $dateStart = null, string $dateEnd = null): array
    {
        //collecte des données
        $data['visitors'] = $this->showCountVisitorsBetweenDates($dateStart, $dateEnd);
        $data['pagesType'] = $this->showCountPagesTypeBetweenDates($dateStart, $dateEnd);
        $data['pagesName'] = $this->showCountPagesBetweenDates($dateStart, $dateEnd);

        $data['devicesType'] = $this->showCountDevicesTypeBetweenDates($dateStart, $dateEnd);
        $data['devices'] = $this->showCountDevicesBetweenDates($dateStart, $dateEnd);

        $data['devicesTypeForShop'] = $this->showCountDevicesTypeForShopBetweenDates($dateStart, $dateEnd);
        $data['devicesForShop'] = $this->showCountDevicesForShopBetweenDates($dateStart, $dateEnd);

        $data['referer'] = $this->showCountRefererBetweenDates($dateStart, $dateEnd);


        //Formatage des données pour les tableaux d'objets :
        //-1- trie les tableaux ($key) par ordre décroissant
        //-2- regroupe les données dans un tableau si le type ($value) est précisé
        $arraysToProcess = [
            'pagesName' => 'pageType',
            'devices' => 'device',
            'devicesForShop' => 'device',
            'referer' => null
        ];
        foreach ($arraysToProcess as $key => $value) {
            if (isset($data[$key]) && is_array($data[$key])) {
                // Trier les données par la clé 'count' en ordre décroissant
                $data[$key] = array_sort($data[$key], 'count', SORT_DESC);

                // Si un regroupement est défini, appliquer la fonction de regroupement
                if ($value) {
                    $data[$key] = groupMultipleKeysObjectsArray($data[$key], $value);
                }
            }
        }

        return $data;
    }

    /**
     * Retourne le nb de pages consultées, le nb de visiteurs uniques, et le nb visiteurs uniques par 24h
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return object|array|bool
     */
    public function showCountVisitorsBetweenDates(string $dateStart = null, string $dateEnd = null): object|array|bool
    {
        $params = [];

        $sql = 'SELECT 
                COUNT(`date`) AS consultedPages, 
                COUNT(DISTINCT `ip`) AS unique_ips, 
                COUNT(DISTINCT `ip`, `date`) AS countByDay_ips 
            FROM ' . $this->tableName;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetch(PDO::FETCH_OBJ) : false;
    }


    /**
     * Compte le nb de pages servies selon leur type (PAGE, ARTICLE, SHOP)
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return object|array|bool
     */
    public function showCountPagesTypeBetweenDates(string $dateStart = null, string $dateEnd = null): object|array|bool
    {
        $params = [];

        $sql = 'SELECT COUNT( CASE WHEN `pageType` = "PAGE" THEN 1 END) AS PAGE, 
        COUNT( CASE WHEN `pageType` = "ARTICLE" THEN 1 END) as ARTICLE, 
        COUNT( CASE WHEN `pageType` = "SHOP" THEN 1 END) as SHOP FROM ' . $this->tableName;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetch(PDO::FETCH_OBJ) : false;
    }


    /**
     * Compte le nb de pages servies selon leur nom et leur type (PAGE, ARTICLE, SHOP)
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return object|array|bool
     */
    public function showCountPagesBetweenDates(string $dateStart = null, string $dateEnd = null): object|array|bool
    {
        $params = [];
        $sql = 'SELECT `pageType`, `pageName`, COUNT(`pageName`) AS count FROM ' . $this->tableName;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);
        $sql .= ' GROUP BY `pageName`, `pageType` ';

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /*
     * -------------------------------------------------
     * ------------- Détail par navigateur -------------
     * -------------------------------------------------
     */

    /**
     * Retourne un objet contenant le nombre d’appareils différents par type d’appareil (Ordinateur, Mobile ou Autre)
     *
     * @param mixed|null $dateStart
     * @param mixed|null $dateEnd
     * @return bool|mixed
     */
    public function showCountDevicesTypeBetweenDates(mixed $dateStart = null, mixed $dateEnd = null): mixed
    {
        $params = [];

        $sql = 'SELECT COUNT(DISTINCT CASE WHEN `device` = "desktop" THEN CONCAT(`ip`, `osName`, `browserName`) END) AS ordinateur,
    COUNT(DISTINCT CASE WHEN `device` = "mobile" THEN CONCAT(`ip`, `osName`, `browserName`) END) AS mobile,
    COUNT(DISTINCT CASE WHEN `device` = "unknown" THEN CONCAT(`ip`, `osName`, `browserName`) END) AS autre FROM' . $this->tableName;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetch(PDO::FETCH_OBJ) : false;
    }

    /**
     * Retourne un tableau d’objets, chaque objet contenant le type d’appareil, son OS, le navigateur utilisé et son nombre d'occurrences
     *
     * @param mixed|null $dateStart
     * @param mixed|null $dateEnd
     * @return bool|mixed
     */
    public function showCountDevicesBetweenDates(mixed $dateStart = null, mixed $dateEnd = null): mixed
    {
        $params = [];
        $sql = ' SELECT
                    CASE WHEN `device` = "desktop" THEN "ordinateur" WHEN `device` = "unknown" THEN "autre" ELSE `device` END AS `device`,
                    CASE WHEN `osName` = "unknown" THEN "système tiers" ELSE `osName` END AS `osName`,
                    CASE WHEN `browserName` = "unknown" THEN "inconnu" ELSE `browserName` END AS `browserName`,
                    COUNT(DISTINCT ip) AS count FROM ' . $this->tableName;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);
        $sql .= ' GROUP BY `device`, `osName`, `browserName`';

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /*
     * -----------------------------------------------------
     * ----------------- Détail par pageId -----------------
     * -----------------------------------------------------
     */

    /**
     * Retourne un objet contenant, pour un pageID spécifique, le nombre d’appareils différents par type d’appareil (ordinateur, mobile ou autre).
     *
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return object|array|bool
     */
    public function showCountDevicesTypeForShopBetweenDates(string $dateStart = null, string $dateEnd = null): object|array|bool
    {
        $params = [];
        $sql = 'SELECT COUNT(DISTINCT CASE WHEN `device` = "desktop" THEN `ip` END) AS ordinateur, COUNT(DISTINCT CASE WHEN `device` = "mobile" THEN `ip` END) AS mobile, COUNT(DISTINCT CASE WHEN `device` = "unknown" THEN `ip` END) AS autre FROM ' . $this->tableName;

        $params[':pageId'] = 12;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);
        $sql .= ' WHERE `pageId` = :pageId ';

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetch(PDO::FETCH_OBJ) : false;
    }

    /**
     * Retourne un tableau d’objets, chaque objet contenant le type d’appareil, son OS, le navigateur utilisé et son nombre d'occurrences pour le pageID spécifié.
     *
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return object|array|bool
     */
    public function showCountDevicesForShopBetweenDates(string $dateStart = null, string $dateEnd = null): object|array|bool
    {
        $params = [];
        $sql = ' SELECT
                    CASE WHEN `device` = "desktop" THEN "ordinateur" WHEN `device` = "unknown" THEN "autre" ELSE `device` END AS `device`,
                    CASE WHEN `osName` = "unknown" THEN "système tiers" ELSE `osName` END AS `osName`,
                    CASE WHEN `browserName` = "unknown" THEN "inconnu" ELSE `browserName` END AS `browserName`,
                    COUNT(DISTINCT ip) AS count FROM ' . $this->tableName;

        $params[':pageId'] = 12;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);
        $sql .= ' WHERE `pageId` = :pageId ';
        $sql .= ' GROUP BY `device`, `osName`, `browserName`';

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /**
     * Retourne les adresses Referer et leur décompte
     *
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return object|array|bool
     */
    public function showCountRefererBetweenDates(string $dateStart = null, string $dateEnd = null): object|array|bool
    {
        $params = [];
        $sql = ' SELECT `referer`, COUNT(*) AS count FROM ' . $this->tableName;

        // Extension de la requête avec les conditions
        $sql .= $this->addConditions($params, $dateStart, $dateEnd);
        $sql .= ' GROUP BY `referer`';

        // Exécuter la requête directement et retourner le résultat
        $result = DB::exec($sql, $params);

        return $result ? $result->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /**
     * Retourne selon les valeurs $dateStart et $dateEnd une string qui compléte les requêtes sql, et modifie le tableau $params
     * @param array &$params
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @return string
     */
    private function addConditions(array &$params, ?string $dateStart, ?string $dateEnd): string
    {
        // Construction des conditions dynamiques
        $conditions = [];

        if ($dateStart !== null) {
            $conditions[] = 'date >= :dateStart';
            $params[':dateStart'] = $dateStart;
        }

        if ($dateEnd !== null) {
            $conditions[] = 'date <= :dateEnd';
            $params[':dateEnd'] = $dateEnd;
        }

        return !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';
    }
}

