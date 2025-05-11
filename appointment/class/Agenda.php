<?php

namespace App\Plugin\Appointment;

use App\DB;

class Agenda
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_agendas`';
    private $id;
    private $name;
    private $status = 1;
    private $updatedAt;

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
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
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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

    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `name` VARCHAR(100) NOT NULL,
                UNIQUE (`name`),
                `status` BOOLEAN NOT NULL DEFAULT TRUE,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return DB::exec($sql);
    }

    public function show()
    {
        return DB::show($this);
    }

    public function showByStatus()
    {
        return DB::showAll($this, ['status']);
    }

    public function showAll()
    {
        return DB::showAll($this);
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (DB::save($this, ['name', 'status'])) {
            appLog('Add agenda -> name: ' . $this->name);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this, ['name', 'status'], ['id'])) {
            appLog('Update agenda -> name: ' . $this->name . ' status:' . $this->status);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['name']);
    }

    public function count()
    {
        return DB::count($this, ['status']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete agenda -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}