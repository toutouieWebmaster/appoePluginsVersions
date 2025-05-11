<?php

namespace App\Plugin\GlueCard;

use App\DB;
use PDO;

class Content
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_glueCard_contents`';
    private $id;
    private $idHandle;
    private $idPlan;
    private $idItem;
    private $text = null;
    private $lang = APP_LANG;
    private $createdAt;
    private $updatedAt;

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
    public function getIdHandle()
    {
        return $this->idHandle;
    }

    /**
     * @param mixed $idHandle
     */
    public function setIdHandle($idHandle)
    {
        $this->idHandle = $idHandle;
    }

    /**
     * @return mixed
     */
    public function getIdPlan()
    {
        return $this->idPlan;
    }

    /**
     * @param mixed $idPlan
     */
    public function setIdPlan($idPlan)
    {
        $this->idPlan = $idPlan;
    }

    /**
     * @return mixed
     */
    public function getIdItem()
    {
        return $this->idItem;
    }

    /**
     * @param mixed $idItem
     */
    public function setIdItem($idItem)
    {
        $this->idItem = $idItem;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed|string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed|string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * constructor.
     */
    public function __construct()
    {
        if (!DB::isTableExist($this->tableName)) {
            $this->createTable();
        }
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `id_handle` INT(11) UNSIGNED NOT NULL,
                `id_plan` INT(11) UNSIGNED NOT NULL,
                `id_item` INT(11) UNSIGNED NOT NULL,
                `text` TEXT NULL DEFAULT NULL,
                `lang` VARCHAR(50) NOT NULL,
                UNIQUE (`id_handle`, `id_plan`, `id_item`, `lang`),
                `created_at` DATE NOT NULL,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return (bool)DB::exec($sql);
    }

    /**
     * @return bool|array
     */
    public function show()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id` = :id';
        $params = array(':id' => $this->id);
        return (DB::exec($sql, $params))->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @return bool|array
     */
    public function showByItem()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id_item` = :idItem AND `lang` = :lang';
        $params = array(':idItem' => $this->idItem, ':lang' => $this->lang);
        return (DB::exec($sql, $params))->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO ' . $this->tableName . ' (`id_handle`, `id_plan`, `id_item`, `text`, `lang`, `created_at`) 
        VALUES (:idHandle, :idPlan, :idItem, :text, :lang, NOW())';
        $params = array(':idHandle' => $this->idHandle, ':idPlan' => $this->idPlan,
            ':idItem' => $this->idItem, ':text' => $this->text, ':lang' => $this->lang);
        if ($return = DB::exec($sql, $params)) {
            appLog('Add content -> user: ' . getUserLogin() . ' idItem: ' . $this->idItem . ' lang: ' . $this->lang);
            return $return;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET `text` = :text WHERE `id_handle` = :idHandle AND `id_plan` = :idPlan AND `id_item` = :idItem AND `lang` = :lang';
        $params = array(':text' => $this->text, ':idHandle' => $this->idHandle, ':idPlan' => $this->idPlan, ':idItem' => $this->idItem, ':lang' => $this->lang);
        if (DB::exec($sql, $params)) {
            appLog('Update content -> user: ' . getUserLogin() . ' idItem: ' . $this->idItem . ' lang: ' . $this->lang);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        $sql = 'SELECT `id` FROM ' . $this->tableName . ' WHERE `id_handle` = :idHandle AND `id_plan` = :idPlan AND `id_item` = :idItem AND `lang` = :lang';
        $params = array(':idHandle' => $this->idHandle, ':idPlan' => $this->idPlan, ':idItem' => $this->idItem, ':lang' => $this->lang);
        return (DB::exec($sql, $params))->fetchColumn();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id` = :id';
        if (DB::exec($sql, [':id' => $this->id])) {
            appLog('Delete content -> user: ' . getUserLogin() . ' id: ' . $this->id);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteByItem()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id_item` = :idItem';
        if (DB::exec($sql, [':idItem' => $this->idItem])) {
            appLog('Delete item content -> user: ' . getUserLogin() . ' idItem: ' . $this->idItem);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteByPlan()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id_plan` = :idPlan';
        if (DB::exec($sql, [':idPlan' => $this->idPlan])) {
            appLog('Delete plan content -> user: ' . getUserLogin() . ' idPlan: ' . $this->idPlan);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteByHandle()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id_handle` = :idHandle';
        if (DB::exec($sql, [':idHandle' => $this->idHandle])) {
            appLog('Delete handle content -> user: ' . getUserLogin() . ' idHandle: ' . $this->idHandle);
            return true;
        }
        return false;
    }
}