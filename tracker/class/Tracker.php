<?php

namespace App\Plugin\Tracker;

use App\DB;
use PDO;
use App\Plugin\Tracker\Browser;

class Tracker
{

    /**
     * @var string
     */
    private string $tableName = '`' . TABLEPREFIX . 'appoe_plugin_tracker`';
    /**
     * @var int
     */
    private int $id;
    /**
     * @var string
     */
    private string $date;
    /**
     * @var string
     */
    private string $ip;
    /**
     * @var int|string
     */
    private int|string $pageId;
    /**
     * @var string
     */
    private string $pageType;
    /**
     * @var string
     */
    private string $pageName;
    /**
     * @var string
     */
    private string $pageSlug;
    /**
     * @var string|null
     */
    private ?string $referer = null;
    /**
     * @var string|null
     */
    private ?string $device = null;
    /**
     * @var string|null
     */
    private ?string $browserName = null;
    /**
     * @var string|null
     */
    private ?string $browserVersion = null;
    /**
     * @var string|null
     */
    private ?string $osName = null;
    /**
     * @var string|null
     */
    private ?string $osVersion = null;

    /**
     * @param bool $save
     */
    public function __construct(bool $save = false)
    {
        if ($save) {
            $this->date = date('Y-m-d H:i:s');
            $this->ip = getIP() ?: '';
            $this->pageId = getPageParam('currentPageID');
            $this->pageType = getPageParam('currentPageType');
            $this->pageName = getPageParam('currentPageName');
            $this->pageSlug = getPageParam('currentPageSlug');

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
     * @return bool|object
     */
    public function save(): bool|object
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int|string
     */
    public function getPageId(): int|string
    {
        return $this->pageId;
    }

    /**
     * @param int|string $pageId
     */
    public function setPageId(int|string $pageId): void
    {
        $this->pageId = $pageId;
    }

    /**
     * @return string
     */
    public function getPageType(): string
    {
        return $this->pageType;
    }

    /**
     * @param string $pageType
     */
    public function setPageType(string $pageType): void
    {
        $this->pageType = $pageType;
    }

    /**
     * @return string
     */
    public function getPageName(): string
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     */
    public function setPageName(string $pageName): void
    {
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getPageSlug(): string
    {
        return $this->pageSlug;
    }

    /**
     * @param string $pageSlug
     */
    public function setPageSlug(string $pageSlug): void
    {
        $this->pageSlug = $pageSlug;
    }

    /**
     * @return ?string
     */
    public function getReferer(): ?string
    {
        return $this->referer;
    }

    /**
     * @param ?string $referer
     */
    public function setReferer(?string $referer): void
    {
        $this->referer = $referer;
    }

    /**
     * @return ?string
     */
    public function getDevice(): ?string
    {
        return $this->device;
    }

    /**
     * @param ?string $device
     */
    public function setDevice(?string $device): void
    {
        $this->device = $device;
    }

    /**
     * @return ?string
     */
    public function getBrowserName(): ?string
    {
        return $this->browserName;
    }

    /**
     * @param ?string $browserName
     */
    public function setBrowserName(?string $browserName): void
    {
        $this->browserName = $browserName;
    }

    /**
     * @return ?string
     */
    public function getBrowserVersion(): ?string
    {
        return $this->browserVersion;
    }

    /**
     * @param ?string $browserVersion
     */
    public function setBrowserVersion(?string $browserVersion): void
    {
        $this->browserVersion = $browserVersion;
    }

    /**
     * @return ?string
     */
    public function getOsName(): ?string
    {
        return $this->osName;
    }

    /**
     * @param ?string $osName
     */
    public function setOsName(?string $osName): void
    {
        $this->osName = $osName;
    }

    /**
     * @return ?string
     */
    public function getOsVersion(): ?string
    {
        return $this->osVersion;
    }

    /**
     * @param ?string $osVersion
     */
    public function setOsVersion(?string $osVersion): void
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return bool|object|array
     */
    public function showBetweenDates(?string $dateStart = null, ?string $dateEnd = null): bool|object|array
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return array
     */
    public function getData(?string $dateStart = null, ?string $dateEnd = null): array
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return object|array|bool
     */
    public function showCountVisitorsBetweenDates(?string $dateStart = null, ?string $dateEnd = null): object|array|bool
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return object|array|bool
     */
    public function showCountPagesTypeBetweenDates(?string $dateStart = null, ?string $dateEnd = null): object|array|bool
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return object|array|bool
     */
    public function showCountPagesBetweenDates(?string $dateStart = null, ?string $dateEnd = null): object|array|bool
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return bool|array|object
     */
    public function showCountDevicesTypeBetweenDates(?string $dateStart = null, ?string $dateEnd = null): bool|array|object
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return bool|array|object
     */
    public function showCountDevicesBetweenDates(?string $dateStart = null, ?string $dateEnd = null): bool|array|object
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return object|array|bool
     */
    public function showCountDevicesTypeForShopBetweenDates(?string $dateStart = null, ?string $dateEnd = null): object|array|bool
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return object|array|bool
     */
    public function showCountDevicesForShopBetweenDates(?string $dateStart = null, ?string $dateEnd = null): object|array|bool
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
     * @return object|array|bool
     */
    public function showCountRefererBetweenDates(?string $dateStart = null, ?string $dateEnd = null): object|array|bool
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
     * @param ?string $dateStart
     * @param ?string $dateEnd
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

