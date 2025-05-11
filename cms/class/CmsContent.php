<?php

namespace App\Plugin\Cms;

use App\DB;
use PDO;

class CmsContent
{
    private $id;
    private $idCms;
    private $type = 'BODY';
    private $metaKey;
    private $metaValue;
    private $lang = LANG;

    private $data = null;
    private $dbh = null;

    public function __construct($idCms = null, $lang = null, $onlyHeaders = false)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($idCms) && !is_null($lang)) {
            $this->idCms = $idCms;
            $this->lang = $lang;
            $this->showAll($onlyHeaders);
        }
    }

    /**
     * @param bool $onlyHeaders
     * @return array|bool
     */
    public function showAll($onlyHeaders = false)
    {

        $sqlAdd = $onlyHeaders ? ' AND type = "HEADER" ' : '';
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content WHERE idCms = :idCms ' . $sqlAdd . ' AND lang = :lang ORDER BY created_at ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);

            if ($onlyHeaders) {
                $this->setHeaders();
            }
            return $this->data;
        }
    }

    /**
     *
     */
    public function setHeaders()
    {

        if (!isArrayEmpty($this->data)) {

            $newData = [];
            foreach ($this->data as $data) {
                $newData[$data->metaKey] = $data->metaValue;
            }
            $this->data = $newData;
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
     * @return null
     */
    public function getIdCms()
    {
        return $this->idCms;
    }

    /**
     * @param null $idCms
     */
    public function setIdCms($idCms)
    {
        $this->idCms = $idCms;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param mixed $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param mixed $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_plugin_cms_content` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`idCms` INT(11) NOT NULL,
                	`type` VARCHAR(25) NOT NULL DEFAULT "BODY",
  					`metaKey` VARCHAR(255) NOT NULL,
  					`metaValue` TEXT NOT NULL,
  					`lang` VARCHAR(10) NOT NULL DEFAULT "fr",
                	`created_at` DATE NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                	UNIQUE (`idCms`, `type`, `metaKey`, `lang`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function show()
    {

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * Feed class attributs
     *
     * @param $data
     */
    public function feed($data)
    {
        foreach ($data as $attribut => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_cms_content (idCms, type, metaKey, metaValue, lang, created_at) 
                VALUES (:idCms, :type, :metaKey, :metaValue, :lang, CURDATE())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->id = $id;
            appLog('Creating page content -> idCms: ' . $this->idCms . ' type: ' . $this->type . ' metaKey: ' . $this->metaKey . ' lang: ' . $this->lang);
            return true;
        }
    }

    /**
     * @param $headers
     * @return bool
     */
    public function saveHeaders($headers)
    {

        $authorizedHeaders = array('name', 'description', 'slug', 'menuName');

        if (!isArrayEmpty($headers)) {


            $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_cms_content (idCms, type, metaKey, metaValue, lang, created_at) 
                VALUES (?, ?, ?, ?, ?, CURDATE())';

            $stmt = $this->dbh->prepare($sql);

            foreach (getLangs() as $lang => $longLang) {
                foreach ($headers as $key => $val) {
                    if (in_array($key, $authorizedHeaders)) {
                        $stmt->execute(array($this->idCms, 'HEADER', $key, $val, $lang));
                    }
                }
            }

            $error = $stmt->errorInfo();
            if ($error[0] == '00000') {
                appLog('Creating page headers -> idCms: ' . $this->idCms);
            }
            return true;


        }

        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_cms_content SET metaKey = :metaKey, metaValue = :metaValue WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':metaValue', $this->metaValue);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating page content -> id: ' . $this->id . ' metaKey: ' . $this->metaKey);
            return true;
        }
    }

    /**
     * @param $oldName
     * @param $newName
     * @return bool
     */
    public function renameFilename($oldName, $newName)
    {

        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_cms_content SET metaValue = :newName WHERE metaValue = :oldName';

        $stmt = DB::exec($sql, [':newName' => $newName, ':oldName' => $oldName]);

        if ($stmt) {
            appLog('Renaming files in page content -> old: ' . $oldName . ' new: ' . $newName);
            return true;
        }
        return false;
    }

    /**
     * @param $headers
     * @return bool
     */
    public function updateHeaders($headers)
    {

        $authorizedHeaders = array('name', 'description', 'slug', 'menuName');

        if (!isArrayEmpty($headers)) {

            $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_cms_content 
            SET metaValue = :metaValue WHERE idCms = :idCms AND type = "HEADER" AND metaKey = :metaKey AND lang = :lang';

            $stmt = $this->dbh->prepare($sql);


            foreach ($headers as $key => $val) {
                if (in_array($key, $authorizedHeaders)) {
                    $stmt->execute(array(':metaValue' => $val, ':idCms' => $this->idCms, ':metaKey' => $key, ':lang' => $this->lang));
                }
            }

            $error = $stmt->errorInfo();
            if ($error[0] == '00000') {
                appLog('Updating page headers -> idCms: ' . $this->idCms . ' lang: ' . $this->lang);
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {

        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content WHERE idCms = :idCms';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete page content -> idCms: ' . $this->idCms);
            return true;
        }
    }

    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist($forUpdate = false)
    {

        $sql = 'SELECT id FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content WHERE idCms = :idCms AND type = :type AND metaKey = :metaKey AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':idCms', $this->idCms);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':metaKey', $this->metaKey);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                if ($forUpdate) {
                    $data = $stmt->fetch(PDO::FETCH_OBJ);
                    if ($data->id == $this->id) {
                        return true;
                    }
                }
                return false;
            } else {
                return true;
            }
        }
    }
}