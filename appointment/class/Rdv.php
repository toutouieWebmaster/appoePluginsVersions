<?php

namespace App\Plugin\Appointment;

use App\DB;
use PDO;

class Rdv
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_rdv`';
    private $id;
    private $idAgenda;
    private $idClient;
    private $idTypeRdv;
    private $date;
    private $start;
    private $end;
    private $options = null;
    private $status = 1;
    private $createdAt;
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
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * @param mixed $idClient
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;
    }

    /**
     * @return mixed
     */
    public function getIdTypeRdv()
    {
        return $this->idTypeRdv;
    }

    /**
     * @param mixed $idTypeRdv
     */
    public function setIdTypeRdv($idTypeRdv)
    {
        $this->idTypeRdv = $idTypeRdv;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
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


    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `idAgenda` TINYINT UNSIGNED NOT NULL,
                `idClient` INT(11) UNSIGNED NOT NULL,
                `idTypeRdv` TINYINT UNSIGNED NOT NULL,
                `date` DATE NOT NULL,
                `start` SMALLINT UNSIGNED NOT NULL,
                UNIQUE (`idAgenda`,`idClient`,`idTypeRdv`,`date`,`start`),
                `end` SMALLINT UNSIGNED NOT NULL,
                `options` TEXT NULL DEFAULT NULL,
                `status` BOOLEAN NOT NULL DEFAULT TRUE,
                `createdAt` DATETIME NOT NULL,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return DB::exec($sql);
    }

    public function show()
    {
        return DB::show($this);
    }

    public function showByPendingClient()
    {
        return DB::show($this, ['idClient', 'status']);
    }

    public function showAll()
    {
        return DB::showAll($this, ['idAgenda', 'date']);
    }

    public function showPending()
    {
        return DB::showAll($this, ['status']);
    }

    public function showAllByClient()
    {
        return DB::showAll($this, ['idClient'], 'ORDER BY date ASC');
    }

    public function showBetweenDates()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE idAgenda = :idAgenda AND date >= :start AND date <= :end';
        $params = [':idAgenda' => $this->idAgenda, ':start' => $this->start, ':end' => $this->end];
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    public function showAllFromDate()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE date >= :date ORDER BY date ASC';
        $params = [':date' => $this->date];
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (DB::save($this, ['idAgenda', 'idClient', 'idTypeRdv', 'date', 'start', 'end', 'options', 'status', 'createdAt'])) {
            $lastInsertId = DB::lastInsertId();
            $this->setId($lastInsertId);
            appLog('Add Rdv -> idAgenda: ' . $this->idAgenda . ' idClient:' . $this->idClient . ' idTypeRdv:' . $this->idTypeRdv . ' date:' . $this->date);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this, ['idAgenda', 'idClient', 'idTypeRdv', 'date', 'start', 'end', 'options', 'status'], ['id'])) {
            appLog('Update Rdv -> idAgenda: ' . $this->idAgenda . ' idClient:' . $this->idClient . ' idTypeRdv:' . $this->idTypeRdv . ' date:' . $this->date . ' start:' . $this->start . ' end:' . $this->end . ' status:' . $this->status);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['idAgenda', 'idClient', 'idTypeRdv', 'date', 'start']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete Rdv -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}