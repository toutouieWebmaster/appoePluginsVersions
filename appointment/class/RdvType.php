<?php

namespace App\Plugin\Appointment;

use App\DB;

class RdvType
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_rdvtypes`';
    private $id;
    private $idAgenda;
    private $name;
    private $duration;
    private $information = null;
    private $updatedAt;
    private $status = 1;

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
    public function getIdAgenda()
    {
        return $this->idAgenda;
    }

    /**
     * @param mixed $idAgenda
     */
    public function setIdAgenda($idAgenda)
    {
        $this->idAgenda = $idAgenda;
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
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return null
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param null $information
     */
    public function setInformation($information)
    {
        $this->information = $information;
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
                `idAgenda` TINYINT UNSIGNED NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `duration` SMALLINT UNSIGNED NOT NULL,
                UNIQUE (`idAgenda`,`name`,`duration`),
                `information` TEXT NULL DEFAULT NULL,
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
        return DB::showAll($this, ['idAgenda', 'status']);
    }

    public function showAll()
    {
        return DB::showAll($this, ['idAgenda']);
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (DB::save($this, ['idAgenda', 'name', 'duration', 'information'])) {
            appLog('Add RdvType -> idAgenda: ' . $this->idAgenda . ' name:' . $this->name . ' duration:' . $this->duration . ' information:' . $this->information);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this, ['idAgenda', 'name', 'duration', 'information', 'status'], ['id'])) {
            appLog('Update RdvType -> idAgenda: ' . $this->idAgenda . ' name:' . $this->name . ' duration:' . $this->duration . ' information:' . $this->information . ' status:' . $this->status);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['idAgenda', 'name', 'duration']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete RdvType -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}