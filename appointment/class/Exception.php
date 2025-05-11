<?php

namespace App\Plugin\Appointment;

use App\DB;
use PDO;

class Exception
{

    private $tableName = '`' . TABLEPREFIX . 'appoe_plugin_appointment_exceptions`';
    private $id;
    private $idAgenda;
    private $date;
    private $endDate = null;
    private $start;
    private $end;
    private $availability = 'UNAVAILABLE';
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
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @return string
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param string $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
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
                `date` DATE NOT NULL,
                `endDate` DATE NULL DEFAULT NULL,
                `start` SMALLINT UNSIGNED NOT NULL,
                `end` SMALLINT UNSIGNED NOT NULL,
                `availability` VARCHAR(50)  NOT NULL DEFAULT "UNAVAILABLE",
                UNIQUE (`idAgenda`,`date`,`start`,`end`),
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        return DB::exec($sql);
    }

    public function show()
    {
        return DB::show($this);
    }

    public function showByTime()
    {
        return DB::show($this, ['idAgenda', 'date', 'start', 'end']);
    }

    public function showAll()
    {
        return DB::showAll($this, ['idAgenda', 'date']);
    }

    public function showAllFromDateRecurrence()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE idAgenda = :idAgenda 
        AND (date = :date OR (date <= :date AND endDate >= :date)) ORDER BY date ASC';
        $params = [':idAgenda' => $this->idAgenda, ':date' => $this->date];
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    public function showAllFromDate()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE idAgenda = :idAgenda AND date >= :date ORDER BY date ASC';
        $params = [':idAgenda' => $this->idAgenda, ':date' => $this->date];
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
        if (DB::save($this, ['idAgenda', 'date', 'start', 'end', 'availability'])) {
            appLog('Add agenda exception -> idAgenda: ' . $this->idAgenda . ' date:' . $this->date . ' endDate:' . $this->endDate . ' start:' . $this->start . ' end:' . $this->end);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        if (DB::update($this, ['idAgenda', 'date', 'endDate', 'start', 'end', 'availability'], ['id'])) {
            appLog('Update agenda exception -> idAgenda: ' . $this->idAgenda . ' date:' . $this->date . ' endDate:' . $this->endDate . ' start:' . $this->start . ' end:' . $this->end);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return DB::exist($this, ['idAgenda', 'date', 'start', 'end']);
    }

    public function count()
    {
        return DB::count($this, ['idAgenda', 'date']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (DB::delete($this, ['id'])) {
            appLog('Delete exception -> id: ' . $this->id);
            return true;
        }
        return false;
    }
}