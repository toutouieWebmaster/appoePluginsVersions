<?php

namespace App\Plugin\ItemGlue;

use App\DB;
use PDO;

class ArticleRelation
{
    private $id;
    private $type;
    private $typeId;
    private $articleId;

    private $data = null;
    private $dbh = null;

    public function __construct($articleId = null, $type = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($articleId) && !is_null($type)) {
            $this->type = $type;
            $this->articleId = $articleId;
            $this->show();
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
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
     * @return null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param null $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @param mixed $articleId
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations` (
  					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                    `type` VARCHAR(250) NOT NULL,
                    `typeId` INT(11) UNSIGNED NOT NULL,
                    `articleId` INT(11) UNSIGNED NOT NULL,
                    UNIQUE (`type`, `typeId`, `articleId`),
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;ALTER TABLE `' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations` AUTO_INCREMENT = 11;';

        $stmt = DB::exec($sql);
        return (bool)$stmt;
    }

    /**
     * @return array|bool
     */
    public function show()
    {
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations WHERE type = :type AND articleId = :articleId';
        $stmt = DB::exec($sql, [':type' => $this->type, ':articleId' => $this->articleId]);
        $this->data = $stmt ? $stmt->fetchAll(PDO::FETCH_OBJ) : false;
        return $this->data;
    }

    /**
     * @return array|bool
     */
    public function showAll()
    {
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations WHERE articleId = :articleId';
        $stmt = DB::exec($sql, [':articleId' => $this->articleId]);

        return $stmt ? $stmt->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations (type, typeId, articleId) VALUES(:type, :typeId, :articleId)';
        $stmt = DB::exec($sql, [':type' => $this->type, ':typeId' => $this->typeId, ':articleId' => $this->articleId]);

        if ($stmt) {
            appLog('Creating article relation -> type: ' . $this->type . ' typeId:' . $this->typeId . ' articleId:' . $this->articleId);
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations SET type = :type, typeId = :typeId, articleId = :articleId WHERE id = :id';
        $stmt = DB::exec($sql, [':type' => $this->type, ':typeId' => $this->typeId, ':articleId' => $this->articleId, ':id' => $this->id]);

        if ($stmt) {
            appLog('Updating article relation -> id: ' . $this->id . ' type: ' . $this->type . ' typeId:' . $this->typeId . ' articleId:' . $this->articleId);
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles_relations WHERE id = :id';
        $stmt = DB::exec($sql, [':id' => $this->id]);

        if ($stmt) {
            appLog('Deleting article relation -> id: ' . $this->id);
            return true;
        }

        return false;
    }

    /**
     * Feed class attributs
     * @param $data
     */
    public function feed($data)
    {
        if (isset($data)) {
            foreach ($data as $attribut => $value) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

                if (is_callable(array($this, $method))) {
                    $this->$method($value);
                }
            }
        }
    }
}