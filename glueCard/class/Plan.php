<?php

namespace App\Plugin\GlueCard;

use App\DB;
use PDO;

class Plan
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_glueCard_plans`';
    private $id;
    private $idHandle;
    private $name;
    private $order;
    private $type = 'text';
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
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
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
     * @param null $idPlan
     */
    public function __construct($idPlan = null)
    {
        if (!DB::isTableExist($this->tableName)) {
            $this->createTable();
        }

        if (is_numeric($idPlan)) {
            $this->id = $idPlan;
            $this->show();
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
                `name` VARCHAR(100) NOT NULL,
                UNIQUE (`id_handle`, `name`),
                `order` INT(11) NOT NULL,
                `type` VARCHAR(50) NOT NULL,
                `created_at` DATE NOT NULL,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return (bool)DB::exec($sql);
    }

    /**
     * @return bool
     */
    public function show()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id` = :id';
        $params = array(':id' => $this->id);

        if ($return = DB::exec($sql, $params)) {
            $this->feed($return->fetch(PDO::FETCH_OBJ));
            return true;
        }
        return false;
    }

    /**
     * @return bool|array
     */
    public function showByHandle()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id_handle` = :idHandle ORDER BY `order` ASC';
        $params = array(':idHandle' => $this->idHandle);
        return (DB::exec($sql, $params))->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @return bool
     */
    public function save()
    {
        $newOrder = ($this->getNewOrder() + 1);
        $sql = 'INSERT INTO ' . $this->tableName . ' (`id_handle`, `name`, `type`, `order`, `created_at`) 
        VALUES (:idHandle, :name, :type, :order, NOW())';
        $params = array(':idHandle' => $this->idHandle, ':name' => $this->name, ':type' => $this->type, ':order' => $newOrder);
        if ($return = DB::exec($sql, $params)) {
            appLog('Add plan -> user: ' . getUserLogin() . ' idHandle: ' . $this->idHandle . ' name: ' . $this->name . ' type: ' . $this->type);
            return $return;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET `name` = :name, `type` = :type, `order` = :order WHERE `id` = :id';
        $params = array(':name' => $this->name, ':type' => $this->type, ':order' => $this->order, ':id' => $this->id);
        if (DB::exec($sql, $params)) {
            appLog('Update plan -> user: ' . getUserLogin() . ' name: ' . $this->name . ' type: ' . $this->type . ' order: ' . $this->order);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        $sql = 'SELECT `id` FROM ' . $this->tableName . ' WHERE `id_handle` = :idHandle AND `name` = :name';
        $params = array(':idHandle' => $this->idHandle, ':name' => $this->name);
        return (DB::exec($sql, $params))->fetchColumn();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id` = :id';
        if (DB::exec($sql, [':id' => $this->id])) {
            appLog('Delete plan -> user: ' . getUserLogin() . ' id: ' . $this->id);

            $Content = new Content();
            $Content->setIdPlan($this->id);
            $Content->deleteByPlan();
            return true;
        }
        return false;
    }

    public function getNewOrder()
    {
        $sql = 'SELECT MAX(`order`) as maxOrder FROM ' . $this->tableName . ' WHERE `id_handle` = :idHandle';
        return ((DB::exec($sql, [':idHandle' => $this->idHandle]))->fetch(PDO::FETCH_OBJ))->maxOrder;
    }

    /**
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