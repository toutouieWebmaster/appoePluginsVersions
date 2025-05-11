<?php

namespace App\Plugin\GlueCard;

use App\DB;
use PDO;

class Handle
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_glueCard_handles`';
    private $id;
    private $name;
    private $status = 1;
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
     * constructor.
     * @param null $idHandle
     */
    public function __construct($idHandle = null)
    {
        if (!DB::isTableExist($this->tableName)) {
            $this->createTable();
        }

        if (is_numeric($idHandle)) {
            $this->id = $idHandle;
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
                `name` VARCHAR(100) NOT NULL,
                UNIQUE (`name`),
                `status` BOOLEAN NOT NULL DEFAULT TRUE,
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
    public function showAll()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' ORDER BY status DESC, name ASC';
        return (DB::exec($sql))->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO ' . $this->tableName . ' (`name`, `created_at`) VALUES (:name, NOW())';
        $params = array(':name' => $this->name);
        if ($return = DB::exec($sql, $params)) {
            appLog('Add handle -> user: ' . getUserLogin() . ' name: ' . $this->name);
            return $return;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET `name` = :name, `status` = :status WHERE `id` = :id';
        $params = array(':name' => $this->name, ':status' => $this->status, ':id' => $this->id);
        if (DB::exec($sql, $params)) {
            appLog('Update handle -> user: ' . getUserLogin() . ' name: ' . $this->name . ' status: ' . $this->status);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        $sql = 'SELECT `id` FROM ' . $this->tableName . ' WHERE `name` = :name';
        $params = array(':name' => $this->name);
        return (DB::exec($sql, $params))->fetchColumn();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id` = :id';
        if (DB::exec($sql, [':id' => $this->id])) {
            appLog('Delete handle -> user: ' . getUserLogin() . ' id: ' . $this->id);

            $sql = 'DELETE FROM `' . TABLEPREFIX . 'appoe_plugin_glueCard_plans` WHERE `id_handle` = :idHandle';
            if (DB::exec($sql, [':idHandle' => $this->id])) {
                appLog('Delete all plans -> user: ' . getUserLogin() . ' idHandle: ' . $this->id);

                $Content = new Content();
                $Content->setIdHandle($this->id);
                $Content->deleteByHandle();
            }

            return true;
        }
        return false;
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