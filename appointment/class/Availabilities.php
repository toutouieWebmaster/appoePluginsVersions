<?php

namespace App\Plugin\Appointment;

use App\DB;

class Availabilities
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_availabilities`';
    private $id;
    private $idAgenda;
    private $day;
    private $start;
    private $end;
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
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
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
                `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `idAgenda` TINYINT UNSIGNED NOT NULL,
                `day` TINYINT UNSIGNED NOT NULL,
                `start` SMALLINT UNSIGNED NOT NULL,
                `end` SMALLINT UNSIGNED NOT NULL,
                UNIQUE (`idAgenda`,`day`,`start`,`end`),
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return DB::exec($sql);
    }

    public function show()
    {
        return DB::show($this);
    }

    public function showAll()
    {
        return DB::showAll($this, ['idAgenda']);
    }

    public function showAllByDay()
    {
        return DB::showAll($this, ['idAgenda', 'day']);
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (DB::save($this, ['idAgenda', 'day', 'start', 'end'])) {
            appLog('Add agenda availability -> idAgenda: ' . $this->idAgenda . ' day:' . $this->day . ' start:' . $this->start . ' end:' . $this->end);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this, ['idAgenda', 'day', 'start', 'end'], ['id'])) {
            appLog('Update agenda availability -> idAgenda: ' . $this->idAgenda . ' day:' . $this->day . ' start:' . $this->start . ' end:' . $this->end);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['idAgenda', 'day', 'start', 'end']);
    }

    public function count()
    {
        return DB::count($this, ['idAgenda', 'day']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete agenda availability -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}