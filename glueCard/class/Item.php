<?php

namespace App\Plugin\GlueCard;

use App\DB;
use PDO;

class Item
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_glueCard_items`';
    private $id;
    private $idHandle;
    private $order;
    private $status = 1;
    private $createdAt;
    private $updatedAt;
    private $count = null;

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
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @return bool
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param bool $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * constructor.
     * @param null $idItem
     */
    public function __construct($idItem = null)
    {
        if (!DB::isTableExist($this->tableName)) {
            $this->createTable();
        }

        if (is_numeric($idItem)) {
            $this->id = $idItem;
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
                `order` INT(11) NOT NULL,
                `status` BOOLEAN NOT NULL DEFAULT TRUE,
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
        $limit = !is_null($this->count) ? ' LIMIT ' . $this->count : '';
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id_handle` = :idHandle ORDER BY `status` DESC, `order` ASC ' . $limit;
        $params = array(':idHandle' => $this->idHandle);
        return (DB::exec($sql, $params))->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @return bool
     */
    public function save()
    {
        $newOrder = ($this->getNewOrder() + 1);
        $sql = 'INSERT INTO ' . $this->tableName . ' (`id_handle`, `order`, `created_at`) 
        VALUES (:idHandle, :order, NOW())';
        $params = array(':idHandle' => $this->idHandle, ':order' => $newOrder);
        if ($return = DB::exec($sql, $params)) {
            appLog('Add item -> user: ' . getUserLogin() . ' idHandle: ' . $this->idHandle);
            return $return;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET `order` = :order, `status` = :status WHERE `id` = :id';
        $params = array(':order' => $this->order, ':status' => $this->status, ':id' => $this->id);
        if (DB::exec($sql, $params)) {
            appLog('Update item -> user: ' . getUserLogin() . ' id: ' . $this->id . ' order: ' . $this->order . ' status: ' . $this->status);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        $sql = 'SELECT `id` FROM ' . $this->tableName . ' WHERE `id` = :id';
        return (DB::exec($sql, [':id' => $this->id]))->fetchColumn();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id` = :id';
        if (DB::exec($sql, [':id' => $this->id])) {
            appLog('Delete item -> user: ' . getUserLogin() . ' id: ' . $this->id);

            $Content = new Content();
            $Content->setIdItem($this->id);
            $Content->deleteByItem();

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