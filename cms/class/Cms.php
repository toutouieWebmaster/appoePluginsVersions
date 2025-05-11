<?php

namespace App\Plugin\Cms;

use App\DB;
use PDO;

class Cms
{
    private $id;
    private $type = 'PAGE';
    private $filename;
    private $statut = 1;

    private $name;
    private $menuName;
    private $description;
    private $slug;

    private $lang = LANG;
    private $dbh = null;

    public function __construct($idCms = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($idCms)) {
            $this->id = $idCms;
            $this->show();
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * @param mixed $menuName
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return bool|mixed|string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param bool|mixed|string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_plugin_cms` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`type` VARCHAR(100) NOT NULL DEFAULT "PAGE",
  					`filename` VARCHAR(255) NOT NULL,
  					UNIQUE (`type`, `filename`),
  					`statut` BOOLEAN NOT NULL DEFAULT TRUE,
                	`created_at` DATE NOT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;ALTER TABLE `' . TABLEPREFIX . 'appoe_plugin_cms` AUTO_INCREMENT = 11;';

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

        $sql = 'SELECT C.*,
         (SELECT cc1.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc1 WHERE cc1.type = "HEADER" AND cc1.metaKey = "slug" AND cc1.idCms = C.id AND cc1.lang = :lang) AS slug,
        (SELECT cc2.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc2 WHERE cc2.type = "HEADER" AND cc2.metaKey = "description" AND cc2.idCms = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc3 WHERE cc3.type = "HEADER" AND cc3.metaKey = "name" AND cc3.idCms = C.id AND cc3.lang = :lang) AS name,
        (SELECT cc4.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc4 WHERE cc4.type = "HEADER" AND cc4.metaKey = "menuName" AND cc4.idCms = C.id AND cc4.lang = :lang) AS menuName
        FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C
         WHERE C.id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':lang', $this->lang);
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
     * @return bool
     */
    public function showByFilename()
    {
        $sql = 'SELECT C.* FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C WHERE C.filename = :filename';

        if ($result = DB::exec($sql, [':filename' => $this->filename])) {
            if ($result->rowCount() == 1) {

                $row = $result->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                $CmsContent = new CmsContent($this->id, $this->lang, true);
                $this->feed($CmsContent->getData());

                $result->closeCursor();
                unset($this->dbh);
                return true;
            }
        }
        return false;
    }

    /**
     * @param $slug
     * @param bool|mixed|string $lang
     * @return bool
     */
    public function showBySlug($slug, $lang = LANG)
    {

        $sql = 'SELECT C.* FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C
         INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_cms_content AS CC
         ON(C.id = CC.idCms)
         WHERE CC.type = "HEADER" AND CC.metaKey = "slug" AND CC.metaValue = :slug AND CC.lang = :lang';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                $CmsContent = new CmsContent($this->id, $lang, true);
                $this->feed($CmsContent->getData());

                return true;

            } else {
                return false;
            }
        }
    }

    /**
     * @param $slug
     * @return bool|object
     */
    public function getFilenameBySlug($slug)
    {

        $sql = 'SELECT C.filename FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C
        INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_cms_content AS CC
         ON(C.id = CC.idCms)
         WHERE CC.type = "HEADER" AND CC.metaKey = "slug" AND CC.metaValue = :slug';

        if ($return = DB::exec($sql, [':slug' => $slug])) {
            if($row = $return->fetch(PDO::FETCH_OBJ)) {
                return $row->filename;
            }
        }
        return false;
    }

    /**
     * @param bool|mixed|string $lang
     * @return bool
     */
    public function showDefaultSlug($lang = LANG)
    {

        $sql = 'SELECT C.* FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C
         INNER JOIN ' . TABLEPREFIX . 'appoe_plugin_cms_content AS CC
         ON(C.id = CC.idCms)
         WHERE C.filename = "index" AND CC.type = "HEADER" AND CC.metaKey = "slug" AND CC.lang = :lang';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                $CmsContent = new CmsContent($this->id, $lang, true);
                $this->feed($CmsContent->getData());

                return true;

            } else {

                return false;
            }
        }
    }

    /**
     * @param $pageCount
     * @return array|bool
     */
    public function showAll($pageCount = false)
    {
        $sql = 'SELECT C.*,
         (SELECT cc1.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc1 WHERE cc1.type = "HEADER" AND cc1.metaKey = "slug" AND cc1.idCms = C.id AND cc1.lang = :lang) AS slug,
        (SELECT cc2.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc2 WHERE cc2.type = "HEADER" AND cc2.metaKey = "description" AND cc2.idCms = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc3 WHERE cc3.type = "HEADER" AND cc3.metaKey = "name" AND cc3.idCms = C.id AND cc3.lang = :lang) AS name,
        (SELECT cc4.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc4 WHERE cc4.type = "HEADER" AND cc4.metaKey = "menuName" AND cc4.idCms = C.id AND cc4.lang = :lang) AS menuName
        FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C
        WHERE C.statut = :statut ORDER BY C.created_at DESC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            return (!$pageCount) ? $data : $count;
        }
    }

    /**
     * @param $countPage
     *
     * @return array|bool|int
     */
    public function showAllPages($countPage = false)
    {

        $sql = 'SELECT C.*,
         (SELECT cc1.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc1 WHERE cc1.type = "HEADER" AND cc1.metaKey = "slug" AND cc1.idCms = C.id AND cc1.lang = :lang) AS slug,
        (SELECT cc2.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc2 WHERE cc2.type = "HEADER" AND cc2.metaKey = "description" AND cc2.idCms = C.id AND cc2.lang = :lang) AS description,
        (SELECT cc3.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc3 WHERE cc3.type = "HEADER" AND cc3.metaKey = "name" AND cc3.idCms = C.id AND cc3.lang = :lang) AS name,
        (SELECT cc4.metaValue FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content AS cc4 WHERE cc4.type = "HEADER" AND cc4.metaKey = "menuName" AND cc4.idCms = C.id AND cc4.lang = :lang) AS menuName
        FROM ' . TABLEPREFIX . 'appoe_plugin_cms AS C
        WHERE C.type = "PAGE" AND C.statut = :statut ORDER BY C.filename ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            return (!$countPage) ? $data : $count;
        }
    }

    /**
     * @return bool
     */
    public function save()
    {

        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_cms (type, filename, created_at) 
                VALUES (:type, :filename, CURDATE())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->execute();

        $this->id = $this->dbh->lastInsertId();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Creating page -> type: ' . $this->type . ' filename: ' . $this->filename);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function update()
    {

        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_cms SET type = :type, filename = :filename, statut = :statut WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating page -> id: ' . $this->id . ' type: ' . $this->type . ' filename: ' . $this->filename . ' statut: ' . $this->statut);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_cms_menu WHERE idCms = :id;';
        $sql .= 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_cms_content WHERE idCms = :id;';
        $sql .= 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_cms WHERE id = :id;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete page -> id: ' . $this->id);
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

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_plugin_cms WHERE type = :type AND filename = :filename';
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                if ($forUpdate) {
                    $row = $stmt->fetch(PDO::FETCH_OBJ);
                    return $this->id == $row->id;
                }
                return false;
            } else {
                return true;
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
}